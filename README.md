# MobiFix Repair Management System — README

Below is a complete `README.md` you can put in the **root of your MobiFix project**.

````markdown
# MobiFix Repair Management System

A web-based Mobile Phone Repair Management System designed to help phone repair businesses manage customers, repair requests, repair services, repair progress, and payments.

---

## Project Overview

MobiFix provides a centralized platform for managing mobile phone repair operations.

The system has two main users:

- Customer
- Administrator

Customers can create accounts, submit repair requests, track repair progress, view technician notes, view repair costs, view available services, and monitor their payments.

Administrators can manage customers, repair requests, repair statuses, repair costs, services, and payments through an administrative dashboard.

---

## Features

### Customer Features

- Customer registration
- Customer login and logout
- Customer dashboard
- Submit phone repair requests
- View personal repair history
- View detailed repair information
- Track repair progress
- View repair status
- View estimated repair cost
- View technician notes
- View payment information
- View available repair services

### Administrator Features

- Administrator login and logout
- Admin dashboard
- View repair statistics
- View recent repair requests
- Manage repair requests
- View customer information
- Manage registered customers
- Update repair status
- Update estimated repair costs
- Add technician notes
- Manage repair services
- Add services
- Edit services
- Delete services
- Manage payments
- View payment records

---

## Repair Statuses

MobiFix uses the following repair stages:

1. Pending
2. Diagnosing
3. In Progress
4. Ready for Collection
5. Completed
6. Cancelled

Customers can track their repair based on these statuses.

---

## Technologies Used

### Frontend

- HTML5
- CSS3
- JavaScript

### Backend

- PHP

### Database

- MySQL

### Development Environment

- XAMPP
- Apache
- phpMyAdmin

### Development Tools

- Visual Studio Code
- Git
- GitHub

---

## Database

The MobiFix database uses a relational MySQL database.

### Main Tables

- `customers`
- `repairs`
- `repair_services`
- `spare_parts`
- `repair_parts`
- `payments`
- `special_requests`
- `admins`

### Main Relationships

```text
customers
    |
    | 1
    |
    | many
    v
repairs
    |
    +-----------------> payments
    |
    +-----------------> special_requests
    |
    v
repair_parts
    |
    v
spare_parts
````

The `repair_services` table stores the services offered by MobiFix.

Services added or updated by an administrator are automatically displayed on the customer services page.

---

## System Workflow

### Customer Workflow

```text
Customer
    |
    v
Register
    |
    v
Login
    |
    v
Customer Dashboard
    |
    v
Submit Repair Request
    |
    v
Admin Reviews Repair
    |
    v
Repair Status Updated
    |
    v
Customer Tracks Repair
    |
    v
Payment Recorded
    |
    v
Customer Views Payment
    |
    v
Repair Completed
```

### Administrator Workflow

```text
Admin
    |
    v
Admin Login
    |
    v
Admin Dashboard
    |
    +---- Manage Repairs
    |
    +---- Manage Customers
    |
    +---- Manage Services
    |
    +---- Manage Payments
    |
    v
