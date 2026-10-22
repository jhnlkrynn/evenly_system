# Eventlynk

Eventlynk is a multi-vendor event-services booking platform for customers, vendors, admins, and super admins. It includes database-backed booking and payment flows, vendor operations, staff scheduling, dashboards and charts, PDF documents, database notifications, and role-based access control.

## Tech stack

- HTML, CSS, JavaScript
- Bootstrap 5 and vanilla JavaScript
- PHP and Laravel 13
- MySQL through XAMPP
- VS Code
- Git and GitHub
- Laravel Authentication and Policies
- Chart.js
- Laravel Database Notifications
- Laravel PDF with `barryvdh/laravel-dompdf`

## Local setup

1. Install and start XAMPP Apache and MySQL.
2. Create a MySQL database named `eventlynk` in phpMyAdmin.
3. From the project root, install PHP dependencies:

   ```bash
   composer install
   ```

4. Install Node dependencies:

   ```bash
   npm install
   ```

5. Copy the environment file and configure the MySQL values:

   ```bash
   cp .env.example .env
   ```

   Use `DB_CONNECTION=mysql`, `DB_DATABASE=eventlynk`, `DB_USERNAME=root`, and the XAMPP MySQL password in `.env`.

6. Generate the application key:

   ```bash
   php artisan key:generate
   ```

7. Run migrations and seed the demo database:

   ```bash
   php artisan migrate --seed
   ```

8. Link uploaded service images and vendor logos:

   ```bash
   php artisan storage:link
   ```

9. Start the frontend watcher and Laravel server in separate terminals:

   ```bash
   npm run dev
   php artisan serve
   ```

Open `http://127.0.0.1:8000`.

## Default seeded accounts

All seeded accounts use the password `password123`.

| Role | Email |
| --- | --- |
| Super admin | `superadmin@eventlynk.com` |
| Admin | `admin@eventlynk.com` |
| Vendor | `gourmet@events.ph` |
| Customer | `maria.santos@email.com` |

Additional vendor and customer accounts are created by the seeders for the marketplace and booking examples.

## Folder structure

```text
app/
  Http/Controllers/       Web, dashboard, booking, payment and notification endpoints
  Http/Requests/           Validated Form Requests grouped by feature
  Models/                  Eloquent models and relationships
  Notifications/           Database notification classes
  Policies/                Ownership and role authorization
  Support/                 Pricing, search and database dashboard providers
database/
  factories/               Test data factories
  migrations/              Application schema
  seeders/                 Reference data and demo records
resources/
  css/                     Eventlynk styles and responsive rules
  js/                      Bootstrap, Chart.js and vanilla JavaScript modules
  views/                   Blade pages, layouts, components and PDFs
routes/web.php             Web routes and role middleware
public/                    Public assets and storage symlink
tests/Feature/             HTTP and authorization feature tests
```

## Verification

Run the automated suite with:

```bash
php artisan test --compact
```

The suite covers role login and dashboard access, booking creation, vendor-date double-booking prevention, demo payments, dashboard ownership, staff conflicts, and database notifications.
