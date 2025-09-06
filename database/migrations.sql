
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
