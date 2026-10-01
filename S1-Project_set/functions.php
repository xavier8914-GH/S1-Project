<?php
 
require_once __DIR__ . '/db.php';
 
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// === Phone functions ===
function get_phones(string $search = ''): array
{
    $sql = 'SELECT phones.*, brands.name AS brand_name, brands.official_url
            FROM phones
            JOIN brands ON brands.id = phones.brand_id';
    $params = [];
 
    if ($search !== '') {
        $sql .= ' WHERE phones.name LIKE :name_search OR brands.name LIKE :brand_search';
        $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $search);
        $params['name_search'] = "%$escaped%";
        $params['brand_search'] = "%$escaped%";
    }
 
    $sql .= ' ORDER BY phones.status ASC, phones.name ASC';
 
    $stmt = get_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}
 
function get_phone(int $id): ?array
{
    $stmt = get_db()->prepare(
        'SELECT phones.*, brands.name AS brand_name
         FROM phones JOIN brands ON brands.id = phones.brand_id
         WHERE phones.id = :id'
    );
    $stmt->execute(['id' => $id]);
    $phone = $stmt->fetch();
    return $phone ?: null;
}
 
function get_brands(): array
{
    return get_db()->query('SELECT * FROM brands ORDER BY name')->fetchAll();
}

function add_brand(string $name, ?string $officialUrl): void
{
    get_db()->prepare(
        'INSERT INTO brands (name, official_url) VALUES (:name, :official_url)'
    )->execute([
        'name' => $name,
        'official_url' => $officialUrl,
    ]);
}

function get_gsmarena_url(array $phone): string
{
    $detailUrl = $phone['detail_url'] ?? '';
    if (is_string($detailUrl)) {
        $url = parse_url($detailUrl);
        if (is_array($url)
            && ($url['scheme'] ?? '') === 'https'
            && in_array(strtolower($url['host'] ?? ''), ['gsmarena.com', 'www.gsmarena.com'], true)
            && preg_match('/-[0-9]+\.php$/', $url['path'] ?? '') === 1) {
            return $detailUrl;
        }
    }

    $brand = trim((string) ($phone['brand_name'] ?? ''));
    $model = trim((string) ($phone['name'] ?? ''));
    if ($brand !== '' && strncasecmp($model, $brand, strlen($brand)) === 0) {
        $model = trim(substr($model, strlen($brand)));
    }

    return 'https://www.gsmarena.com/results.php3?sQuickSearch=' . rawurlencode(trim($brand . ' ' . $model));
}

function has_gsmarena_detail_url(array $phone): bool
{
    $detailUrl = $phone['detail_url'] ?? '';
    if (!is_string($detailUrl)) {
        return false;
    }

    $url = parse_url($detailUrl);
    return is_array($url)
        && ($url['scheme'] ?? '') === 'https'
        && in_array(strtolower($url['host'] ?? ''), ['gsmarena.com', 'www.gsmarena.com'], true)
        && preg_match('/-[0-9]+\.php$/', $url['path'] ?? '') === 1;
}

/**
 * $data expects: name, brand_id, spec_highlight, price_myr, status, detail_url
 */
function save_phone(array $data, ?int $id, int $createdBy): void
{
    $params = [
        'name'       => $data['name'],
        'brand_id'   => $data['brand_id'],
        'spec'       => $data['spec_highlight'],
        'price'      => $data['price_myr'],
        'status'     => $data['status'],
        'detail_url' => $data['detail_url'],
        'image_path' => $data['image_path'] ?? null,
    ];
 
    if ($id) {
        $stmt = get_db()->prepare(
            'UPDATE phones SET name = :name, brand_id = :brand_id, spec_highlight = :spec,
             price_myr = :price, status = :status, detail_url = :detail_url,
             image_path = :image_path WHERE id = :id'
        );
        $params['id'] = $id;
    }
    else {
        $stmt = get_db()->prepare(
            'INSERT INTO phones (name, brand_id, spec_highlight, price_myr, status, detail_url, image_path, created_by)
             VALUES (:name, :brand_id, :spec, :price, :status, :detail_url, :image_path, :created_by)'
        );
        $params['created_by'] = $createdBy;
    }
    $stmt->execute($params);
}
 
