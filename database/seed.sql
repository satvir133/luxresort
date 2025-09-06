USE luxresort;

-- Admin user (already seeded, but repeat here in case you want to reset)
INSERT INTO users (id,email,password_hash,first_name,last_name)
VALUES (1,'admin@demo.test', 
        '$2y$10$T1qLJwD4vKx7v0QWhq9A5e4AZd5cV9cW0f7oQm5gHfQK9y5yWwI1u',
        'Demo','Admin')
ON DUPLICATE KEY UPDATE email=email;

INSERT IGNORE INTO user_roles(user_id,role_id) 
SELECT 1, id FROM roles WHERE name='admin';

-- Staff user
INSERT INTO users (id,email,password_hash,first_name,last_name)
VALUES (2,'staff@demo.test', 
        '$2y$10$T1qLJwD4vKx7v0QWhq9A5e4AZd5cV9cW0f7oQm5gHfQK9y5yWwI1u',
        'Demo','Staff')
ON DUPLICATE KEY UPDATE email=email;

INSERT IGNORE INTO user_roles(user_id,role_id) 
SELECT 2, id FROM roles WHERE name='staff';

-- Guest user
INSERT INTO users (id,email,password_hash,first_name,last_name)
VALUES (3,'guest@demo.test', 
        '$2y$10$T1qLJwD4vKx7v0QWhq9A5e4AZd5cV9cW0f7oQm5gHfQK9y5yWwI1u',
        'Demo','Guest')
ON DUPLICATE KEY UPDATE email=email;

INSERT IGNORE INTO user_roles(user_id,role_id) 
SELECT 3, id FROM roles WHERE name='guest';
