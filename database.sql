CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    steamid BIGINT UNSIGNED NOT NULL,
    personaname VARCHAR(255) NOT NULL,
    avatarhash VARCHAR(255) NOT NULL,
    steam_trade_url VARCHAR(255),
    name VARCHAR(255),
    email VARCHAR(255),
    verified_email TINYINT(1) NOT NULL,
    phone VARCHAR(255),
    created_date DATE NOT NULL
);

CREATE TABLE IF NOT EXISTS login_log (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    login_date DATETIME NOT NULL,
    user_ip VARCHAR(255) NOT NUll
);

CREATE TABLE IF NOT EXISTS purchase (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    payment_method VARCHAR(255),
    payment_method VARCHAR(255),
    payment_wallet TINYINT(1),
    status enum('pending', 'approved', 'complete', 'canceled') NOT NULL,
    coupon VARCHAR(255) NOT NULL,
    subtotal DECIMAL(8,2) NOT NULL,
    discount DECIMAL(8,2) NOT NULL,
    total DECIMAL(8,2) NOT NULL,
    created_date DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS purchase_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    trading_id INT UNSIGNED NOT NULL,
    status enum('pending', 'trading', 'canceled') NOT NULL,
    item_name TEXT NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    offer_price DECIMAL(8,2)
);

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cs_item_variant_id INT UNSIGNED NOT NULL,
    steam_asset BIGINT UNSIGNED,
    pattern_float DOUBLE,
    availability TINYINT NOT NULL,
    base_price_percentage DECIMAL(5,2),
    price DECIMAL(8,2),
    offer_percentage DECIMAL(5,2),
    updated_date DATE
);

CREATE TABLE IF NOT EXISTS cs_item_variant (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cs_item_id INT UNSIGNED NOT NULL,
    market_hash_name VARCHAR(255) NOT NULL,
    category ENUM('normal', 'tournament', 'strange', 'unusual', 'unusual_strange'),
    exterior ENUM('fn', 'mw', 'ft', 'ww', 'bs')
);

--"market_hash_name": "StatTrak™ AK-47 | Uncharted (Minimal Wear)"
--"market_hash_name": "Special Agent Ava | FBI"
-- category: Normal, Souvenir, StatTrak™, ★, ★ StatTrak™
-- category: normal, tournament, strange, unusual, unusual_strange

CREATE TABLE IF NOT EXISTS cs_item (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    type_br VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    name_br VARCHAR(255) NOT NULL,
    family VARCHAR(255) NOT NULL,
    family_br VARCHAR(255) NOT NULL,
    collection_id SMALLINT UNSIGNED NOT NULL,
    rarity VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
);

-- type: Agent, Machinegun, Pistol, Rifle, Shotgun, SMG, Sniper Rifle
-- rarity: Consumer Grade, Industrial Grade, Mil-Spec, Restricted, Classified, Covert, Contraband, Distinguished, Exceptional, Superior, Master

CREATE TABLE IF NOT EXISTS cs_collections (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_br VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS coupon (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    percent TINYINT(2) NOT NULL,
    user_id INT UNSIGNED,
    min_value DECIMAL(8,2),
    expiration_date DATETIME NOT NULL
);

/*
CREATE TABLE IF NOT EXISTS coupon (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(255) NOT NULL,
    type ENUM('percentage', 'money') NOT NULL,
    value DECIMAL(2,2),
    user_id INT UNSIGNED,
    limited INT UNSIGNED,
    count INT UNSIGNED NOT NULL,
    expiration_date DATETIME NOT NULL
);
*/

CREATE TABLE IF NOT EXISTS mercadopago (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    data_id INT UNSIGNED NOT NULL,
    ts INT UNSIGNED NOT NULL,
    hash VARCHAR(64) NOT NULL
);

CREATE TABLE IF NOT EXISTS ticket (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    ticket VARCHAR(20) NOT NULL,
    subject VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS ticket_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT UNSIGNED NOT NULL,
    admin TINYINT(1) UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    created_date DATETIME NOT NULL
);

CREATE TABLE `wallet` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `balance` decimal(8,2) NOT NULL,
  `pending` decimal(8,2) NOT NULL
);

CREATE TABLE `wallet_historic` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `value` decimal(8,2) NOT NULL,
  `status` enum('credit','debit') NOT NULL,
  `created_date` datetime NOT NULL
);

CREATE TABLE IF NOT EXISTS trading (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    steam_asset BIGINT UNSIGNED NOT NULL,
    tradeofferid BIGINT UNSIGNED,
    status ENUM('pending', 'sent') NOT NULL,
    created_date DATETIME NOT NULL
);