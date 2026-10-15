@echo off
echo ========================================================
echo   COE TRACK A - AUTOMATED SETUP SCRIPT (BOOTSTRAP)
echo ========================================================

:: 1. Semak fail .env backend
if not exist "src\backend\.env" (
    echo [1/4] Mencipta fail src\backend\.env daripada .env.example...
    copy "src\backend\.env.example" "src\backend\.env" >nul
) else (
    echo [1/4] Fail src\backend\.env sedia ada ditemui.
)

:: 2. Bina dan hidupkan container Docker di latar belakang
echo [2/4] Membina dan menghidupkan Docker containers...
docker compose up --build -d

:: 3. Tunggu seketika sehingga servis pangkalan data sedia
echo [3/4] Menunggu pangkalan data PostgreSQL sedia menerima sambungan...
timeout /t 5 /nobreak >nul

:: 4. Jalankan migration dan seeder secara automatik
echo [4/4] Menjalankan Migration dan Database Seeder...
docker compose exec backend php artisan key:generate --force
docker compose exec backend php artisan migrate --force
docker compose exec backend php artisan db:seed --force

echo.
echo ========================================================
echo   SETUP SELESAI DENGAN JAYA! PROJEK SEDIA DIGUNAKAN!
echo ========================================================
echo   - Frontend Catalog (Nuxt): http://localhost:3000
echo   - Backend API (Laravel):   http://localhost:8000/api/products
echo   - Database GUI (DBeaver):  localhost:5433 (coe_db / coe_user / secret)
echo ========================================================