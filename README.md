# ProdFlow — Production Management System

ProdFlow is a web-based management system designed for manufacturing and production companies. It provides a centralized platform to manage production workflows, inventory, sales, human resources, and financials — all in one place.

---

## Tech Stack

**Backend**
- PHP 8.2
- Laravel 12
- Laravel Sanctum (API authentication)
- MySQL / MariaDB
- Redis (cache & optional queue)
- Predis

**Frontend**
- Vue.js
- Vite

---

## Features

| Module | Description |
|---|---|
| Dashboard | KPIs, activity feed, and company overview |
| Companies | Multi-tenant company setup and settings |
| Users & Authentication | Login, roles, invitations (Sanctum) |
| Staff / Employees | Employee records and management |
| Clients | Customer registry and CRUD |
| Products | Product catalog and pricing |
| Materials | Raw materials and definitions |
| Materials Stock | Stock levels, movements, and inventory |
| Production | Production orders and workflow |
| Machines | Machine registry and capacity |
| Maintenances | Machine maintenance scheduling |
| Warehouses | Warehouse locations and stock |
| Suppliers | Supplier contacts and procurement |
| Trucks | Fleet / delivery vehicles |
| Sales | Sales records and invoicing |
| Orders | Order management linked to sales |
| Expenses | Company expenses tracking |
| Planification | Production planning and scheduling |
| Vacations | Staff leave requests and approval |
| Contracts | Employment and business contracts |
| Salaries | Payroll and salary records |
| Reports | Batch reports, stock overview, async generation |
| Production Reports | Production analytics (summary, trends, machines) |
| Sales Reports | Sales analytics (summary, trends, top clients/products) |
| AI Assistant | Chat, data queries, text analysis, alerts |

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
- Redis (recommended for production cache)


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
| `CACHE_STORE` | Cache driver (`redis` in production) |
| `REDIS_HOST` | Redis host |
| `REDIS_PORT` | Redis port |
| `QUEUE_CONNECTION` | Queue driver (`database` or `redis`) |

---

## License

This project is proprietary software. All rights reserved.
