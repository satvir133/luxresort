
CREATE DATABASE IF NOT EXISTS `luxresort`;


USE `luxresort`;

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- 4) Tables (idempotent)

CREATE TABLE IF NOT EXISTS `users` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email`         VARCHAR(190) NOT NULL,
  `phone`         VARCHAR(32),
  `password_hash` VARCHAR(255) NOT NULL,
  `first_name`    VARCHAR(80),
  `last_name`     VARCHAR(80),
  `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_users_email` (`email`),
  KEY `ix_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `roles` (
  `id`     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`   VARCHAR(50) NOT NULL,    -- 'admin' | 'staff' | 'guest'
  `label`  VARCHAR(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` BIGINT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  CONSTRAINT `fk_user_roles_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_roles_role`
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`  VARCHAR(80) NOT NULL,     -- e.g., 'rooms.write'
  `label` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_permissions_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id`       INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  CONSTRAINT `fk_role_permissions_role`
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_permissions_perm`
    FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- (Optional) If you plan DB-stored sessions later:
-- CREATE TABLE IF NOT EXISTS `sessions` (
--   `id` VARCHAR(128) PRIMARY KEY,
--   `user_id` BIGINT UNSIGNED,
--   `payload` BLOB NOT NULL,
--   `last_activity` INT UNSIGNED NOT NULL,
--   KEY `ix_sessions_user` (`user_id`),
--   CONSTRAINT `fk_sessions_user`
--     FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) Seed minimal RBAC (idempotent via INSERT IGNORE)

INSERT IGNORE INTO `roles` (`id`,`name`,`label`) VALUES
  (1,'admin','Administrator'),
  (2,'staff','Hotel Staff'),
  (3,'guest','Guest');

INSERT IGNORE INTO `permissions` (`id`,`code`,`label`) VALUES
  (1,'rooms.read','View rooms'),
  (2,'rooms.write','Manage rooms'),
  (3,'pricing.write','Manage pricing'),
  (4,'bookings.write','Manage bookings'),
  (5,'payments.refund','Issue refunds'),
  (6,'audit.read','View audit logs');

-- Admin gets all permissions
INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`)
SELECT r.id, p.id FROM `roles` r JOIN `permissions` p ON 1=1
WHERE r.name='admin';

-- Staff gets a safe subset
INSERT IGNORE INTO `role_permissions` (`role_id`,`permission_id`)
SELECT r.id, p.id
FROM `roles` r
JOIN `permissions` p ON p.code IN ('rooms.read','rooms.write','pricing.write','bookings.write','payments.refund')
WHERE r.name='staff';



USE luxresort;
CREATE TABLE IF NOT EXISTS leads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);


-- Ensure database exists
CREATE DATABASE IF NOT EXISTS luxresort
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE luxresort;

