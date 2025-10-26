-- ⚠️ SECURITY WARNING: Database Initialization Script
-- This file is executed ONCE when MySQL container is first created.
--
-- 🔐 PASSWORD SECURITY:
-- - The sample data below uses placeholder text 'hashedpassword'
-- - These are NOT secure and should NEVER be used in production
-- - Replace with real bcrypt hashes generated via:
--     make bcrypt PASSWORD='your-secure-password'
--   or
--     docker run --rm -v "$PWD/scripts":/scripts php:8.2-cli php /scripts/bcrypt.php 'your-secure-password'
-- - Paste the resulting $2y$10$... value into INSERT statements
--
-- 🛡️ PRODUCTION CHECKLIST:
-- - [ ] Replace all placeholder passwords with real bcrypt hashes
-- - [ ] Remove or modify sample user accounts
-- - [ ] Use strong, unique passwords (16+ characters)
-- - [ ] Consider using database migration tools instead of init.sql
-- - [ ] Never commit real credentials to version control
--
-- Create tables
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    status ENUM('draft','published','archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ⚠️ SAMPLE DATA BELOW - FOR DEVELOPMENT/TESTING ONLY
-- These are placeholder values and NOT suitable for production use
-- Replace 'hashedpassword' with real bcrypt hashes before deploying
INSERT INTO users (username, email, password_hash) VALUES
('admin', 'admin@example.com', 'hashedpassword'),  -- TODO: Replace with bcrypt hash
('john_doe', 'john@example.com', 'hashedpassword');  -- TODO: Replace with bcrypt hash

INSERT INTO posts (user_id, title, content, status) VALUES
(1, 'Welcome', 'This is a test post from the LEMP stack.', 'published');