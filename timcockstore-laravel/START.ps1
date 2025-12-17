# Скрипт для быстрого запуска приложения

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "TimCockStore - Laravel Startup Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Проверка наличия php
try {
    $phpVersion = php --version 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "PHP not found"
    }
} catch {
    Write-Host "Error: PHP not found. Please install PHP first." -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

# Проверка наличия composer
try {
    $composerVersion = composer --version 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "Composer not found"
    }
} catch {
    Write-Host "Error: Composer not found. Please install Composer first." -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}

Write-Host "[1/4] Installing dependencies..." -ForegroundColor Yellow
composer install --quiet
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error installing dependencies" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}
Write-Host "[1/4] Done" -ForegroundColor Green

Write-Host "[2/4] Running migrations..." -ForegroundColor Yellow
php artisan migrate --quiet
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error running migrations" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}
Write-Host "[2/4] Done" -ForegroundColor Green

Write-Host "[3/4] Seeding database..." -ForegroundColor Yellow
php artisan db:seed --quiet
if ($LASTEXITCODE -ne 0) {
    Write-Host "Error seeding database" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit 1
}
Write-Host "[3/4] Done" -ForegroundColor Green

Write-Host "[4/4] Starting development server..." -ForegroundColor Yellow
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Server is running at: http://localhost:8000" -ForegroundColor Green
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

php artisan serve --port=8000

