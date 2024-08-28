# Test Assignment
### MySQL scripts

```bat
CREATE DATABASE product_list;

USE product_list;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    type ENUM('DVD', 'Book', 'Furniture') NOT NULL,
    size INT NULL, -- for DVDs
    weight DECIMAL(10, 2) NULL, -- for Books
    height DECIMAL(10, 2) NULL, -- for Furniture
    width DECIMAL(10, 2) NULL, -- for Furniture
    length DECIMAL(10, 2) NULL -- for Furniture
);
```