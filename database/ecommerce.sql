CREATE DATABASE ecommerce_assignment;

USE ecommerce_assignment;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    status TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE product_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    option_type ENUM('size', 'paper') NOT NULL,
    option_name VARCHAR(100) NOT NULL,
    option_value VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_product_options_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE
);

CREATE TABLE pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    paper_type VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_pricing_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE,

    UNIQUE KEY unique_product_quantity_paper
        (product_id, quantity, paper_type)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    customer_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    product_id INT NOT NULL,
    size VARCHAR(100) NOT NULL,
    paper_type VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    artwork_file VARCHAR(255) NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_orders_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
);

CREATE TABLE uploaded_artwork_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100) NULL,
    file_size INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_artwork_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE SET NULL
);

INSERT INTO products
    (sku, name, description, image)
VALUES
    (
        'BC',
        'Custom Business Cards',
        'Premium custom printed business cards.',
        'business-card.jpg'
    );

SET @product_id = LAST_INSERT_ID();

INSERT INTO product_options
    (product_id, option_type, option_name, option_value)
VALUES
    (@product_id, 'size', 'Standard', '3.5 x 2 inches'),
    (@product_id, 'size', 'Square', '2.5 x 2.5 inches'),
    (@product_id, 'paper', 'Matte', 'Matte'),
    (@product_id, 'paper', 'Glossy', 'Glossy');

INSERT INTO pricing
    (product_id, quantity, paper_type, price)
VALUES
    (@product_id, 100, 'Matte', 20.00),
    (@product_id, 500, 'Matte', 80.00),
    (@product_id, 1000, 'Matte', 140.00),
    (@product_id, 100, 'Glossy', 25.00),
    (@product_id, 500, 'Glossy', 100.00),
    (@product_id, 1000, 'Glossy', 180.00);