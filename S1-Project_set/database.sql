-- ==========================================================
-- PhonePulse - complete database setup (run once, top to bottom)
-- Creates: tables, roles, 7 users (password 1234), brands, phones,
--          news, sample comments / favourites / contact message
-- WARNING: DROP DATABASE below wipes any existing phonepulse data.
-- ==========================================================
DROP DATABASE IF EXISTS `phonepulse`;
CREATE DATABASE `phonepulse` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `phonepulse`;
 
SET FOREIGN_KEY_CHECKS = 0;
 
-- === Roles ===
CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === Users ===
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  KEY `idx_users_role_id` (`role_id`),
  CONSTRAINT `fk_users_roles` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === Brands ===
CREATE TABLE `brands` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `official_url` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_brands_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === Phones (same brand + name can only exist once) ===
CREATE TABLE `phones` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `spec_highlight` VARCHAR(255) NOT NULL,
  `price_myr` DECIMAL(10,2) DEFAULT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'available',
  `detail_url` VARCHAR(255) DEFAULT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_phones_brand_name` (`brand_id`, `name`),
  KEY `idx_phones_status` (`status`),
  KEY `idx_phones_created_by` (`created_by`),
  CONSTRAINT `fk_phones_brands` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `fk_phones_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === Favorites (removed automatically with the user or phone) ===
CREATE TABLE `favorites` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `phone_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_favorites_user_phone` (`user_id`, `phone_id`),
  KEY `idx_favorites_phone_id` (`phone_id`),
  CONSTRAINT `fk_favorites_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_favorites_phones` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === Phone comments (removed automatically with the phone) ===
CREATE TABLE `phone_comments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone_id` INT UNSIGNED NOT NULL,
  `author_name` VARCHAR(100) NOT NULL,
  `body` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_phone_comments_phone_created` (`phone_id`, `created_at`),
  CONSTRAINT `fk_phone_comments_phones` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === News (keeps the article if its phone or author is deleted) ===
CREATE TABLE `news` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `source` VARCHAR(255) NOT NULL,
  `published_on` DATE DEFAULT NULL,
  `content` TEXT NOT NULL,
  `phone_id` INT UNSIGNED DEFAULT NULL,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_news_phone_id` (`phone_id`),
  KEY `idx_news_created_by` (`created_by`),
  CONSTRAINT `fk_news_phones` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_news_users` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === Contact messages ===
CREATE TABLE `contact_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- === Roles data ===
INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(2, 'salesman'),
(3, 'viewer');
 
-- === Users (password for every account: 1234) ===
INSERT INTO `users` (`id`, `username`, `password_hash`, `role_id`) VALUES
(1, 'admin', '$2y$10$8/urjQuuHGlQ0NBu0VioZewsDwP6US41r2qqY6dwl2.dXYIvOrKMC', 1),
(2, 'Xavier', '$2y$10$z0wpin.PfBfpJ9EtXC.ez.5e7qdBuWoq.n8tCfksKf423JqoTzwPK', 1),
(3, 'Peter', '$2y$10$/sEjT7ONKgkvvJgLs4LuDOYY1YHCUJ2r3AAGUF5V3dVWfX2iA2o3e', 1),
(4, 'Ming', '$2y$10$65S3DS3FlwDBceco.BwHCOBuNOf..OWWH9wqBS2PM9DiMtWOOcmEG', 2),
(5, 'Aina', '$2y$10$OFDN2Nb7IvIHFHAc.LRrBOYN6MjKlUXcl2WnHwUWC5MQ6J4.RhkMK', 3),
(6, 'Daniel', '$2y$10$yWOa4TCSr5XP/qdkoShs.eKApmXqWjiCtO6KVYJchKgs47d59Uhg6', 3),
(7, 'Farid', '$2y$10$8tAXRYJN8VgYOrujBl/zF.aVnXirrpPzMzuRyJXNSw8h50GfBEhHi', 3);
 
-- === Brands ===
INSERT INTO `brands` (`id`, `name`, `official_url`) VALUES
(1, 'Samsung', 'https://www.samsung.com/my/'),
(2, 'iQOO', 'https://www.iqoo.com/'),
(3, 'Red Magic', 'https://redmagic.gg/'),
(4, 'Honor', 'https://www.honor.com/my/'),
(5, 'Xiaomi', 'https://www.mi.com/my/'),
(6, 'Huawei', 'https://consumer.huawei.com/my/'),
(7, 'Apple', 'https://www.apple.com/my/'),
(8, 'Oppo', 'https://www.oppo.com/my/'),
(9, 'Vivo', 'https://www.vivo.com/my/');
 
