# ProdFlow — Production Management System

ProdFlow is a web-based management system designed for manufacturing and production companies. It provides a centralized platform to manage production workflows, inventory, sales, human resources, and financials — all in one place.

---

## Tech Stack

**Backend**
- PHP 8.2
- Laravel 12
- Laravel Sanctum (API authentication)
- MySQL / MariaDB

**Frontend**
- Vue.js
- Vite

---

## Features

| Module | Status |
|---|---|
| Companies | Done |
| Users & Authentication | Done |
| Staff / Employees | Done |
| Products | Done |
| Clients | Done |
| Sales | Done |
| Expenses | Done |
| Machines | In progress |
| Materials | In progress |
| Materials Stock | In progress |
| Production | In progress |
| Warehouses | Planned |
| Suppliers | Planned |
| Salaries | Planned |
| Contracts | Planned |
| Reports | Planned |

---

## Architecture

The backend follows a layered architecture pattern:

```
Request → Controller → Service → Repository → Database
```

- **Controller** — handles HTTP requests, input validation, and responses
- **Service** — contains business logic
- **Repository** — handles all database queries
- **Model** — represents database tables via Eloquent ORM

---

## Getting Started

### Requirements

- PHP >= 8.2
- Composer
- Node.js & npm
- MySQL or MariaDB

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/your-org/prodflow-backend.git
cd prodflow-backend
```

**2. Install dependencies**
```bash
composer install
npm install
```

**3. Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prodflow
DB_USERNAME=root
DB_PASSWORD=
```

**4. Run migrations**
```bash
php artisan migrate
```

**5. Start the development servers**

Backend:
```bash
php artisan serve
```

Frontend (in a separate terminal):
```bash
npm run dev
```

The API will be available at `http://127.0.0.1:8000` and the frontend at `http://localhost:5173`.

---

## API Overview

All API routes are prefixed with `/api/admin/`.

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/admin/clients` | List all clients |
| POST | `/api/admin/create_client` | Create a client |
| GET | `/api/admin/edit_client/{id}` | Get a client by ID |
| POST | `/api/admin/update_client` | Update a client |
| GET | `/api/admin/delete_client/{id}` | Delete a client |

The same pattern applies to: `companies`, `staff`, `products`, `sales`, `expenses`, `machines`, `materials`.

Authentication is handled via Laravel Sanctum. Protected routes require a Bearer token in the `Authorization` header.

---

## Environment Variables

| Variable | Description |
|---|---|
| `APP_KEY` | Laravel application key |
| `APP_ENV` | `local` or `production` |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |

---

## License

This project is proprietary software. All rights reserved.
