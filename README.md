# 📱 MobiFix Repair Management System

MobiFix is a web-based Mobile Phone Repair Management System designed to help repair businesses manage customers, repair requests, services, repair progress, and payments from one centralized platform.

The system provides separate interfaces for **customers** and **administrators**, making it easier to manage the complete repair process from request submission to completion.

---

## 🚀 Features

### 👤 Customer
- Customer registration and login
- Submit phone repair requests
- View personal repair history
- Track repair progress
- View repair status and estimated cost
- View technician notes
- View payment information
- Browse available repair services

### 🛠️ Administrator
- Secure admin login
- Admin dashboard with repair statistics
- Manage repair requests
- Manage registered customers
- Update repair status and costs
- Add technician notes
- Add, edit, and delete repair services
- Manage customer payments

### 📊 Repair Tracking

Repairs can move through the following stages:

```text
Pending → Diagnosing → In Progress
       → Ready for Collection → Completed

Repairs can also be marked as Cancelled when necessary.

🛠️ Tech Stack
Frontend: HTML5, CSS3, JavaScript
Backend: PHP
Database: MySQL
Server: Apache / XAMPP
Database Management: phpMyAdmin
Development: Visual Studio Code
Version Control: Git & GitHub
🗄️ Database

MobiFix uses a relational MySQL database with tables for:

customers
repairs
repair_services
spare_parts
repair_parts
payments
special_requests
admins

The database uses primary keys, foreign keys, and relationships to keep repair and customer information organized.

📁 Project Structure
MobiFix/
├── admin/
│   ├── dashboard.php
│   ├── login.php
│   ├── repair.php
│   ├── customer.php
│   ├── customers.php
│   └── service/
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
│   └── js/
│
├── index.php
├── login.php
├── register.php
└── README.md
📦 Installation
1. Clone the repository
git clone https://github.com/YOUR-USERNAME/MobiFix.git
cd MobiFix
2. Move the project to XAMPP

Place the project inside:

C:\xampp\htdocs\MobiFix
3. Start XAMPP

Start:

Apache
MySQL
4. Create the database

Open:

http://localhost/phpmyadmin

Create the MobiFix database and import the project's SQL database structure.

5. Configure the database

Update:

config/database.php

with your local MySQL credentials.

6. Run the application

Open:

http://localhost/MobiFix/
🔐 Security

The system implements several security practices, including:

Session-based authentication
Protected customer and admin pages
Customer repair ownership verification
Prepared SQL statements
Output escaping with htmlspecialchars()
Password hashing
Role-based access control
📱 Responsive Design

MobiFix is designed to work across:

💻 Desktop
💻 Laptop
📱 Tablet
📱 Mobile
🔄 System Workflow
Customer
   ↓
Register / Login
   ↓
Submit Repair
   ↓
Admin Reviews Repair
   ↓
Repair Status Updated
   ↓
Customer Tracks Progress
   ↓
Payment Recorded
   ↓
Repair Completed
📸 Screenshots

Screenshots of the following sections will be added:

Homepage
Customer dashboard
Repair details
Admin dashboard
Service management
Customer services page
Payment management
🎯 Project Purpose

MobiFix was developed to demonstrate how a traditional mobile phone repair process can be transformed into a centralized digital management system using PHP and MySQL.

The project focuses on customer management, repair tracking, service management, payment management, authentication, database relationships, and responsive web design.

🔮 Future Improvements
M-Pesa API integration
SMS and email notifications
Online payment processing
Digital invoices and receipts
Technician accounts
Customer reviews and ratings
Repair analytics and reports
Image uploads for damaged devices
📜 License

This project was developed for educational and demonstration purposes.


### One thing to change before pushing

Replace:

```text
https://github.com/YOUR-USERNAME/MobiFix.git

with your actual repository URL.

Since your GitHub username is wahuu2, once the repository is created, that section would become something like:

git clone https://github.com/wahuu2/MobiFix.git
cd MobiFix

This is the right level for the README: someone lands on GitHub, reads it in 1–2 minutes, understands what MobiFix is, sees the stack/features, and knows how to run it. The detailed academic documentation should remain separate from the README.
