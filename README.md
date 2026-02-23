# Auto Lusitano - Virtual Car Dealership

## Project Overview

**Auto Lusitano** is a comprehensive web application for managing a virtual car dealership in Portugal. This project was developed as part of the DAW (Desenvolvimento de Aplicações Web) course to demonstrate CRUD operations in a real-world business scenario.

The application serves as a digital platform for a car dealership established in 1985, offering services for buying, selling, and renting vehicles. It provides a complete management system for inventory, customers, sales transactions, and rental agreements.

## Purpose

This educational project aims to:
- Demonstrate practical implementation of CRUD (Create, Read, Update, Delete) operations
- Showcase PHP/PDO integration with MySQL databases
- Implement a RESTful API architecture
- Create a responsive web interface using modern frontend technologies
- Consolidate knowledge of web application development concepts

## Features

### Core Functionality
- **Customer Management**: Complete CRUD operations for client records
- **Vehicle Inventory**: Manage car listings with detailed specifications and image uploads
- **Sales Management**: Track vehicle sales transactions
- **Rental Management**: Handle car rental agreements and bookings
- **Status Management**: Activate/deactivate records with soft delete functionality

### Technical Features
- RESTful API endpoints for data operations
- AJAX-powered dynamic interfaces
- Form validation and user feedback
- Responsive Bootstrap 5 design
- Font Awesome icons for enhanced UI
- **Image Upload System**: Secure file upload with validation, automatic replacement, and deletion

## Technology Stack

### Frontend
- HTML5
- CSS3
- Bootstrap 5
- JavaScript (AJAX)
- Font Awesome Icons

### Backend
- PHP 7+
- MySQL Database
- PDO (PHP Data Objects)

## Project Structure

```
auto-lusitano/
├── api/                    # REST API endpoints
│   ├── cars/              # Vehicle management endpoints
│   │   ├── upload_image.php    # Image upload handler
│   │   ├── delete_image.php    # Image deletion handler
│   │   └── ...                 # Other car endpoints
│   ├── customer/          # Customer management endpoints
│   ├── rentals/           # Rental management endpoints
│   └── sales/             # Sales management endpoints
├── images/                # Car image storage
├── car_stand.sql          # Database schema
├── *.php                  # Main application pages
├── header.html            # Shared navigation component
└── README.md              # Project documentation
```

## Database

The application uses a MySQL database with the schema defined in `car_stand.sql`. The database includes tables for:
- Customers (clients)
- Vehicles (cars) - includes image_filename for photo storage
- Sales transactions
- Rental agreements
- Users (authentication system)

## Authentication & Security

The application includes a comprehensive login system to protect access to all features:

### User Roles
- **Admin**: Full access to all features and user management
- **Manager**: Access to all business operations (CRUD on customers, cars, sales, rentals)
- **User**: Limited access to view and basic operations

### Default Login Credentials
- **Admin**: `admin` / `password123`
- **Manager**: `manager` / `password123`
- **User**: `user` / `password123`

### Security Features
- Password hashing using PHP's `password_hash()` function
- Session-based authentication
- Protected routes requiring login
- Role-based access control
- Secure logout functionality

## Installation & Setup

1. Import the `car_stand.sql` file into your MySQL database
2. Configure database connection in `cnn.php` and `api/cnn.php`
3. Ensure PHP 7+ and MySQL are installed
4. Access the application through a web server (Apache/Nginx)
5. Login using the default credentials above

## Author

**André Gonçalves**
- DAW Student
- Individual Project for Web Application Development Course
- Date: February 22, 2026

## Academic Context

This project was developed to fulfill the requirements of the Desenvolvimento de Aplicações Web (DAW) course, focusing on practical application of web development concepts learned throughout the semester.