function delete_phone(int $id): void
{
    get_db()->prepare('DELETE FROM phones WHERE id = :id')->execute(['id' => $id]);
}

// === Favourites ===
function get_favorite_ids(int $userId): array
{
    $stmt = get_db()->prepare('SELECT phone_id FROM favorites WHERE user_id = :user_id');
    $stmt->execute(['user_id' => $userId]);
    return array_column($stmt->fetchAll(), 'phone_id');
}
 
function toggle_favorite(int $userId, int $phoneId): void
{
    // Try delete first; if nothing was deleted, it wasn't favorited, so insert.
    // Requires a UNIQUE(user_id, phone_id) constraint to be fully race-safe.
    $stmt = get_db()->prepare('DELETE FROM favorites WHERE user_id = :u AND phone_id = :p');
    $stmt->execute(['u' => $userId, 'p' => $phoneId]);
 
    if ($stmt->rowCount() === 0) {
        try {
            get_db()->prepare('INSERT INTO favorites (user_id, phone_id) VALUES (:u, :p)')
                ->execute(['u' => $userId, 'p' => $phoneId]);
        } catch (\PDOException $e) {
            // Ignore duplicate-insert race if unique constraint exists; otherwise rethrow.
            if (!str_contains($e->getMessage(), 'Duplicate') && !str_contains($e->getMessage(), 'UNIQUE')) {
                throw $e;
            }
        }
    }
}

// === Comments ===
function get_phone_comments(array $phoneIds): array
{
    $phoneIds = array_values(array_unique(array_filter(
        array_map('intval', $phoneIds),
        static fn (int $id): bool => $id > 0
    )));
    if ($phoneIds === []) {
        return [];
    }

    $placeholders = [];
    $params = [];
    foreach ($phoneIds as $index => $phoneId) {
        $parameter = 'phone_' . $index;
        $placeholders[] = ':' . $parameter;
        $params[$parameter] = $phoneId;
    }

    $stmt = get_db()->prepare(
        'SELECT phone_id, author_name, body, created_at
         FROM phone_comments
         WHERE phone_id IN (' . implode(', ', $placeholders) . ')
         ORDER BY created_at DESC'
    );
    $stmt->execute($params);

    $commentsByPhone = [];
    foreach ($stmt->fetchAll() as $comment) {
        $commentsByPhone[$comment['phone_id']][] = $comment;
    }

    return $commentsByPhone;
}

function get_phone_comment_counts(array $phoneIds): array
{
    $phoneIds = array_values(array_unique(array_filter(
        array_map('intval', $phoneIds),
        static fn (int $id): bool => $id > 0
    )));
    if ($phoneIds === []) {
        return [];
    }

    $placeholders = [];
    $params = [];
    foreach ($phoneIds as $index => $phoneId) {
        $parameter = 'phone_' . $index;
        $placeholders[] = ':' . $parameter;
        $params[$parameter] = $phoneId;
    }

    $stmt = get_db()->prepare(
        'SELECT phone_id, COUNT(*) AS comment_count
         FROM phone_comments
         WHERE phone_id IN (' . implode(', ', $placeholders) . ')
         GROUP BY phone_id'
    );
    $stmt->execute($params);

    $counts = [];
    foreach ($stmt->fetchAll() as $row) {
        $counts[$row['phone_id']] = (int) $row['comment_count'];
    }

    return $counts;
}

function add_phone_comment(int $phoneId, string $authorName, string $body): void
{
    get_db()->prepare(
        'INSERT INTO phone_comments (phone_id, author_name, body)
         VALUES (:phone_id, :author_name, :body)'
    )->execute([
        'phone_id' => $phoneId,
        'author_name' => $authorName,
        'body' => $body,
    ]);
}

// ---- News ---------------------------------------------------------------
 
