@echo off
REM Скрипт для быстрого запуска приложения

echo ========================================
echo TimCockStore - Laravel Startup Script
echo ========================================
echo.

REM Проверка наличия php
php --version >nul 2>&1
if errorlevel 1 (
    echo Error: PHP not found. Please install PHP first.
    pause
    exit /b 1
)

REM Проверка наличия composer
composer --version >nul 2>&1
if errorlevel 1 (
    echo Error: Composer not found. Please install Composer first.
    pause
    exit /b 1
)

echo [1/4] Installing dependencies...
composer install --quiet
if errorlevel 1 (
    echo Error installing dependencies
    pause
    exit /b 1
)

echo [2/4] Running migrations...
php artisan migrate --quiet
if errorlevel 1 (
    echo Error running migrations
    pause
    exit /b 1
)

echo [3/4] Seeding database...
php artisan db:seed --quiet
if errorlevel 1 (
    echo Error seeding database
    pause
    exit /b 1
)

echo [4/4] Starting development server...
echo.
echo ========================================
echo Server is running at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php artisan serve --port=8000

pause

