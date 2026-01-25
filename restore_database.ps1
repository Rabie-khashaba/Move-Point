# Database Restore Script for Laravel HR System
# Run this script to restore a backup of your MySQL database

param(
    [Parameter(Mandatory=$true)]
    [string]$BackupFile
)

Write-Host "Starting database restore..." -ForegroundColor Green

# Check if backup file exists
if (-not (Test-Path $BackupFile)) {
    Write-Host "Error: Backup file '$BackupFile' not found!" -ForegroundColor Red
    Write-Host "Please provide a valid backup file path." -ForegroundColor Yellow
    exit 1
}

# Check if Docker containers are running
$mysqlContainer = docker ps --filter "name=test-laravel-mysql" --format "{{.Names}}"
if (-not $mysqlContainer) {
    Write-Host "Error: MySQL container is not running!" -ForegroundColor Red
    Write-Host "Please start your Docker containers first with: docker-compose up -d" -ForegroundColor Yellow
    exit 1
}

Write-Host "Backup file: $BackupFile" -ForegroundColor Yellow
Write-Host "File size: $([math]::Round((Get-Item $BackupFile).Length / 1KB, 2)) KB" -ForegroundColor Cyan

# Confirm restore operation
Write-Host "`n⚠️  WARNING: This will overwrite your current database!" -ForegroundColor Red
$confirm = Read-Host "Are you sure you want to continue? (yes/no)"
if ($confirm -ne "yes") {
    Write-Host "Restore cancelled." -ForegroundColor Yellow
    exit 0
}

try {
    Write-Host "`nRestoring database..." -ForegroundColor Yellow
    
    # Restore the database
    Get-Content $BackupFile | docker exec -i test-laravel-mysql-1 mysql -u root -ppassword hr_system
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Database restore completed successfully!" -ForegroundColor Green
        Write-Host "Your database has been restored from: $BackupFile" -ForegroundColor Cyan
        
        # Clear Laravel caches after restore
        Write-Host "`nClearing Laravel caches..." -ForegroundColor Yellow
        docker exec test-laravel-laravel.test-1 php artisan optimize:clear
        docker exec test-laravel-laravel.test-1 php artisan optimize
        
        Write-Host "Laravel caches cleared and reoptimized!" -ForegroundColor Green
    } else {
        Write-Host "Database restore failed!" -ForegroundColor Red
        exit 1
    }
} catch {
    Write-Host "Error during restore: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host "`nRestore process completed!" -ForegroundColor Green
Write-Host "You may need to restart your application." -ForegroundColor Yellow
