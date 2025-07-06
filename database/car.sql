
CREATE DATABASE IF NOT EXISTS car;
USE car;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    id_number VARCHAR(20),
    license_number VARCHAR(20),
    license_place VARCHAR(50),
    role ENUM('user', 'worker', 'admin') DEFAULT 'user',
    is_active BOOLEAN DEFAULT FALSE,
    reset_token VARCHAR(100),
    activation_token VARCHAR(100)
);

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    model VARCHAR(100),
    year INT,
    fuel_type VARCHAR(20),
    seats INT,
    gearbox VARCHAR(20),
    image VARCHAR(255),
    views INT DEFAULT 0
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vehicle_id INT,
    start_datetime DATETIME,
    end_datetime DATETIME,
    code VARCHAR(100),
    is_cancelled BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
);

CREATE TABLE returns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT,
    return_time DATETIME,
    vehicle_condition TEXT,
    damage_report TEXT,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reservation_id INT,
    comment_text TEXT,
    comment_time DATETIME,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id)
);
