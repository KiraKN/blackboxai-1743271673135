<?php
require_once __DIR__ . '/../config/database.php';

function initializeDatabase() {
    $db = getDBConnection();
    
    // Create users table
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            name TEXT NOT NULL,
            phone TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    // Create auth_tokens table
    $db->exec("
        CREATE TABLE IF NOT EXISTS auth_tokens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            token TEXT NOT NULL UNIQUE,
            expires_at TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");
    
    // Create products table
    $db->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            category TEXT,
            image_path TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    // Create orders table
    $db->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            status TEXT CHECK(status IN ('pending', 'processing', 'completed', 'cancelled')) DEFAULT 'pending',
            total REAL NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
    ");
    
    // Create order_items table
    $db->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            price REAL NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id)
        );
    ");
    
    // Create admin_users table
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            permissions TEXT CHECK(permissions IN ('admin', 'super_admin')) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Create plants table
    $db->exec("
        CREATE TABLE IF NOT EXISTS plants (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            scientific_name TEXT,
            description TEXT,
            care_instructions TEXT,
            water_needs TEXT,
            light_requirements TEXT,
            growth_rate TEXT,
            toxicity TEXT,
            image_path TEXT,
            is_featured BOOLEAN DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Create plant_categories table
    $db->exec("
        CREATE TABLE IF NOT EXISTS plant_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");

    // Create plant_category_map table
    $db->exec("
        CREATE TABLE IF NOT EXISTS plant_category_map (
            plant_id INTEGER NOT NULL,
            category_id INTEGER NOT NULL,
            PRIMARY KEY (plant_id, category_id),
            FOREIGN KEY (plant_id) REFERENCES plants(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES plant_categories(id) ON DELETE CASCADE
        );
    ");
    
    return true;
}

// Execute initialization
if (initializeDatabase()) {
    echo "Database tables created successfully!";
} else {
    echo "Error creating database tables";
}