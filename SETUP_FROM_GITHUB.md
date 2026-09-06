# RideSync Laravel Setup From GitHub

Use these steps after cloning or pulling this project from GitHub onto a new computer.

## Requirements

- PHP 8.2 or newer
- Composer
- Node.js and npm
- SQLite, or MySQL/MariaDB if you prefer using XAMPP database

## 1. Clone The Project

```bash
git clone YOUR_REPOSITORY_URL ridesync
cd ridesync
```

If you already cloned it before and only need the latest changes:

```bash
git pull
```

## 2. Install PHP Dependencies

```bash
composer install
```

## 3. Install JavaScript Dependencies

```bash
npm install
```

## 4. Create The Environment File

On Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

On Git Bash, macOS, or Linux:

```bash
cp .env.example .env
```

## 5. Generate The App Key

```bash
php artisan key:generate
```

## 6. Set Up The Database

The default `.env.example` uses SQLite:

```env
DB_CONNECTION=sqlite
```

Create the SQLite database file:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
```

If you want to use MySQL in XAMPP instead, create a database in phpMyAdmin, then update `.env` like this:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ridesync
DB_USERNAME=root
DB_PASSWORD=
```

## 7. Run Migrations

```bash
php artisan migrate
```

To also create demo data and sample accounts:

```bash
php artisan db:seed
```

Or run migrations and seed together:

```bash
php artisan migrate --seed
```

## 8. Build Frontend Assets

For development with live reload:

```bash
npm run dev
```

For a production-style build:

```bash
npm run build
```

## 9. Run The Laravel Server

```bash
php artisan serve
```

Open the app in your browser:

```text
http://127.0.0.1:8000
```

## Useful Commands

Clear cached config, routes, and views:

```bash
php artisan optimize:clear
```

Run tests:

```bash
php artisan test
```

Rebuild cached Blade views:

```bash
php artisan view:cache
```

List routes:

```bash
php artisan route:list
```

## Demo Login Accounts

These accounts are created when you run:

```bash
php artisan db:seed
```

Admin:

```text
Email: admin@ridesync.test
Password: Admin@123
```

Mechanic:

```text
Email: mechanic@ridesync.test
Password: Mechanic@123
```

Customer:

```text
Email: customer@ridesync.test
Password: Customer@123
```

## One-Command Setup Option

This project also has a Composer setup script:

```bash
composer run setup
```

That command installs Composer dependencies, creates `.env`, generates the app key, runs migrations, installs npm packages, and builds frontend assets.
