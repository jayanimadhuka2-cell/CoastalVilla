
-- =========================================
-- CEYLONORA
-- LUXURY COASTAL VILLA BOOKING SYSTEM
-- Database Structure and Sample Records
-- =========================================

CREATE DATABASE IF NOT EXISTS coastal_villa;

USE coastal_villa;


-- =========================================
-- 1. USERS TABLE
-- =========================================

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    password VARCHAR(255),
    role VARCHAR(20)
);


-- =========================================
-- 2. ROOMS TABLE
-- =========================================

CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255)
);


-- =========================================
-- 3. BOOKINGS TABLE
-- =========================================

CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT,
    guest_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    checkin DATE,
    checkout DATE,
    guests INT,
    status VARCHAR(20) DEFAULT 'Pending',

    FOREIGN KEY (room_id) REFERENCES rooms(room_id)
);


-- =========================================
-- 4. SAMPLE ROOM RECORDS
-- =========================================

INSERT INTO rooms
(room_name, description, price, image)
VALUES
(
    'Deluxe Ocean View Room',
    'Comfortable room with a beautiful ocean view.',
    15000.00,
    'room1.jpg'
),
(
    'Luxury Coastal Room',
    'Luxury accommodation with modern facilities.',
    22000.00,
    'room2.jpg'
),
(
    'Family Villa Room',
    'Spacious room suitable for families.',
    28000.00,
    'room3.jpg'
);


-- =========================================
-- 5. SAMPLE USER RECORDS
-- =========================================

-- Sample passwords must be PHP password_hash() values.
-- Replace these placeholders with real password hashes
-- before importing the records.

-- INSERT INTO users (name, email, password, role)
-- VALUES
-- ('Admin User', 'admin@example.com', 'PASTE_HASH_HERE', 'Admin'),
-- ('Sample Customer', 'customer@example.com', 'PASTE_HASH_HERE', 'Customer');


-- =========================================
-- 6. SAMPLE BOOKING RECORDS
-- =========================================

INSERT INTO bookings
(room_id, guest_name, email, phone, checkin, checkout, guests, status)
VALUES
(
    1,
    'Sample Guest',
    'sampleguest@example.com',
    '0712345678',
    '2027-01-10',
    '2027-01-12',
    2,
    'Pending'
);


-- =========================================
-- END OF CEYLONORA DATABASE STRUCTURE
-- =========================================
