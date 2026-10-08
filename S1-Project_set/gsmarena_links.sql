UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = 'https://www.gsmarena.com/huawei_ascend_mate2_4g-5949.php'
WHERE brands.name = 'Huawei' AND phones.name = 'Huawei Mate 2';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = 'https://www.gsmarena.com/apple_iphone_18_pro-14934.php'
WHERE brands.name = 'Apple' AND phones.name = 'iPhone 18 Pro';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = 'https://www.gsmarena.com/apple_iphone_18_pro_max-14716.php'
WHERE brands.name = 'Apple' AND phones.name = 'iPhone 18 Pro Max';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = 'https://www.gsmarena.com/apple_iphone_duo_fold-13804.php'
WHERE brands.name = 'Apple' AND phones.name = 'iPhone Duo';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = ''
WHERE brands.name = 'Oppo' AND phones.name = 'Oppo X10 Pro';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = 'https://www.gsmarena.com/samsung_galaxy_z_fold_wide_5g-14673.php'
WHERE brands.name = 'Samsung' AND phones.name = 'Samsung Fold8';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = ''
WHERE brands.name = 'Vivo' AND phones.name IN ('Vivo 500 Pro', 'Vivo 500 Ultra');

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = 'https://www.gsmarena.com/xiaomi_18_fold_5g-14931.php'
WHERE brands.name = 'Xiaomi' AND phones.name = 'Xiaomi 18 Fold';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = ''
WHERE brands.name = 'Xiaomi' AND phones.name = 'Xiaomi 18 Ultra';

UPDATE phones
JOIN brands ON brands.id = phones.brand_id
SET phones.detail_url = ''
WHERE brands.name = 'Honor'
  AND phones.name IN ('Honor Magic 9 Pro', 'Honor Magic 9 Pro Max');

