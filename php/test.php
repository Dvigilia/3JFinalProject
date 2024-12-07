CREATE DATABASE BusinessModel;
use BusinessModel;


// USERS TABLE
CREATE TABLE Users(
    user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'therapist', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

//SERVICES TABLE 
CREATE TABLE Services(
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    duration INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

);

//appoinments

CREATE TABLE Appointments (
    appointment_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    therapist_id INT NOT NULL,
    service_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'canceled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(user_id),
    FOREIGN KEY (service_id) REFERENCES Services(service_id)
);

//Payments 

CREATE TABLE Payments(
    payment_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    paument_method ENUM('cash', 'credit_card', 'paypal') NOT NULL,
    payment_status ENUM('paid', 'unpaid', 'refunded') NOT NULL DEFAULT 'unpaid',
    transaction_id VARCHAR(100) UNIQUE,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES Appointments(appointment_id)
);

// Availability

CREATE TABLE Availability(
    availability_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    therapist_id INT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    FOREIGN KEY (therapist_id) REFERENCES Users(user_id)
);

// Reviews

CREATE TABLE Reviews(
    review_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK(rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES Appointments(appointment_id),
    FOREIGN KEY (user_id) REFERENCES Users(user_id)
);


INSERT INTO Services(service_name,description, price,duration)
VALUES('Swedish Massage','This is a type of full-body massage that is usually aimed at people who are new to massages or are sensitive to touch.','200','60'),
('Shiatsu','Japanese massage technique based on the principles of traditional Chinese medicine.','350','60'),
('Thai massage','Thai massage is a popular form of complementary and alternative medicine (CAM) that combines the principles of acupressure and yoga.','450','60');

INSERT INTO Users(full_name,email,phone_number,password,role)
VALUES('admin','admin@gmail.com','1234567','admin','admin');

INSERT INTO Services(service_name,description,price,duration)
VALUES('Deep tissue massage', 'Deep tissue massage uses more pressure than a Swedish massage. It's a good option if you have muscle problems.', '380', '80');

UPDATE Services
SET image_path = CASE 
    WHEN service_id = 2 THEN 'images/serv2.webp'
    WHEN service_id = 3 THEN 'images/serv3.jpg'
    WHEN service_id = 4 THEN 'images/serv4.jpg'
    ELSE image_path  -- Keeps existing value if no match
END;
