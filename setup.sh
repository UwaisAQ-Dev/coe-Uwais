#!/bin/bash
set -e

echo "========================================================"
echo "  COE TRACK A - AUTOMATED SETUP SCRIPT (BOOTSTRAP)"
echo "========================================================"

# 1. Semak fail .env backend
if [ ! -f "src/backend/.env" ]; then
    echo "[1/4] Creating src/backend/.env from .env.example..."
    cp src/backend/.env.example src/backend/.env
else
    echo "[1/4] Existing src/backend/.env found."
fi

# 2. Build and start containers
echo "[2/4] Building and booting Docker containers..."
docker compose up --build -d

# 3. Wait for PostgreSQL
echo "[3/4] Waiting for PostgreSQL database to become healthy..."
sleep 5

# 4. Run migrations and seed
echo "[4/4] Running migrations and database seeder..."
docker compose exec backend php artisan key:generate --force
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan db:seed --force

echo ""
echo "========================================================"
echo "  SETUP COMPLETED SUCCESSFULLY! APPLICATION IS READY!"
echo "========================================================"
echo "  - Frontend Catalog (Nuxt): http://localhost:3000"
echo "  - Backend API (Laravel):   http://localhost:8000/api/products"
echo "  - Database GUI (DBeaver):  localhost:5433"
echo "========================================================"