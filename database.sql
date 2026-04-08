-- GAMEVO Database Schema

-- Buat Database
CREATE DATABASE IF NOT EXISTS gamevo_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamevo_db;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    avatar VARCHAR(255),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Login History
CREATE TABLE IF NOT EXISTS login_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Games
CREATE TABLE IF NOT EXISTS games (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    title VARCHAR(150),
    description TEXT,
    currency VARCHAR(50),
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Game Packages
CREATE TABLE IF NOT EXISTS game_packages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    game_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    amount INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Products (untuk compatibility dengan existing orders)
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    price DECIMAL(10, 2),
    description TEXT,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Orders (Penjualan)
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    total_price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_proof VARCHAR(255),
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_date TIMESTAMP NULL,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Admin Login History
CREATE TABLE IF NOT EXISTS admin_login_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabel Admin Login History
CREATE TABLE IF NOT EXISTS admin_login_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    logout_time TIMESTAMP NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    FOREIGN KEY (admin_id) REFERENCES admin(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create index untuk faster queries
CREATE INDEX idx_username ON users(username);
CREATE INDEX idx_email ON users(email);
CREATE INDEX idx_user_id ON login_history(user_id);
CREATE INDEX idx_admin_username ON admin(username);
CREATE INDEX idx_order_user ON orders(user_id);
CREATE INDEX idx_order_product ON orders(product_id);
CREATE INDEX idx_order_status ON orders(status);
CREATE INDEX idx_order_date ON orders(order_date);
CREATE INDEX idx_game_slug ON games(slug);
CREATE INDEX idx_package_game ON game_packages(game_id);

-- Insert Default Games
INSERT INTO games (slug, name, title, description, currency, image_url) VALUES 
('roblox', 'Roblox', 'Top Up Roblox - Robux', 'Beli Robux untuk Roblox dengan harga terbaik dan terpercaya. Proses instant, tanpa perlu menunggu lama.', 'Robux', 'assets/images/rblx_icon.jpg'),
('mobile-legends', 'Mobile Legends', 'Top Up Mobile Legends - Diamond ML', 'Beli Diamond Mobile Legends dengan harga paling murah se-Indonesia. Garansi uang kembali 100%.', 'Diamond', 'assets/images/ml_icon.jpg'),
('pubg', 'Player Unknown Battlegrounds', 'Top Up PUBG - UC', 'Top Up UC Player Unkown Battlegrounds dengan instant delivery. Payment method lengkap dan aman.', 'UC', 'assets/images/pubg_icon.jpg'),
('genshin-impact', 'Genshin Impact', 'Top Up Genshin Impact - Genesis Crystals', 'Beli Genesis Crystals Genshin Impact dengan cepat dan aman. Tersedia untuk semua server.', 'Genesis Crystals', 'assets/images/gi_icon.jpeg');

-- Insert Default Game Packages untuk Roblox
INSERT INTO game_packages (game_id, name, amount, price) VALUES 
(1, '400 Robux', 400, 35000),
(1, '800 Robux', 800, 68000),
(1, '1700 Robux', 1700, 150000),
(1, '4500 Robux', 4500, 380000);

-- Insert Default Game Packages untuk Mobile Legends
INSERT INTO game_packages (game_id, name, amount, price) VALUES 
(2, '50 Diamond', 50, 9000),
(2, '126 Diamond', 126, 21000),
(2, '259 Diamond', 259, 42000),
(2, '869 Diamond', 869, 138000);

-- Insert Default Game Packages untuk PUBG
INSERT INTO game_packages (game_id, name, amount, price) VALUES 
(3, '50 UC', 50, 10000),
(3, '125 UC', 125, 25000),
(3, '325 UC', 325, 65000),
(3, '1000 UC', 1000, 200000);

-- Insert Default Game Packages untuk Genshin Impact
INSERT INTO game_packages (game_id, name, amount, price) VALUES 
(4, '60 Crystals', 60, 65000),
(4, '330 Crystals', 330, 320000),
(4, '1090 Crystals', 1090, 1030000),
(4, '3080 Crystals', 3080, 2890000);

-- Insert default admin user (password: gamevoadmin, hashed with bcrypt)
INSERT INTO admin (username, password, full_name, email, role) VALUES 
('Admin', '$2y$10$Tz7RZy4l0dW8Jy2KpQxP1.W9v3X5mZ8nB7cD0eF2gH3iJ4kL5mN6', 'Admin GAMEVO', 'admin@gamevo.com', 'super_admin');
