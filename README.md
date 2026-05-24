# AFIT Virtual Admission System - Final Version

A cloud-based virtual admission system for the Air Force Institute of Technology (AFIT), Kaduna. This system digitises the entire student admission lifecycle — from applicant registration and document submission to automated eligibility screening and administrative decision-making.

This is the consolidated **Final Version**, combining the robust backend logic with an automated setup process.

## Features
- **Applicant Portal:** Registration, Multi-Step Application, Document Uploads, Real-Time Status Tracking.
- **Admin Portal:** Dashboard Statistics, Application Review, Eligibility Overrides, Document Verification, and CSV Reporting.
- **Technical Specs:** PHP 8.x backend (custom MVC, PDO, JWT Auth), Vanilla HTML/JS/CSS frontend, MySQL Database.

---

## 🚀 Quick Setup Guide

Since you are running this locally, you can use the built-in automated setup tools.

### 1. Configure the Environment
1. In the `backend/` directory, copy `.env.example` to `.env`.
2. Open `backend/.env` and ensure your database credentials are correct. (Default assumes `root` with no password for XAMPP).

### 2. Initialize the Database
Open your terminal/command prompt at the root of the project (where this README is) and run:
```bash
php setup.php
```
*Note: If `setup.php` fails due to XAMPP's `caching_sha2_password` issue, open phpMyAdmin (`http://localhost/phpmyadmin`), create the `afit_vas` database, and manually import the SQL files from the `database/` folder in order.*

### 3. Install Dependencies
Ensure your PHP `zip` extension is enabled in `php.ini`. Then run:
```bash
cd backend
php composer.phar install
# Or: composer update
```

### 4. Start the Servers
You can easily run this using PHP's built-in web server instead of moving files to `htdocs`.

**Start the Backend API:**
```bash
cd backend
php -S localhost:8000 -t public
```

**Start the Frontend Portal:**
Open a *new* terminal window, and run:
```bash
cd frontend
php -S localhost:3000
```

### 5. Access the Portals
- **Applicant Portal:** [http://localhost:3000](http://localhost:3000)
- **Staff Portal:** [http://localhost:3000/admin/login.html](http://localhost:3000/admin/login.html)
  - *Default Admin Email:* `superadmin@afit.edu.ng`
  - *Default Admin Password:* `Password123`
