# Online Metro System

A web-based Online Metro System developed as a Final Year Project (FYP). The system allows users to register, log in, search metro routes and timings, book tickets, make payments, view tickets, and receive notifications. An admin panel is included for managing trains, schedules, tickets, passengers, and notifications.

## Technologies Used
- PHP
- MySQL / MariaDB
- HTML
- CSS
- JavaScript
- XAMPP (for local development)

## Main Features
### User
- User registration and login
- Forgot/reset password
- Search metro routes
- View timings
- Book tickets
- Payment handling
- Ticket viewing
- Notifications

### Admin
- Admin login and dashboard
- Manage trains
- Manage schedules
- Manage tickets
- Manage passengers
- Manage notifications

## Installation (XAMPP)
1. Install and start Apache and MySQL in XAMPP.
2. Copy this project folder into the `htdocs` directory.
3. Open phpMyAdmin.
4. Create/import the database using `oms_db.sql`.
5. Check database credentials in `config.php` and `admin/config.php` if necessary.
6. Open the project in your browser using:
   `http://localhost/Online-Metro-System-GitHub/`

## Database
The database dump is included in:

`oms_db.sql`

Import this file through phpMyAdmin before running the project.

## Project Structure
```text
Online-Metro-System-GitHub/
├── admin/                 # Admin panel
├── index.php              # Home page
├── login.php              # User login
├── register.php           # User registration
├── dashboard.php          # User dashboard
├── search.php             # Metro search
├── timings.php            # Metro timings
├── book.php               # Ticket booking
├── payment.php            # Payment page
├── ticket.php             # Ticket page
├── notifications.php      # Notifications
├── config.php             # Database configuration
├── oms_db.sql             # Database
├── style.css
└── README.md
```

## GitHub
This repository contains the source code and database required to run the project locally or deploy it to a PHP/MySQL-compatible hosting service.

## Author
Areeba Naz
