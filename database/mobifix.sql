CREATE TABLE customers (
    CustomerID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(150) NOT NULL UNIQUE,
    Address VARCHAR(255),
    Phone VARCHAR(20) NOT NULL
);
CREATE TABLE repairs (
    RepairID INT AUTO_INCREMENT PRIMARY KEY,
    CustomerID INT NOT NULL,
    PhoneBrand VARCHAR(100) NOT NULL,
    PhoneModel VARCHAR(100) NOT NULL,
    IssueDetails TEXT NOT NULL,
    DateReceived DATETIME DEFAULT CURRENT_TIMESTAMP,
    EstimatedCost DECIMAL(10,2) DEFAULT 0.00,
    Status ENUM(
        'Pending',
        'Diagnosing',
        'In Progress',
        'Ready for Collection',
        'Completed',
        'Cancelled'
    ) DEFAULT 'Pending',
    DateCompleted DATETIME NULL,

    CONSTRAINT fk_repairs_customer
        FOREIGN KEY (CustomerID)
        REFERENCES customers(CustomerID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
CREATE TABLE repair_services (
    ServiceID INT AUTO_INCREMENT PRIMARY KEY,
    ServiceName VARCHAR(100) NOT NULL,
    Description TEXT,
    Price DECIMAL(10,2) NOT NULL,
    AvailabilityStatus ENUM('Available', 'Unavailable') DEFAULT 'Available'
);
CREATE TABLE spare_parts (
    PartID INT AUTO_INCREMENT PRIMARY KEY,
    PartName VARCHAR(150) NOT NULL,
    PartDescription TEXT,
    Price DECIMAL(10,2) NOT NULL,
    Quantity INT NOT NULL DEFAULT 0,
    AvailabilityStatus ENUM('Available', 'Out of Stock') DEFAULT 'Available'
);
CREATE TABLE repair_parts (
    RepairPartID INT AUTO_INCREMENT PRIMARY KEY,
    RepairID INT NOT NULL,
    PartID INT NOT NULL,
    QuantityUsed INT NOT NULL DEFAULT 1,

    CONSTRAINT fk_repair_parts_repair
        FOREIGN KEY (RepairID)
        REFERENCES repairs(RepairID)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_repair_parts_part
        FOREIGN KEY (PartID)
        REFERENCES spare_parts(PartID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
CREATE TABLE payments (
    PaymentID INT AUTO_INCREMENT PRIMARY KEY,
    RepairID INT NOT NULL,
    Amount DECIMAL(10,2) NOT NULL,
    PaymentMethod ENUM('Cash', 'M-Pesa', 'Card', 'Bank Transfer') NOT NULL,
    PaymentStatus ENUM('Pending', 'Paid', 'Failed', 'Refunded') DEFAULT 'Pending',
    TransactionReference VARCHAR(100),
    PaymentDate DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_payments_repair
        FOREIGN KEY (RepairID)
        REFERENCES repairs(RepairID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
CREATE TABLE special_requests (
    RequestID INT AUTO_INCREMENT PRIMARY KEY,
    RepairID INT NOT NULL,
    RequestDetails TEXT NOT NULL,
    RequestDate DATETIME DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_special_requests_repair
        FOREIGN KEY (RepairID)
        REFERENCES repairs(RepairID)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
CREATE TABLE admins (
    AdminID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(150) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    Phone VARCHAR(20),
    CreatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
);