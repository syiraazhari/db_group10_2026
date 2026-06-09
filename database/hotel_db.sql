CREATE DATABASE IF NOT EXISTS hotel_db;
USE hotel_db;

CREATE TABLE ADMIN (
    AdminID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL
);

CREATE TABLE CUSTOMER (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(100) NOT NULL,
    IC_PassportNo VARCHAR(20) NOT NULL UNIQUE,
    Gender VARCHAR(10) NOT NULL,
    DateOfBirth DATE NOT NULL,
    PhoneNo VARCHAR(15) NOT NULL,
    Email VARCHAR(100) UNIQUE,
    Address VARCHAR(255) NOT NULL,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL
);

CREATE TABLE ROOM_TYPE (
    RoomTypeID INT AUTO_INCREMENT PRIMARY KEY,
    TypeName VARCHAR(50) NOT NULL UNIQUE,
    Description TEXT NOT NULL,
    PricePerNight DECIMAL(10,2) NOT NULL,
    MaximumGuests INT NOT NULL
);

CREATE TABLE STAFF (
    StaffID INT AUTO_INCREMENT PRIMARY KEY,
    StaffName VARCHAR(100) NOT NULL,
    PhoneNo VARCHAR(15) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Position VARCHAR(50) NOT NULL DEFAULT 'Receptionist'
);

CREATE TABLE ROOM (
    RoomID INT AUTO_INCREMENT PRIMARY KEY,
    RoomTypeID INT NOT NULL,
    RoomNumber VARCHAR(10) NOT NULL UNIQUE,
    FloorNumber INT NOT NULL,
    RoomStatus VARCHAR(20) NOT NULL DEFAULT 'Available',
    CONSTRAINT fk_room_type FOREIGN KEY (RoomTypeID) REFERENCES ROOM_TYPE(RoomTypeID)
);

CREATE TABLE RESERVATION (
    ReservationID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT NOT NULL,
    RoomID INT NOT NULL,
    StaffID INT NULL,
    CheckInDate DATE NOT NULL,
    CheckOutDate DATE NOT NULL,
    NumberOfGuests INT NOT NULL,
    ReservationDate DATE NOT NULL,
    ReservationStatus VARCHAR(20) NOT NULL DEFAULT 'Pending',
    CONSTRAINT fk_reservation_customer FOREIGN KEY (CustomerID) REFERENCES CUSTOMER(CustomerID),
    CONSTRAINT fk_reservation_room FOREIGN KEY (RoomID) REFERENCES ROOM(RoomID),
    CONSTRAINT fk_reservation_staff FOREIGN KEY (StaffID) REFERENCES STAFF(StaffID)
);

CREATE TABLE INVOICE (
    InvoiceID INT AUTO_INCREMENT PRIMARY KEY,
    ReservationID INT NOT NULL UNIQUE,
    InvoiceNumber VARCHAR(50) NOT NULL UNIQUE,
    InvoiceDate DATE NOT NULL,
    TotalAmount DECIMAL(10,2) NOT NULL,
    InvoiceStatus VARCHAR(20) NOT NULL DEFAULT 'Unpaid',
    PaymentDate DATE NULL,
    CONSTRAINT fk_invoice_reservation FOREIGN KEY (ReservationID) REFERENCES RESERVATION(ReservationID)
);

INSERT INTO ADMIN (Username, Password) VALUES
('hotel_admin', 'admin123');

INSERT INTO STAFF (StaffName, PhoneNo, Email, Username, Password, Position) VALUES
('Siti Nurhaliza', '0171234567', 'siti@hotel.com', 'siti_staff', 'staff123', 'Receptionist'),
('Ali Rahman', '0182233445', 'ali@hotel.com', 'ali_staff', 'staff123', 'Front Desk Officer');

INSERT INTO ROOM_TYPE (TypeName, Description, PricePerNight, MaximumGuests) VALUES
('Standard Room', 'Comfortable standard room with basic facilities', 120.00, 2),
('Deluxe Room', 'Spacious deluxe room with city view', 250.00, 3),
('Suite Room', 'Luxury suite with premium amenities', 450.00, 4);

INSERT INTO ROOM (RoomTypeID, RoomNumber, FloorNumber, RoomStatus) VALUES
(1, '101', 1, 'Available'),
(1, '102', 1, 'Available'),
(2, '201', 2, 'Available'),
(2, '202', 2, 'Maintenance'),
(3, '301', 3, 'Available');

INSERT INTO CUSTOMER (FullName, IC_PassportNo, Gender, DateOfBirth, PhoneNo, Email, Address, Username, Password) VALUES
('Muhammad Adief', 'A12345678', 'Male', '2003-01-15', '0123456789', 'adief@email.com', 'Kuala Lumpur', 'adief01', 'customer123'),
('Aisyah Binti Omar', 'B22334455', 'Female', '2002-11-21', '0131122334', 'aisyah@email.com', 'Johor Bahru', 'aisyah02', 'customer123');
