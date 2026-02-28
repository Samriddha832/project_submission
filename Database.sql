-- Create Database
CREATE DATABASE IF NOT EXISTS HotelBooking;
USE HotelBooking;

-- --------------------------------------------------------
-- USERS TABLE
-- --------------------------------------------------------
CREATE TABLE users (
    user_id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(45),
    password VARCHAR(255),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_image VARCHAR(255),
    level ENUM('user','admin','hoteladmin'),
    PRIMARY KEY (user_id)
);


-- --------------------------------------------------------
-- HOTELS TABLE
-- --------------------------------------------------------
CREATE TABLE hotels (
    hotel_id INT(11) NOT NULL AUTO_INCREMENT,
    hotel_name VARCHAR(150),
    location VARCHAR(150),
    created_by VARCHAR(50),
    hotel_image VARCHAR(255),
    about TEXT,
    hotel_admin_id INT(11),
    hotel_address_link VARCHAR(250),
    PRIMARY KEY (hotel_id),
    KEY hotel_admin_id (hotel_admin_id),
    FOREIGN KEY (hotel_admin_id) REFERENCES users(user_id)
);


-- --------------------------------------------------------
-- HOTEL ROOM TABLE
-- --------------------------------------------------------
CREATE TABLE hotel_rooms (
    room_id INT(11) NOT NULL AUTO_INCREMENT,
    hotel_id INT(11) NOT NULL,
    room_number VARCHAR(50) NOT NULL,
    room_type VARCHAR(50) DEFAULT 'Single',
    room_price DECIMAL(10,2) NOT NULL,
    status ENUM('Available','Booked') DEFAULT 'Available',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    room_image VARCHAR(250),
    about_rooms TEXT,
    PRIMARY KEY (room_id),
    KEY hotel_id (hotel_id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id)
);


-- --------------------------------------------------------
-- BOOKINGS TABLE
-- --------------------------------------------------------
CREATE TABLE bookings (
    booking_id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    hotel_id INT(11) NOT NULL,
    room_id INT(11) NOT NULL,
    rooms_booked INT(11) NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    nights INT(11) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    booked_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('booked','cancelled','completed','cancel_requested') NOT NULL DEFAULT 'booked',
    email_send VARCHAR(45) DEFAULT '0',
    PRIMARY KEY (booking_id),
    KEY user_id (user_id),
    KEY hotel_id (hotel_id),
    KEY room_id (room_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id),
    FOREIGN KEY (room_id) REFERENCES hotel_rooms(room_id)
);


-- --------------------------------------------------------
-- PAYMENTS TABLE
-- --------------------------------------------------------
CREATE TABLE payments (
    payment_id INT(11) NOT NULL AUTO_INCREMENT,
    booking_id INT(11),
    user_id INT(11),
    amount DECIMAL(10,2),
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    payment_status ENUM('pending','completed','failed','paid') DEFAULT 'pending',
    payment_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (payment_id),
    KEY booking_id (booking_id),
    KEY user_id (user_id),
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);




CREATE TABLE otp (
    id INT(11) NOT NULL AUTO_INCREMENT,
    otp VARCHAR(45),
    email VARCHAR(200),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    attempt INT(11) DEFAULT 0,
    email_sent VARCHAR(45) DEFAULT '0',
    PRIMARY KEY (id)
);


CREATE TABLE messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,      -- user_id (client or admin)
    receiver_id INT NOT NULL,    -- user_id
    hotel_id INT NULL,           -- which hotel (optional but useful)
    room_id INT NULL,            -- which room (optional)
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT DEFAULT 0,

    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id),
    FOREIGN KEY (hotel_id) REFERENCES hotels(hotel_id),
    FOREIGN KEY (room_id) REFERENCES hotel_rooms(room_id)
);

ALTER TABLE messages ADD auto_sent TINYINT DEFAULT 0;



CREATE TABLE otp (
    id INT(11) NOT NULL AUTO_INCREMENT,
    otp VARCHAR(45),
    email VARCHAR(200),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    attempt INT(11) DEFAULT 0,
    email_sent VARCHAR(45) DEFAULT '0',
    PRIMARY KEY (id)
);