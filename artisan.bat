@echo off
REM PHP avec extensions PostgreSQL (XAMPP). Utiliser: artisan.bat migrate
setlocal
set "PHP_EXE=C:\xampp\php\php.exe"
if not exist "%PHP_EXE%" (
  echo Modifiez PHP_EXE dans artisan.bat si XAMPP est ailleurs.
  exit /b 1
)
"%PHP_EXE%" "%~dp0artisan" %*
