@echo off
REM MediTrack Microservices - Run All Services
REM This script starts all microservices in separate console windows

setlocal enabledelayedexpansion

cd /d "d:\semester 6\APPL\UTS-RPL-APPL-11423018\uts\microservices"

echo.
echo ====================================================
echo   MediTrack - Starting All Microservices
echo ====================================================
echo.

set "SERVICES=api-gateway user-service appointment-service medical-service pharmacy-service payment-service analytics-service"
set SUCCESS=0
set FAILED=0

REM Step 1: Download dependencies for all services
echo [Step 1] Downloading Go dependencies for all services...
echo.

for %%S in (%SERVICES%) do (
    if exist "%%S\go.mod" (
        echo   [^>] %%S: go mod download...
        cd %%S
        call go mod download >nul 2>&1
        cd ..
    )
)

echo.
echo [Step 2] Starting all services...
echo.

REM Start each service in its own window
for %%S in (%SERVICES%) do (
    if exist "%%S\cmd\main.go" (
        echo   [^>] Starting %%S on the configured port...
        start "MediTrack - %%S" cmd /k "cd %%S && go run cmd/main.go"
        timeout /t 2 /nobreak >nul
        set /A SUCCESS+=1
    ) else (
        echo   [!] %%S: main.go not found!
        set /A FAILED+=1
    )
)

echo.
echo ====================================================
echo   Services Startup Summary
echo ====================================================
echo   Started: %SUCCESS%
echo   Failed:  %FAILED%
echo.
echo   Services will run in separate windows:
echo   - API Gateway:       http://localhost:3000
echo   - User Service:      http://localhost:3001
echo   - Appointment:       http://localhost:3002
echo   - Medical:           http://localhost:3003
echo   - Pharmacy:          http://localhost:3004
echo   - Payment:           http://localhost:3005
echo   - Analytics:         http://localhost:3006
echo.
echo   Each service has its own terminal window above.
echo   Close any window to stop that service.
echo.
echo   Check service health: http://localhost:3000/health
echo ====================================================
echo.

cd /d "d:\semester 6\APPL\UTS-RPL-APPL-11423018\uts\microservices"

REM Keep main window open for reference
pause
