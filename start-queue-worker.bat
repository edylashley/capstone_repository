@echo off
echo ========================================================
echo   CSIT Research Library - Background Queue Worker
echo ========================================================
echo.
echo Starting the Laravel background worker...
echo This terminal will handle processing and sending all emails.
echo Leave this window open while testing the system!
echo.
php artisan queue:work
pause
