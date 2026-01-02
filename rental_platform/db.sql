-- Active: 1764673028424@@127.0.0.1@3306@rental_platform
CREATE DATABASE if NOT EXISTS rental_platform;
Use rental_platform;
-- here User Table
CREATE TABLE users(
    id INT PRIMARY key AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT null,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(16),
    role ENUM('travler', 'host', 'admin') NOT NULL DEFAULT 'travler',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
--here Home(rentals) Table
CREATE Table rentals (
    id int PRIMARY key AUTO_INCREMENT,
    host_id int not NULL,
    title VARCHAR(255) not NULL,
    description VARCHAR(255) not null,
    city VARCHAR(255) not null,
    address VARCHAR(500) NOT NULL,
    price_per_night decimal(10, 2) not null,
    max_guests int not null,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Foreign Key (host_id) REFERENCES users(id) on delete CASCADE
);
--here booking table
CREATE table bookings(
    id int primary key AUTO_INCREMENT,
    rental_id int not null, 
    user_id int not null, 
    start_date date not null,
    end_date date not null,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Foreign Key (rental_id) REFERENCES rentals(id) on   delete CASCADE,
    Foreign Key (user_id) REFERENCES users(id)  on  delete CASCADE,
    CHECK (end_date > start_date)
);
--here favorite table
CREATE TABLE favorites (
    user_id INT NOT NULL,
    rental_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (user_id, rental_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE CASCADE
);
--here reviews table
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    rental_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    
    UNIQUE KEY unique_review (booking_id)
);
