-- finish setup
-- === Safe database update: keep existing database and add new phones ===
USE `phonepulse`;

-- === Remove duplicate phone rows before updating ===
DELETE p1
FROM `phones` p1
INNER JOIN `phones` p2
  ON p1.id > p2.id
 AND p1.brand_id = p2.brand_id
 AND p1.name = p2.name;

-- Prevent future duplicates for the same phone name under the same brand
ALTER TABLE `phones`
  ADD UNIQUE KEY `uq_phones_brand_name` (`brand_id`, `name`);

-- === Add missing brands ===
INSERT INTO `brands` (`name`, `official_url`)
VALUES
('Huawei', 'https://consumer.huawei.com/my/'),
('Apple', 'https://www.apple.com/my/'),
('Oppo', 'https://www.oppo.com/my/'),
('Vivo', 'https://www.vivo.com/my/'),
('Samsung', 'https://www.samsung.com/my/'),
('iQOO', 'https://www.iqoo.com/'),
('Red Magic', 'https://redmagic.gg/'),
('Honor', 'https://www.honor.com/my/'),
('Xiaomi', 'https://www.mi.com/my/')
ON DUPLICATE KEY UPDATE `official_url` = VALUES(`official_url`);

-- === Add old phone list back ===
INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Huawei Mate 2', 'Classic Android flagship', 799.00, 'available', 'https://www.gsmarena.com/huawei_ascend_mate2_4g-5949.php', NULL, 1
FROM `brands` b WHERE b.name = 'Huawei'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'iPhone 18 Pro', 'Apple flagship design and performance', 4999.00, 'available', 'https://www.gsmarena.com/apple_iphone_18_pro-14934.php', NULL, 1
FROM `brands` b WHERE b.name = 'Apple'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'iPhone 18 Pro Max', 'Large-screen Apple flagship', 5399.00, 'available', 'https://www.gsmarena.com/apple_iphone_18_pro_max-14716.php', NULL, 1
FROM `brands` b WHERE b.name = 'Apple'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'iPhone Duo', 'Dual-screen concept phone', 4299.00, 'unreleased', 'https://www.gsmarena.com/apple_iphone_duo_fold-13804.php', NULL, 1
FROM `brands` b WHERE b.name = 'Apple'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Oppo X10 Pro', 'Camera-focused premium phone', 2999.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Oppo'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Samsung Fold 8', 'Foldable flagship with premium multitasking', 7999.00, 'available', 'https://www.gsmarena.com/samsung_galaxy_z_fold_wide_5g-14673.php', NULL, 1
FROM `brands` b WHERE b.name = 'Samsung'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Vivo 500 Pro', 'Strong camera package', 2499.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Vivo'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Vivo 500 Ultra', 'Premium display and camera setup', 2799.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Vivo'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Xiaomi 18 Fold', 'Flagship foldable with premium experience', 6999.00, 'available', 'https://www.gsmarena.com/xiaomi_18_fold_5g-14931.php', NULL, 1
FROM `brands` b WHERE b.name = 'Xiaomi'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Xiaomi 18 Ultra', 'Top-end performance and camera power', 4999.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Xiaomi'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Honor Magic 9 Pro', 'Premium camera and flagship design', 3699.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Honor'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Honor Magic 9 Pro Max', 'Large premium flagship with long battery', 4299.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Honor'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

-- === Add your new phone list ===
INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'iQOO 16', 'Dual flagship camera, super-fast charging', 3699.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'iQOO'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Red Magic 12 Pro', 'Gaming-focused chipset and cooling system', 4299.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Red Magic'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Red Magic 12 Pro+', 'Bigger battery and sharper display', 4699.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Red Magic'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Honor 9 Pro', 'Strong camera value for everyday use', 1899.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Honor'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Honor 9 Pro Max', 'Big display and long battery life', 2399.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Honor'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Xiaomi 18 Pro', 'Premium flagship performance and AI camera', 3899.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Xiaomi'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Xiaomi 18 Pro Max', 'Large-screen flagship with top-end power', 4499.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Xiaomi'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Xiaomi Fold 8 Ultra', 'Foldable flagship with ultra display', 6999.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Xiaomi'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

INSERT INTO `phones` (`brand_id`, `name`, `spec_highlight`, `price_myr`, `status`, `detail_url`, `image_path`, `created_by`)
SELECT b.id, 'Samsung Fold 8', 'Foldable flagship with premium multitasking', 7999.00, 'available', '', NULL, 1
FROM `brands` b WHERE b.name = 'Samsung'
ON DUPLICATE KEY UPDATE `spec_highlight` = VALUES(`spec_highlight`);

-- === End ===
