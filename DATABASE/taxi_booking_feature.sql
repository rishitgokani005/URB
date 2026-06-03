-- Taxi Booking Feature SQL
-- Database: dwk

CREATE TABLE acab (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    agency_id VARCHAR(20) NOT NULL,
    agency_name VARCHAR(100) DEFAULT NULL,
    cab_name VARCHAR(100) NOT NULL,
    cab_type VARCHAR(50) NOT NULL,
    seats INT DEFAULT 4,
    price_per_km INT DEFAULT 0,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(50) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    image2 VARCHAR(255) DEFAULT NULL,
    image3 VARCHAR(255) DEFAULT NULL,
    image4 VARCHAR(255) DEFAULT NULL
);

CREATE TABLE acabookings (
    sr_no INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(20) NOT NULL,
    user_id INT NOT NULL,
    cab_id VARCHAR(255) NOT NULL,
    agency_id VARCHAR(20) NOT NULL,
    pickup_location VARCHAR(255) NOT NULL,
    drop_location VARCHAR(255) NOT NULL,
    trip_type ENUM('oneway','roundtrip') DEFAULT 'oneway',
    booking_date DATE NOT NULL,
    return_date DATE DEFAULT NULL,
    pick_up_time TIME NOT NULL,
    total_price DECIMAL(10,2) DEFAULT 0,
    name VARCHAR(100) NOT NULL,
    idProof VARCHAR(255) DEFAULT NULL,
    mobile VARCHAR(20) NOT NULL,
    email VARCHAR(255) DEFAULT NULL,
    paymentMethod VARCHAR(100) DEFAULT 'Cash',
    booking_status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE acab
ADD CONSTRAINT fk_acab_agency
FOREIGN KEY (agency_id)
REFERENCES agencies(id)
ON DELETE CASCADE;

ALTER TABLE acabookings
ADD CONSTRAINT fk_acabooking_user
FOREIGN KEY (user_id)
REFERENCES users(user_id)
ON DELETE CASCADE;

ALTER TABLE acabookings
ADD CONSTRAINT fk_acabooking_agency
FOREIGN KEY (agency_id)
REFERENCES agencies(id)
ON DELETE CASCADE;

ALTER TABLE acabookings
ADD CONSTRAINT fk_acabooking_cab
FOREIGN KEY (cab_id)
REFERENCES acab(id)
ON DELETE CASCADE;

DELIMITER $$

CREATE TRIGGER generate_cab_booking_id
BEFORE INSERT ON acabookings
FOR EACH ROW
BEGIN
    IF NEW.booking_id IS NULL OR NEW.booking_id = '' THEN
        SET NEW.booking_id = CONCAT('CAB', UNIX_TIMESTAMP());
    END IF;
END $$

DELIMITER ;

INSERT INTO acab
(id, agency_id, agency_name, cab_name, cab_type, seats, price_per_km, address, city, image)
VALUES
('CAB001','AGY92503','Krishna Bikes','Swift Dzire','Sedan',4,12,'Dwarka Main Road','Dwarka','dzire.jpg'),
('CAB002','AGY92503','Krishna Bikes','Ertiga','SUV',6,15,'Dwarka Main Road','Dwarka','ertiga.jpg');