-- ROOM TYPES (category-level)
CREATE TABLE IF NOT EXISTS room_types (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name            VARCHAR(120) NOT NULL,
  slug            VARCHAR(140) NOT NULL UNIQUE,
  description     TEXT NULL,
  capacity        TINYINT UNSIGNED NOT NULL DEFAULT 2,          -- adults (baseline)
  base_price_cents INT UNSIGNED NOT NULL DEFAULT 0,              -- default nightly price
  amenities_json  JSON NULL,                                     -- optional (WiFi, Pool, etc.)
  photos_json     JSON NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- PHYSICAL ROOMS (each bookable unit)
CREATE TABLE IF NOT EXISTS rooms (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  room_type_id    BIGINT UNSIGNED NOT NULL,
  code            VARCHAR(40) NOT NULL UNIQUE,                   -- e.g., OV-101
  floor_label     VARCHAR(40) NULL,
  status          ENUM('active','maintenance','retired') NOT NULL DEFAULT 'active',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_rooms_type
    FOREIGN KEY (room_type_id) REFERENCES room_types(id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- BOOKINGS (one per user order; can contain multiple rooms)
CREATE TABLE IF NOT EXISTS bookings (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id         BIGINT UNSIGNED NULL,                          -- null for phone/email bookings by staff
  status          ENUM('pending','confirmed','cancelled','checked_in','checked_out','refunded')
                   NOT NULL DEFAULT 'pending',
  total_cents     INT UNSIGNED NOT NULL DEFAULT 0,
  currency        CHAR(3) NOT NULL DEFAULT 'USD',
  notes           TEXT NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_bookings_user (user_id, status)
  -- (Add FK to users if you already have a users table)
) ENGINE=InnoDB;

-- BOOKING ITEMS (each reserved room with date range)
CREATE TABLE IF NOT EXISTS booking_items (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id      BIGINT UNSIGNED NOT NULL,
  room_id         BIGINT UNSIGNED NOT NULL,
  check_in        DATE NOT NULL,
  check_out       DATE NOT NULL,                                  -- exclusive
  price_cents     INT UNSIGNED NOT NULL DEFAULT 0,                -- subtotal for this item
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_bi_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_bi_room
    FOREIGN KEY (room_id) REFERENCES rooms(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CHECK (check_out > check_in),
  INDEX idx_bi_room_dates (room_id, check_in, check_out)
) ENGINE=InnoDB;

-- BOOKING NIGHTS (one row PER NIGHT reserved) → prevents double-booking
CREATE TABLE IF NOT EXISTS booking_nights (
  id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_item_id    BIGINT UNSIGNED NOT NULL,
  room_id            BIGINT UNSIGNED NOT NULL,
  stay_date          DATE NOT NULL,                               -- each night in [check_in, check_out)
  price_cents        INT UNSIGNED NOT NULL DEFAULT 0,
  created_at         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bn_item
    FOREIGN KEY (booking_item_id) REFERENCES booking_items(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_bn_room
    FOREIGN KEY (room_id) REFERENCES rooms(id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  UNIQUE KEY uq_room_date (room_id, stay_date),                   -- hard guarantee: no two bookings same room/night
  INDEX idx_bn_room (room_id)
) ENGINE=InnoDB;

-- OPTIONAL: PAYMENTS
CREATE TABLE IF NOT EXISTS payments (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id      BIGINT UNSIGNED NOT NULL,
  provider        ENUM('stripe','paypal','manual') NOT NULL DEFAULT 'manual',
  provider_ref    VARCHAR(120) NULL,
  amount_cents    INT UNSIGNED NOT NULL DEFAULT 0,
  status          ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pay_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  INDEX idx_pay_booking (booking_id, status)
) ENGINE=InnoDB;

-- OPTIONAL: GUEST SNAPSHOT (kept with booking)
CREATE TABLE IF NOT EXISTS booking_guests (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id      BIGINT UNSIGNED NOT NULL,
  first_name      VARCHAR(80) NOT NULL,
  last_name       VARCHAR(80) NOT NULL,
  email           VARCHAR(190) NOT NULL,
  phone           VARCHAR(40) NULL,
  created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bg_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
    ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;



USE luxresort;

-- Room Types
INSERT INTO room_types (name, slug, description, capacity, base_price_cents, amenities_json, photos_json)
VALUES
('Ocean Villa', 'ocean-villa', 'Private plunge pool, ocean view, butler-on-call', 2, 45000,
 JSON_ARRAY('Ocean view','Private pool','Butler'), NULL),
('Family Suite', 'family-suite', 'Spacious suite perfect for families', 4, 32000,
 JSON_ARRAY('Two bedrooms','Living area','Kids amenities'), NULL);

-- Physical Rooms
INSERT INTO rooms (room_type_id, code, floor_label, status)
VALUES
( (SELECT id FROM room_types WHERE slug='ocean-villa'), 'OV-101', 'Beachfront', 'active'),
( (SELECT id FROM room_types WHERE slug='ocean-villa'), 'OV-102', 'Beachfront', 'active'),
( (SELECT id FROM room_types WHERE slug='family-suite'), 'FS-201', 'Garden Wing', 'active');
