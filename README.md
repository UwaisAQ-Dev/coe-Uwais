# 🛍️ COE Track A: Full-Stack E-Commerce System (Milestone 1: Foundation)

A full-stack e-commerce reference application built with **Nuxt.js (Frontend)**, **Laravel (Backend REST API)**, and **PostgreSQL (Database)**, fully containerized and orchestrated with **Docker Compose**.

Developed in accordance with the official **Centre of Excellence (COE) Learning Track A** specifications.

---

## 🏛️ System Architecture Overview

```text
               ┌──────────────────────────────────────────────┐
               │         Client Browser (Port 3000)           │
               └──────────────────────┬───────────────────────┘
                                      │
                                      ▼ (HTTP REST Requests)
┌─────────────────────────────────────────────────────────────────────────────┐
│                          Docker Compose Network                             │
│                                                                             │
│  ┌─────────────────────────┐               ┌─────────────────────────────┐  │
│  │   coe_frontend (Nuxt)   │               │    coe_backend (Laravel)    │  │
│  │   - Node.js 20 Alpine   │               │    - PHP 8.4 CLI            │  │
│  │   - SPA Mode            │               │    - Eloquent ORM           │  │
│  │   - Port 3000           │               │    - Port 8000              │  │
│  └─────────────────────────┘               └──────────────┬──────────────┘  │
│                                                           │                 │
│                                                           ▼ (Port 5432)     │
│                                            ┌─────────────────────────────┐  │
│                                            │      coe_db (PostgreSQL)    │  │
│                                            │      - PostgreSQL 16 Alpine │  │
│                                            │      - Host Port: 5433      │  │
│                                            │      - Volume: pgdata       │  │
│                                            └─────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────┘

📂 Repository Structure
text
coe-projek/
├── docker-compose.yml          # Multi-container orchestration (frontend, backend, db)
├── README.md                   # Complete system and per-component documentation
├── src/
│   ├── frontend/               # Nuxt.js client application (Vue 3 SPA)
│   └── backend/                # Laravel 12 API service (RESTful API)

📝 Per-Component Notes
1. Frontend (src/frontend)
Technology Stack: Nuxt 3/4, Vue 3 Composition API, Vite.
Rendering Strategy: Single Page Application (SPA) with ssr: false in nuxt.config.ts for clean client-to-API communication.
Key Features:
Product Catalog: Reactive table view with dynamic loading and error state handling.
Full CRUD: Real-time Create Product form, interactive Edit modal/flow, and Delete confirmation dialogs.
Search & Filter: Keyword search, multi-column sorting (price, date, name), and client/server pagination.
Category Integration: Dynamic dropdown populating available categories fetched live from the backend API.
Network Port: 3000 (http://localhost:3000).
2. Backend (src/backend)
Technology Stack: Laravel 12, PHP 8.4 CLI, Eloquent ORM.
Key Features:
RESTful Resource Controller: ProductController manages full CRUD endpoints with strict validation rules.
Eager Loading Optimization: Queries leverage Product::with('category') to eliminate N+1 query bottlenecks.
Consistent Error Shape: Global exception handler configured in bootstrap/app.php ensures all API errors return a standardized JSON envelope ({ "success": false, "message": "...", "errors": ... }) rather than framework-default HTML stack traces.
Network Port: 8000 (http://localhost:8000/api).
3. Database (coe_db)
Technology Stack: PostgreSQL 16 (Alpine Linux).
Data Modeling & Schema Normalization:
products: Stores product name, price, stock (inventory tracking), description, image URL, and foreign key category_id. Includes database indexes on name, price, and created_at for optimized query performance.
categories: Stores category metadata (name, slug, description).
orders & order_items: Normalized schema ready for Milestone 3 async order processing and price snapshotting.
users: User identity and account records.
Persistence: Database storage is persisted across container lifecycle restarts using Docker Volume pgdata.
Host Exposure: Port forwarded to 5433 on the host machine for visual inspection via database GUI clients (e.g., DBeaver, pgAdmin).
🚀 Quick Start Guide
Prerequisites
Docker Desktop installed and running with the WSL 2 backend on Windows.
Single-Command Boot

From the repository root (coe-projek/), start the entire stack:

bash
docker compose up --build
Database Seeding

Once the containers are up and healthy, populate the initial catalog data by executing the seeder once:

bash
docker compose exec backend php artisan db:seed
Accessing the Application
Frontend Catalog (Nuxt): http://localhost:3000
Backend API (Laravel): http://localhost:8000/api/products
Database GUI (DBeaver): Connect to localhost:5433 (Database: coe_db, User: coe_user, Password: secret).
📡 REST API Reference
HTTP Method	Endpoint	Description	Query Parameters
GET	/api/products	Retrieve paginated products with category relation	search, sort_by, order, page
POST	/api/products	Create a new product with input validation	None (Payload in JSON body)
GET	/api/products/{id}	Retrieve details of a specific product	None
PUT	/api/products/{id}	Update existing product details	None (Payload in JSON body)
DELETE	/api/products/{id}	Remove a product from the database	None
GET	/api/categories	Retrieve all available categories	None