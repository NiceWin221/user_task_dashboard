# 🗂️ User & Task Management API - Laravel 12

RESTful API sederhana untuk manajemen user & task berbasis role (admin/user).

---

## ⚙️ Cara Menjalankan

```bash
git clone https://github.com/NiceWin221/user_task_dashboard.git && cd user_task_dashboard
composer install && cp .env.example .env && php artisan key:generate
# atur DB_CONNECTION, DB_DATABASE, DB_USERNAME, DB_PASSWORD di .env
php artisan migrate --seed && php artisan serve