Update System Records
```

---

## Project Structure

The project is organized into separate sections for customers, administrators, services, configuration, and reusable components.

```text
MobiFix/
│
├── admin/
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── repair.php
│   ├── customer.php
│   ├── customers.php
│   │
│   └── service/
│       ├── index.php
│       ├── add.php
│       ├── edit.php
│       └── delete.php
│
├── customer/
│   ├── dashboard.php
│   ├── repair.php
│   └── logout.php
│
├── services/
│   └── index.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   └── footer.php
│
├── config/
│   └── database.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   └── js/
│
├── index.php
├── login.php
├── register.php
│
└── README.md
```

> The exact folder structure may contain additional files depending on the final implementation.

---

## Installation and Setup

### 1. Install XAMPP

Download and install XAMPP.

Start:

* Apache
* MySQL

---

### 2. Copy the Project

Place the MobiFix project inside the XAMPP `htdocs` directory.

Example:

```text
C:\xampp\htdocs\MobiFix
```

---

### 3. Create the Database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Create a database for the project.

Example:

```text
mobifix
```

---

### 4. Create the Database Tables

Run the SQL database script in phpMyAdmin.

The database contains:

```text
customers
repairs
repair_services
spare_parts
repair_parts
payments
special_requests
admins
```

---

### 5. Configure Database Connection

Open:

```text
config/database.php
```

Update the database credentials if necessary.

Example:

```php
<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "mobifix"
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
```

---

### 6. Run the Application

Open the browser and visit:

```text
http://localhost/MobiFix/
```

The MobiFix homepage should appear.

---

## Customer Access

Customers can:

1. Open the MobiFix website.
2. Create an account.
3. Log in.
4. Access their dashboard.
5. Submit a repair request.
6. Track their repair.
7. View repair information.
8. View payment information.

---

## Administrator Access

Administrators use the admin login page.

Example:

```text
http://localhost/MobiFix/admin/login.php
```

After successful authentication, the administrator is redirected to the admin dashboard.

From there, administrators can manage:

* Repairs
* Customers
* Services
* Payments

---

## Security

The system includes several security measures.

### Session Protection

Protected customer and administrator pages verify that the appropriate user is logged in.

### Customer Authorization

Customers can only access repairs belonging to their own customer account.

### Prepared Statements

Prepared statements are used for database queries involving user-controlled values.

### Output Escaping

Database and user-generated content is escaped before being displayed using:

```php
htmlspecialchars()
```

### Password Hashing

Passwords should be stored using PHP's secure password hashing functions:

```php
password_hash()
```

Passwords can then be verified using:

```php
password_verify()
```

---

## Responsive Design

MobiFix is designed to work across:

* Desktop computers
* Laptops
* Tablets
* Mobile phones

Responsive CSS media queries are used to adjust layouts for smaller screens.

---

## Testing

The system should be tested using the following scenarios:

| Test                       | Expected Result                  |
| -------------------------- | -------------------------------- |
| Customer registration      | Customer account is created      |
| Duplicate email            | Registration is rejected         |
| Valid customer login       | Dashboard opens                  |
| Invalid login              | Access is denied                 |
| Repair submission          | Repair is stored                 |
| Customer views repair      | Own repair information displayed |
| Unauthorized repair access | Access denied                    |
| Admin login                | Admin dashboard opens            |
| Update repair              | Repair information changes       |
| Add service                | Service appears in database      |
| Edit service               | Service information updates      |
| Delete service             | Service is removed               |
| Customer services page     | Current services are displayed   |
| Add payment                | Payment is recorded              |
| Customer payment view      | Payment information displayed    |
| Logout                     | User session is terminated       |

---

## Future Improvements

Possible future improvements include:

* M-Pesa payment integration
* SMS notifications
* Email notifications
* Online payment processing
* Automated invoices
* Digital payment receipts
* Technician accounts
* Customer feedback and ratings
* Repair analytics
* Advanced search and filtering
* Phone damage image uploads
* Automated database backups
* Production deployment

---

## Development

This project was developed as an academic/software development project to demonstrate the use of PHP, MySQL, HTML, CSS, JavaScript, database relationships, authentication, CRUD operations, and responsive web design.

---

## Version Control

Git is used for version control and GitHub is used to store the project's source code.

Development is organized into incremental commits as major features are completed.

---

## Project Status

```text
Project Setup             ✅
Database                  ✅
Customer Authentication   ✅
Customer Dashboard        ✅
Repair Management         ✅
Repair Tracking           ✅
Customer Management       ✅
Service Management        ✅
Payment Management        ✅
Homepage                  ✅
Responsive CSS            ✅
Security Review           🔄
Final Testing             🔄
Documentation             🔄
Deployment                ⏳
```

---

## License

This project was developed for educational and demonstration purposes.

---

## Author

**MobiFix Repair Management System**

Developed using PHP, MySQL, HTML, CSS and JavaScript.

````

### Where to put it

Create this file directly inside your main project folder:

```text
MobiFix/
│
├── admin/
├── customer/
├── services/
├── includes/
├── config/
├── assets/
├── index.php
├── login.php
├── register.php
│
└── README.md   ← HERE
````

Then when you push the project to GitHub, GitHub will automatically display the README on the repository's main page.

One thing I would **not** do yet is put fake screenshots, fake demo credentials, or a fake live URL into the README. Once the final testing and deployment are done, we can add those properly.
