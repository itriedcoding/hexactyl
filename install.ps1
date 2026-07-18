<# 
.SYNOPSIS
    Hexactyl Panel - Quick Install Script for Windows
.DESCRIPTION
    Installs Hexactyl Panel on Windows with all dependencies.
.NOTES
    Run as Administrator: PowerShell -ExecutionPolicy Bypass -File install.ps1
.LINK
    https://github.com/Hexactyl-Projects/hexactyl
    https://hexactyl-docs.vercel.app
#>

$ErrorActionPreference = "Stop"

$PanelDir = "C:\hexactyl"
$DbName = "hexactyl"
$DbUser = "hexactyl"
$DbPass = -join ((1..16) | ForEach-Object { "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789".ToCharArray() | Get-Random })
$AppKey = [Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Minimum 0 -Maximum 256 }))

function Write-Banner {
    Write-Host ""
    Write-Host "  _   _  ____  ____  ____  _  __" -ForegroundColor Orange
    Write-Host " | | | |/ ___||  _ \|  _ \| |/" -ForegroundColor Orange
    Write-Host " | |_| | |    | |_) | | | | ' / " -ForegroundColor Orange
    Write-Host " |  _  | |___ |  __/| |_| | . \ " -ForegroundColor Orange
    Write-Host " |_| |_|\____||_|   |____/|_|\_\" -ForegroundColor Orange
    Write-Host ""
    Write-Host "  Game Server Management Panel" -ForegroundColor Cyan
    Write-Host "  https://hexactyl-docs.vercel.app" -ForegroundColor Cyan
    Write-Host ""
}

function Test-Admin {
    $identity = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($identity)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

function Install-Prerequisites {
    Write-Host "[...] Installing prerequisites..." -ForegroundColor Yellow
    
    # Install Chocolatey if not present
    if (!(Get-Command choco -ErrorAction SilentlyContinue)) {
        Set-ExecutionPolicy Bypass -Scope Process -Force
        [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
        iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
    }
    
    # Install packages
    choco install -y git php composer nodejs nginx mysql
    
    Write-Host "[OK] Prerequisites installed" -ForegroundColor Green
}

function Set-Database {
    Write-Host "[...] Configuring database..." -ForegroundColor Yellow
    
    $mysqlPath = "C:\tools\mysql\current\bin\mysql.exe"
    
    & $mysqlPath -u root -e "CREATE DATABASE IF NOT EXISTS $DbName;"
    & $mysqlPath -u root -e "CREATE USER IF NOT EXISTS '$DbUser'@'localhost' IDENTIFIED BY '$DbPass';"
    & $mysqlPath -u root -e "GRANT ALL PRIVILEGES ON $DbName.* TO '$DbUser'@'localhost';"
    & $mysqlPath -u root -e "FLUSH PRIVILEGES;"
    
    Write-Host "[OK] Database configured" -ForegroundColor Green
}

function Install-Panel {
    Write-Host "[...] Downloading Hexactyl..." -ForegroundColor Yellow
    
    git clone https://github.com/Hexactyl-Projects/hexactyl.git $PanelDir
    Set-Location $PanelDir
    
    Write-Host "[...] Installing PHP dependencies..." -ForegroundColor Yellow
    composer install --no-dev --optimize-autoloader
    
    Write-Host "[...] Installing Node.js dependencies..." -ForegroundColor Yellow
    npm install
    
    Write-Host "[...] Configuring environment..." -ForegroundColor Yellow
    Copy-Item .env.example .env
    php artisan key:generate --force
    
    (Get-Content .env) `
        -replace 'DB_DATABASE=panel', "DB_DATABASE=$DbName" `
        -replace 'DB_USERNAME=hexactyl', "DB_USERNAME=$DbUser" `
        -replace 'DB_PASSWORD=', "DB_PASSWORD=$DbPass" `
        -replace 'APP_URL=http://panel.example.com', "APP_URL=http://localhost" |
        Set-Content .env
    
    Write-Host "[...] Running migrations..." -ForegroundColor Yellow
    php artisan migrate --force
    php artisan db:seed --force
    
    Write-Host "[...] Building assets..." -ForegroundColor Yellow
    npm run build:production
    
    Write-Host "[...] Creating storage link..." -ForegroundColor Yellow
    php artisan storage:link
    
    Write-Host "[OK] Panel installed" -ForegroundColor Green
}

function Show-Summary {
    Write-Host ""
    Write-Host "============================================================" -ForegroundColor Green
    Write-Host "  Hexactyl Panel Installed Successfully!" -ForegroundColor Green
    Write-Host "============================================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "  Panel URL:       http://localhost" -ForegroundColor Yellow
    Write-Host "  Panel Path:      $PanelDir" -ForegroundColor Yellow
    Write-Host "  Database:        $DbName" -ForegroundColor Yellow
    Write-Host "  DB User:         $DbUser" -ForegroundColor Yellow
    Write-Host "  DB Password:     $DbPass" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  Create admin user:" -ForegroundColor Cyan
    Write-Host "    cd $PanelDir" -ForegroundColor White
    Write-Host "    php artisan p:user:make" -ForegroundColor White
    Write-Host ""
    Write-Host "  Documentation:" -ForegroundColor Cyan
    Write-Host "    https://hexactyl-docs.vercel.app" -ForegroundColor White
    Write-Host ""
    Write-Host "============================================================" -ForegroundColor Green
    
    # Save credentials
    @"
Database: $DbName
DB User: $DbUser
DB Pass: $DbPass
"@ | Out-File "$env:USERPROFILE\Desktop\hexactyl-credentials.txt"
    
    Write-Host "`n  Credentials saved to: Desktop\hexactyl-credentials.txt" -ForegroundColor Yellow
}

# Run
Write-Banner
if (!(Test-Admin)) {
    Write-Host "[ERROR] Run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell -> Run as Administrator" -ForegroundColor Yellow
    pause
    exit 1
}
Install-Prerequisites
Set-Database
Install-Panel
Show-Summary