function get_news(): array
{
    return get_db()->query(
        'SELECT news.*, phones.name AS phone_name
         FROM news LEFT JOIN phones ON phones.id = news.phone_id
         ORDER BY news.published_on IS NULL, news.published_on DESC'
    )->fetchAll();
}
 
function save_news(array $data, ?int $id, int $createdBy): void
{
    $params = [
        'source'       => $data['source'],
        'published_on' => $data['published_on'],
        'content'      => $data['content'],
        'phone_id'     => $data['phone_id'],
    ];
 
    if ($id) {
        $stmt = get_db()->prepare(
            'UPDATE news SET source = :source, published_on = :published_on,
             content = :content, phone_id = :phone_id WHERE id = :id'
        );
        $params['id'] = $id;
    }
    else {
        $stmt = get_db()->prepare(
            'INSERT INTO news (source, published_on, content, phone_id, created_by)
             VALUES (:source, :published_on, :content, :phone_id, :created_by)'
        );
        $params['created_by'] = $createdBy;
    }
    $stmt->execute($params);
}
 
function delete_news(int $id): void
{
    get_db()->prepare('DELETE FROM news WHERE id = :id')->execute(['id' => $id]);
}
 
//Users / roles
 
function find_user_by_username(string $username): ?array
{
    $stmt = get_db()->prepare(
        'SELECT users.*, roles.name AS role
         FROM users JOIN roles ON roles.id = users.role_id
         WHERE username = :username'
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    return $user ?: null;
}
 
function count_users(): int
{
    return (int) get_db()->query('SELECT COUNT(*) AS n FROM users')->fetch()['n'];
}
 
function create_user(string $username, string $password, string $roleName): void
{
    $roleStmt = get_db()->prepare('SELECT id FROM roles WHERE name = :name');
    $roleStmt->execute(['name' => $roleName]);
    $role = $roleStmt->fetch();
 
    if (!$role) {
        throw new InvalidArgumentException("Unknown role: $roleName");
    }
 
    $stmt = get_db()->prepare(
        'INSERT INTO users (username, password_hash, role_id) VALUES (:username, :hash, :role_id)'
    );
    $stmt->execute([
        'username' => $username,
        'hash'     => password_hash($password, PASSWORD_DEFAULT),
        'role_id'  => $role['id'],
    ]);
}
 
function get_users(): array
{
    return get_db()->query(
        'SELECT users.id, users.username, users.created_at, roles.name AS role
         FROM users JOIN roles ON roles.id = users.role_id ORDER BY users.username'
    )->fetchAll();
}
 
function get_roles(): array
{
    return get_db()->query('SELECT * FROM roles ORDER BY id')->fetchAll();
}
 
function update_user_role(int $userId, int $roleId): void
{
    get_db()->prepare('UPDATE users SET role_id = :role_id WHERE id = :id')
        ->execute(['role_id' => $roleId, 'id' => $userId]);
}
 
function delete_user(int $userId): void
{
    get_db()->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $userId]);
}
 
//Dashboard stats
 
function get_stats(): array
{
    $db = get_db();
    return [
        'phones'      => (int) $db->query('SELECT COUNT(*) AS n FROM phones')->fetch()['n'],
        'unreleased'  => (int) $db->query("SELECT COUNT(*) AS n FROM phones WHERE status = 'unreleased'")->fetch()['n'],
        'users'       => (int) $db->query('SELECT COUNT(*) AS n FROM users')->fetch()['n'],
        'favorites'   => (int) $db->query('SELECT COUNT(*) AS n FROM favorites')->fetch()['n'],
        'news'        => (int) $db->query('SELECT COUNT(*) AS n FROM news')->fetch()['n'],
    ];
}

function add_contact_message(string $name, string $email, string $message): void
{
    get_db()->prepare(
        'INSERT INTO contact_messages (name, email, message)
         VALUES (:name, :email, :message)'
    )->execute([
        'name' => $name,
        'email' => $email,
        'message' => $message,
    ]);
}

function get_contact_messages(): array
{
    return get_db()->query(
        'SELECT id, name, email, message, created_at
         FROM contact_messages
         ORDER BY created_at DESC'
    )->fetchAll();
}