-- === Phones ===
INSERT INTO `phones` (`id`, `brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`) VALUES
(1, 2, 'iQOO 16', 'Dual flagship camera, super-fast charging', 3699.00, 'available', '', NULL, 1),
(2, 3, 'Red Magic 12 Pro', 'Gaming-focused chipset and cooling system', 4299.00, 'available', '', NULL, 1),
(3, 3, 'Red Magic 12 Pro+', 'Bigger battery and sharper display', 4699.00, 'available', '', NULL, 1),
(4, 4, 'Honor 9 Pro', 'Strong camera value for everyday use', 1899.00, 'available', '', NULL, 1),
(5, 4, 'Honor 9 Pro Max', 'Big display and long battery life', 2399.00, 'available', '', NULL, 1),
(6, 5, 'Xiaomi 18 Pro', 'Premium flagship performance and AI camera', 3899.00, 'available', '', NULL, 1),
(7, 5, 'Xiaomi 18 Pro Max', 'Large-screen flagship with top-end power', 4499.00, 'available', '', NULL, 1),
(8, 5, 'Xiaomi Fold 8 Ultra', 'Foldable flagship with ultra display', 6999.00, 'available', '', NULL, 1),
(9, 1, 'Samsung Fold 8', 'Foldable flagship with premium multitasking', 7999.00, 'available', 'https://www.gsmarena.com/samsung_galaxy_z_fold_wide_5g-14673.php', NULL, 1),
(10, 6, 'Huawei Mate 2', 'Classic Android flagship', 799.00, 'available', 'https://www.gsmarena.com/huawei_ascend_mate2_4g-5949.php', NULL, 1),
(11, 7, 'iPhone 18 Pro', 'Apple flagship design and performance', 4999.00, 'available', 'https://www.gsmarena.com/apple_iphone_18_pro-14934.php', NULL, 1),
(12, 7, 'iPhone 18 Pro Max', 'Large-screen Apple flagship', 5399.00, 'available', 'https://www.gsmarena.com/apple_iphone_18_pro_max-14716.php', NULL, 1),
(13, 7, 'iPhone Duo', 'Dual-screen concept phone', 4299.00, 'unreleased', 'https://www.gsmarena.com/apple_iphone_duo_fold-13804.php', NULL, 1),
(14, 8, 'Oppo X10 Pro', 'Camera-focused premium phone', 2999.00, 'available', '', NULL, 1),
(15, 9, 'Vivo 500 Pro', 'Strong camera package', 2499.00, 'available', '', NULL, 1),
(16, 9, 'Vivo 500 Ultra', 'Premium display and camera setup', 2799.00, 'available', '', NULL, 1),
(17, 5, 'Xiaomi 18 Fold', 'Flagship foldable with premium experience', 6999.00, 'available', 'https://www.gsmarena.com/xiaomi_18_fold_5g-14931.php', NULL, 1),
(18, 5, 'Xiaomi 18 Ultra', 'Top-end performance and camera power', 4999.00, 'available', '', NULL, 1),
(19, 4, 'Honor Magic 9 Pro', 'Premium camera and flagship design', 3699.00, 'available', '', NULL, 1),
(20, 4, 'Honor Magic 9 Pro Max', 'Large premium flagship with long battery', 4299.00, 'available', '', NULL, 1);
 
-- === News ===
INSERT INTO `news` (`id`, `source`, `published_on`, `content`, `phone_id`, `created_by`) VALUES
(1, 'PhonePulse', '2026-09-30', 'New flagship lineup added to the catalog with gaming and foldable highlights.', 1, 1),
(2, 'TechWire', '2026-09-28', 'The latest Xiaomi and Honor models are now available in the catalog.', 6, 1),
(3, 'Notebookcheck', '2026-09-30', 'RedMagic confirmed the RedMagic 12 Pro+ releases on 15 October 2026. It has an active fan with built-in water cooling, an under-display camera and the Snapdragon 8 Elite Extreme Gen 6 chip.', 3, 1),
(4, 'Notebookcheck', '2026-09-15', 'iQOO confirmed the iQOO 16 launches in China on 29 September 2026. It has a 2K 165Hz OLED display, the Snapdragon 8 Elite Gen 6 chip, a dedicated gaming chip and a triple 50MP camera.', 1, 1),
(5, 'Notebookcheck', '2026-08-13', 'Honor set the Magic 9 series launch for 28 September 2026, with Pro and Pro Max models. The leaked specs list a 200MP main camera and a 200MP periscope telephoto, with an Arri-branded camera system.', 19, 1),
(6, 'Notebookcheck', '2026-07-31', 'Certification listings suggest the Xiaomi 18 Pro Max will launch outside China, unlike the Xiaomi 17 Pro Max. The Pro and Pro Max were expected around September 2026, and the base Xiaomi 18 later.', 7, 1),
(7, 'Newsbytes', NULL, 'Samsung announced the Galaxy Z Fold 8 launch event in London on 22 July.', 9, 1);
 
-- === Sample comments ===
INSERT INTO `phone_comments` (`id`, `phone_id`, `author_name`, `body`) VALUES
(1, 1, 'Ali', 'Looks good for a flagship value.'),
(2, 9, 'Naim', 'The foldable design looks impressive.');
 
-- === Sample favorites ===
INSERT INTO `favorites` (`id`, `user_id`, `phone_id`) VALUES
(1, 1, 1),
(2, 1, 9);
 
-- === Sample contact message ===
INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`) VALUES
(1, 'Demo User', 'demo@example.com', 'This is a sample contact message for the new database.');
 
SET FOREIGN_KEY_CHECKS = 1;
 
-- === Quick check ===
SELECT users.id, users.username, roles.name AS role FROM users JOIN roles ON roles.id = users.role_id ORDER BY users.id;