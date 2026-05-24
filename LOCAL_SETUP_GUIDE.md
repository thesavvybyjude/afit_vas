# Local Setup Guide for Windows (XAMPP)

Welcome! Since you are running this project locally on a Windows machine using XAMPP, this guide is specifically tailored to help you bypass common local environment errors and get the AFIT Virtual Admission System running smoothly.

There are two ways to run this project:
1. **Method A:** Using XAMPP's Apache Server (Best for production-like testing).
2. **Method B:** Using PHP's Built-in Server (Best if you want to run it straight from your Downloads folder).

Choose the method that works best for you!

---

## Prerequisites (Important Fixes for XAMPP)

Before starting either method, we need to ensure your XAMPP is configured correctly.

### 1. Enable the PHP ZIP Extension
Composer needs the `zip` extension to download the backend libraries.
1. Open your XAMPP Control Panel.
2. Click the **Config** button next to Apache and select `php.ini`.
3. In the text file that opens, press `Ctrl+F` and search for `;extension=zip`.
4. Remove the semicolon (`;`) at the beginning of the line so it looks exactly like this:
   `extension=zip`
5. Save the file and close it.
6. **Stop** and **Restart** Apache in the XAMPP Control Panel.

### 2. Configure the Database
Because some local XAMPP MySQL setups block terminal scripts from creating databases automatically (due to missing `caching_sha2_password.dll` plugins), the easiest way is to use phpMyAdmin:

1. Open your browser and go to: [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)
2. Click the **Databases** tab at the top.
3. Under "Create database", type `afit_vas` (leave the dropdown as `utf8mb4_unicode_ci`) and click **Create**.
4. Click on the new `afit_vas` database in the left sidebar.
5. Click the **Import** tab at the top.
6. Click **Choose File** and navigate to `Final_AFIT_VAS/database/`.
7. Select `schema.sql` and click **Import** at the bottom of the page.

---

## Method A: Running via XAMPP Apache (Recommended)

If you want to use the standard `http://localhost/` URL, you must move the project into your `htdocs` folder.

### 1. Move the Folder
Copy or move the entire `Final_AFIT_VAS` folder from your Downloads into your XAMPP directory:
`C:\xampp\htdocs\Final_AFIT_VAS`

### 2. Configure the Environment
1. Go into `C:\xampp\htdocs\Final_AFIT_VAS\backend\`.
2. Copy `.env.example` and rename it to `.env`.
3. Open `.env` and update the URLs to point to your `htdocs` path:
   ```ini
   APP_URL=http://localhost/Final_AFIT_VAS/backend/public
   FRONTEND_URL=http://localhost/Final_AFIT_VAS/frontend
   ```
*(Note: Keep DB_USER as `root` and DB_PASS blank, as is standard for XAMPP).*

### 3. Install Backend Dependencies
1. Open PowerShell or Command Prompt.
2. Navigate to the backend folder:
   ```bash
   cd C:\xampp\htdocs\Final_AFIT_VAS\backend
   ```
3. Run the Composer installation:
   ```bash
   php composer.phar install
   ```

### 4. Update the Frontend API URL
1. Open `C:\xampp\htdocs\Final_AFIT_VAS\frontend\js\api.js`.
2. Change the `API_BASE` variable at the very top of the file to:
   ```javascript
   const API_BASE = 'http://localhost/Final_AFIT_VAS/backend/public/api';
   ```

### 5. Test the Application
Open your browser and visit:
- **Applicant Portal:** [http://localhost/Final_AFIT_VAS/frontend/](http://localhost/Final_AFIT_VAS/frontend/)
- **Admin Portal:** [http://localhost/Final_AFIT_VAS/frontend/admin/login.html](http://localhost/Final_AFIT_VAS/frontend/admin/login.html)

---

## Method B: Running directly from your Downloads folder

If you prefer NOT to move the files into `htdocs`, you can run two separate local servers directly from the `Final_AFIT_VAS` folder using PHP's built-in tools.

### 1. Configure the Environment
1. Go into `Final_AFIT_VAS/backend/`.
2. Copy `.env.example` and rename it to `.env`.
3. Open `.env` and ensure the URLs match the local servers we are about to start:
   ```ini
   APP_URL=http://localhost:8000
   FRONTEND_URL=http://localhost:3000
   ```

### 2. Install Backend Dependencies
1. Open PowerShell or Command Prompt.
2. Navigate to the backend folder in your Downloads:
   ```bash
   cd "C:\Users\thesa\Downloads\P V1\P V1\Final_AFIT_VAS\backend"
   ```
3. Run the Composer installation:
   ```bash
   php composer.phar install
   ```

### 3. Start the Backend API Server
In that same terminal window (still inside the `backend` folder), run:
```bash
php -S localhost:8000 -t public
```
*(Leave this terminal window open!)*

### 4. Start the Frontend Server
Open a **second, brand new** terminal window.
1. Navigate to the frontend folder:
   ```bash
   cd "C:\Users\thesa\Downloads\P V1\P V1\Final_AFIT_VAS\frontend"
   ```
2. Start the frontend server:
   ```bash
   php -S localhost:3000
   ```
*(Leave this terminal window open too!)*

### 5. Test the Application
Open your browser and visit:
- **Applicant Portal:** [http://localhost:3000](http://localhost:3000)
- **Admin Portal:** [http://localhost:3000/admin/login.html](http://localhost:3000/admin/login.html)

---

## Default Admin Credentials
When logging into the Admin portal, use:
- **Email:** `superadmin@afit.edu.ng`
- **Password:** `Password123`
