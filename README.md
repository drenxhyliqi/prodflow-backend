# ProdFlow — Production Management System

ProdFlow is a web-based ERP system built for manufacturing and production companies. It provides a centralized platform to manage production workflows, inventory, sales, human resources, financials, and analytics — all in one place.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.2 |
| Framework | Laravel 12 |
| Authentication | Laravel Sanctum |
| Database | MySQL / MariaDB |
| Cache | Redis (Upstash) via Predis |
| Queue | Database queue (Laravel Jobs) |
| AI | OpenAI API |
| Frontend | React + TypeScript + Vite |

---

## Modules

| Module | Status | Description |
|--------|--------|-------------|
| Companies | Done | Multi-company support with active company switching |
| Users & Authentication | Done | Sanctum token auth, login, user management, invitations |
| Staff / Employees | Done | Employee records management |
| Products | Done | Product catalogue with cache invalidation |
| Materials | Done | Raw material definitions |
| Materials Stock | Done | IN/OUT stock transactions with per-warehouse capacity enforcement |
| Warehouses | Done | Capacity tracking with dynamic usage bars |
| Machines | Done | Machine registry |
| Production | Done | Production batch logging |
| Planification | Done | Production planning with status tracking |
| Clients | Done | Client management |
| Sales | Done | Multi-product sales with invoice support |
| Expenses | Done | Expense tracking |
| Suppliers | Done | Supplier management |
| Salaries | Done | Employee salary records |
| Contracts | Done | Contract management with expiry tracking |
| Vacations | Done | Leave requests with approval workflow |
| Maintenances | Done | Machine maintenance logging |
| Trucks | Done | Fleet/truck management |
| Orders | Done | Order management with status tracking |
| Dashboard | Done | KPIs, charts, top products, recent activity |
| Production Report | Done | Output, efficiency, machines, top products, status distribution |
| Sales Report | Done | Revenue, orders, trends, top products, top clients |
| Reports Center | Done | Background batch generation for all report types |
| AI Assistant | Done | Chat, data analysis, and automated company alerts |

---

## Architecture

```
Request → Controller → Service → Repository → Database
```

- **Controller** — HTTP request handling, input validation, responses
- **Service** — business logic, calculations, cache management
- **Repository** — all database queries via Query Builder
- **Model** — Eloquent ORM table representations
- **Job** — background queue workers for long-running tasks

### Caching Strategy

- Reports and frequently-read data are cached in Redis (Upstash)
- Cache is invalidated on create / update / delete operations
- Each developer uses a unique `CACHE_PREFIX` in `.env` to avoid shared cache conflicts on Upstash

### Background Jobs

Report generation runs as background jobs via Laravel's database queue:

```
Frontend → POST /api/admin/reports/batch → ReportBatchService → GenerateReportRunJob (queued)
                                                                        ↓
                                                              queue:work processes job
                                                                        ↓
                                                          report_runs.result = JSON payload
```

---

## Getting Started

### Requirements

- PHP >= 8.2
- Composer
- MySQL or MariaDB
- Redis instance (local or Upstash cloud)

### Installation

**1. Clone the repository**
```bash
git clone https://github.com/your-org/prodflow-backend.git
cd prodflow-backend
```

**2. Install dependencies**
```bash
composer install
```

**3. Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Update `.env`**
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prodflow
DB_USERNAME=root
DB_PASSWORD=

# Redis (Upstash or local)
CACHE_STORE=redis
CACHE_PREFIX=your_name          # use a unique prefix per developer
REDIS_CLIENT=predis
REDIS_SCHEME=tls
REDIS_HOST=your-upstash-host
REDIS_PASSWORD=your-upstash-password
REDIS_PORT=6379

# Queue
QUEUE_CONNECTION=database

