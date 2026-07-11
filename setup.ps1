$mysqlPath = "D:\backup\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe"
$phpPath = "D:\backup\laragon\bin\php\php-8.2.29-nts-Win32-vs16-x64\php.exe"

Write-Host "Creating database..." -ForegroundColor Green
& $mysqlPath -u root -e "CREATE DATABASE IF NOT EXISTS finance;"

Write-Host "Running migrations..." -ForegroundColor Green
cd D:\backup\laragon\www\finance-dashboard
& $phpPath artisan migrate --force

Write-Host "Done!" -ForegroundColor Green
