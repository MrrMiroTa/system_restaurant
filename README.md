# Restaurant Management Admin Dashboard

A responsive admin dashboard for restaurant management using PHP and MySQL.

## Features

- Admin registration and login
- Dashboard with total sales (today/month), top ordered menu
- CRUD for menu and stock (with categories: ingredient, meat, vegetable, drink)
- Order management and receipt printing
- Low-stock alerts (<5)
- Responsive design with sidebar menu and logo

## Getting Started

1. Import `database/init.sql` into MySQL.
2. Place the project in your XAMPP `htdocs` directory.
3. Start Apache and MySQL from XAMPP control panel.
4. Access via `http://localhost/System/frontend/login.php`

## File Structure

- `frontend/` - PHP/CSS/JS for UI
- `backend/` - PHP backend scripts
- `database/` - SQL initialization script

## Notes

- Update DB credentials in `backend/db.php` if needed.
- Extend backend logic for full CRUD and dashboard data as required.