# OpenAI
OPENAI_API_KEY=your-key
```

**5. Run migrations**
```bash
php artisan migrate
```

**6. Create the first admin user**
```bash
php artisan tinker
# Inside tinker:
App\Models\UsersModel::create([
    'name'       => 'Admin',
    'surname'    => 'User',
    'username'   => 'admin',
    'password'   => bcrypt('password'),
    'role'       => 'admin',
    'company_id' => 1,
]);
```

**7. Start the development servers**

In three separate terminals:

```bash
# Terminal 1 — Laravel API server
php artisan serve

# Terminal 2 — Queue worker (required for report generation)
php artisan queue:work

# Terminal 3 — Frontend (if running frontend locally)
npm run dev
```

The API will be available at `http://127.0.0.1:8000`.

---

## API Reference

All protected routes require:
```
Authorization: Bearer {token}
```

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/login` | Login and receive token |
| POST | `/api/logout` | Revoke token |
| GET | `/api/me` | Get authenticated user |

### Core CRUD pattern
All core modules follow the same endpoint pattern (replace `{module}` with `clients`, `products`, `staff`, etc.):

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/{module}` | Paginated list with optional `?search=` |
| POST | `/api/admin/create_{module}` | Create record |
| GET | `/api/admin/edit_{module}/{id}` | Get single record |
| POST | `/api/admin/update_{module}` | Update record |
| GET | `/api/admin/delete_{module}/{id}` | Delete record |

Applies to: `companies`, `clients`, `staff`, `products`, `materials`, `machines`, `warehouses`, `suppliers`, `salaries`, `contracts`, `vacations`, `maintenances`, `trucks`, `orders`, `sales`, `expenses`, `planification`, `production`, `materials_stock`.

### Dashboard
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/dashboard` | Full dashboard payload (KPIs, charts, activity) |
| POST | `/api/admin/dashboard/clear-activity` | Clear recent activity (for testing) |

### Production Report
All endpoints accept `?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/reports/production/summary` | KPI summary |
| GET | `/api/admin/reports/production/trends` | Daily / weekly / monthly trends |
| GET | `/api/admin/reports/production/machines` | Machine performance |
| GET | `/api/admin/reports/production/top-products` | Top produced products |
| GET | `/api/admin/reports/production/status-distribution` | Plan status breakdown |

### Sales Report
All endpoints accept `?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD`

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/admin/reports/sales/summary` | Revenue, orders, growth vs previous period |
| GET | `/api/admin/reports/sales/trends` | Weekly/monthly revenue vs expenses |
| GET | `/api/admin/reports/sales/top-products` | Top products by revenue |
| GET | `/api/admin/reports/sales/top-clients` | Top clients by revenue |
| GET | `/api/admin/reports/sales/orders-overview` | Monthly orders with growth % |

### Reports Center (Batch)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/admin/reports/batch` | Create a new report batch |
| GET | `/api/admin/reports/batch/{id}` | Poll batch status |
| GET | `/api/admin/reports/run/{id}` | Get a completed run's data |

### AI Assistant
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/ai/chat` | General chat |
| POST | `/api/ai/chat-data` | Chat with company data context |
| POST | `/api/ai/analyze-text` | Analyze a text input |
| POST | `/api/ai/alerts` | Generate automated company alerts |

---

## Environment Variables

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Laravel application key |
| `APP_ENV` | `local` or `production` |
| `DB_HOST` | Database host |
| `DB_PORT` | Database port (default 3306) |
| `DB_DATABASE` | Database name |
| `DB_USERNAME` | Database user |
| `DB_PASSWORD` | Database password |
| `CACHE_STORE` | Cache driver — use `redis` |
| `CACHE_PREFIX` | Unique per-developer prefix for Redis keys |
| `REDIS_CLIENT` | Use `predis` |
| `REDIS_SCHEME` | `tls` for Upstash, `tcp` for local |
| `REDIS_HOST` | Redis / Upstash hostname |
| `REDIS_PASSWORD` | Redis / Upstash password |
| `REDIS_PORT` | Redis port (default 6379) |
| `QUEUE_CONNECTION` | Use `database` |
| `OPENAI_API_KEY` | OpenAI API key for AI features |

---

## License

This project is proprietary software. All rights reserved.
