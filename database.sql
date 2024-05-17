CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    steamid BIGINT UNSIGNED NOT NULL,
    steam_trade_url VARCHAR(255),
    name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(255),
    nickname VARCHAR(255),
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
    pay_method VARCHAR(255) NOT NULL,
    status VARCHAR(255) NOT NULL,
    coupon VARCHAR(255) NOT NULL,
    subtotal DECIMAL(8,2) NOT NULL,
    discount DECIMAL(8,2) NOT NULL,
    total DECIMAL(8,2) NOT NULL,
    created_date DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS purchase_items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT UNSIGNED NOT NULL,
    item_id INT UNSIGNED NOT NULL,
    item_name TEXT NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    offer_price DECIMAL(8,2)
);

CREATE TABLE IF NOT EXISTS items (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type_name ENUM('agent', 'weapon') NOT NULL,
    type_id INT UNSIGNED NOT NULL,
    market_hash_name VARCHAR(255) NOT NULL,
    availability TINYINT NOT NULL,
    price DECIMAL(8,2),
    offer_price DECIMAL(8,2)
);

CREATE TABLE IF NOT EXISTS agents (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    agent_type VARCHAR(255) NOT NULL,
    agent_type_br VARCHAR(255) NOT NULL,
    agent_name VARCHAR(255) NOT NULL,
    agent_name_br VARCHAR(255) NOT NULL,
    agent_family VARCHAR(255) NOT NULL,
    agent_family_br VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
);

--"market_hash_name": "StatTrak™ AK-47 | Uncharted (Minimal Wear)"
--"market_hash_name": "Special Agent Ava | FBI"

CREATE TABLE IF NOT EXISTS weapons_attributes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    weapon_id INT UNSIGNED NOT NULL,
    weapon_stattrak TINYINT(1) NOT NULL,    
    weapon_exterior ENUM('fn', 'mw', 'ft', 'ww', 'bs') NOT NULL
);

CREATE TABLE IF NOT EXISTS weapons (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    weapon_collection_id INT UNSIGNED NOT NULL,
    weapon_type VARCHAR(255) NOT NULL,
    weapon_type_br VARCHAR(255) NOT NULL,
    weapon_rarity TINYINT UNSIGNED NOT NULL,
    weapon_name VARCHAR(255) NOT NULL,
    weapon_name_br VARCHAR(255) NOT NULL,
    weapon_family VARCHAR(255) NOT NULL,
    weapon_family_br VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS collections (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    name_br VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS coupon (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    percent TINYINT(2) NOT NULL,
    user_id INT UNSIGNED,
    expiration_date DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS mercadopago (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
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