<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# HR Management & Dynamic Forms API (Laravel 12)

This project is a **Laravel 12 REST API** that provides:

- JWT-based authentication
- Role & permission management
- User management
- Dynamic form creation
- Form submissions
- Excel import/export of submissions

The project supports **Laravel Sail (Docker)** and is designed to be consumed by frontend or mobile clients.

---

## Requirements

- WSL2 (Ubuntu recommended)
- PHP 8.2+
- Composer
- Docker & Docker Compose
- Laravel 12

---

## Installation & Setup

All commands should be run inside **WSL2 bash**.

### 1. Clone the Repository
```bash
git clone <repository-url>
cd <project-folder>
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Create Environment File
```bash
cp .env.example .env
```

### 4. Environment Configuration

Only If you are using **Laravel Sail**, update your `.env` file else ignore it:

```env
APP_SERVICE="hr.api"
DB_HOST="hr.mysql"
REDIS_HOST="hr.redis"
```

### 5. Cache Configuration

```env
CACHE_STORE=array
```

> Cache tagging is used. File and database cache drivers are not supported for cache tagging. You can use redis also.

### 6. Generate Application Key
```bash
php artisan key:generate
```

### 7. Install Laravel Sail
```bash
php artisan sail:install
```

### 8. Generate JWT Secret Key
```bash
php artisan jwt:secret
```

---

## Laravel Sail Setup (Optional)

### Add Sail Alias
```bash
nano ~/.bashrc
```

Add:
```bash
alias sail='[ -f sail ] && bash sail || bash ./vendor/bin/sail'
```

Restart terminal after saving.

### Build Docker Images
```bash
sail build --no-cache
```

### Start Containers
```bash
sail up -d
```

---

## Database Migration & Seeding

### Run Migrations
```bash
php artisan migrate
# or if you are using sail then
sail artisan migrate
```

### Run Module Seeders
```bash
php artisan module:seed User
php artisan module:seed DynamicForm
```

> Seed `User` module first, then `DynamicForm`. Otherwise it will throw error.

---

## Default User Credentials

| Role | Email | Password |
|-----|------|----------|
| Admin | admin@company.com | password |
| HR | hr@company.com | password |
| Employee | employee@company.com | password |

---

## Authentication

JWT authentication is used.

```http
Authorization: Bearer <token>
```

---

## API Endpoints

### Authentication

| Endpoint | Method | Role | Description |
|--------|--------|------|-------------|
| /api/auth/login | POST | All | Login |
| /api/auth/refresh | POST | All | Refresh token |
| /api/auth/logout | POST | All | Logout |

### Users

| Endpoint | Method | Role | Description |
|--------|--------|------|-------------|
| /api/users | GET/POST | Admin | List / create users |
| /api/users/{id} | GET/PATCH/DELETE | Admin | Manage user |
| /api/users/password | POST | All | Update password |

### Roles & Permissions

| Endpoint | Method | Role | Description |
|--------|--------|------|-------------|
| /api/roles | GET/POST | Admin | List / create roles |
| /api/roles/{role} | PATCH/DELETE | Admin | Edit / delete roles |
| /api/permissions | GET | Admin | List permissions |

### Forms

| Endpoint | Method | Role | Description |
|--------|--------|------|-------------|
| /api/forms | GET | All | List forms |
| /api/forms | POST | Admin, HR | Create form |
| /api/forms/{form} | GET | All | View form |
| /api/forms/{form} | PATCH | Admin, HR | Update form |
| /api/forms/{form} | DELETE | Admin, HR | Delete form |
| /api/forms/{form}/publish | POST | Admin, HR | Publish form |

### Fields

| Endpoint | Method | Role | Description |
|--------|--------|------|-------------|
| /api/forms/{form}/fields | GET | All | List fields |
| /api/forms/{form}/fields | POST | Admin, HR | Add field |
| /api/forms/{form}/fields/{field} | PATCH | Admin, HR | Update field |
| /api/forms/{form}/fields/{field} | DELETE | Admin, HR | Delete field |

### Submissions

| Endpoint | Method | Role | Description |
|--------|--------|------|-------------|
| /api/forms/{form}/submissions | GET | Admin, HR | List submissions |
| /api/forms/{form}/submissions | POST | Employee | Create submission |
| /api/submissions/{submission} | GET | Owner/Admin/HR | View submission |
| /api/forms/{form}/submissions/{submission} | PATCH | Owner | Update submission |

### Import / Export

| Endpoint | Method | Role | Description |
|--------|--------|------|-------------|
| /api/forms/{form}/export | GET | Admin, HR | Export Excel |
| /api/forms/{form}/import | POST | Admin, HR | Import Excel |

---

### Permissions & Policies

- Permissions are currently enforced for viewing and updating submissions
- Laravel Policies are used to ensure:
    - Users can only view/update their own submissions
    - Admin and HR can view all submissions

___

## Excel Import Template

A sample Excel file is available at:
```
public/form_template.xlsx
```

**Format**
- First column: Employee Email
- Remaining columns: Form field names
- Email must belong to a registered employee

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
