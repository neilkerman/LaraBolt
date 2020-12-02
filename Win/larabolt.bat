@echo off
SET command=%1
SET args=%2
SET callpath=%cd%
php %~dp0/larabolt.php %command% %args%
echo.
echo.
pause