@echo off
REM Colors using enhanced console output
REM Title
cls
echo.
echo ====================================================
echo   MediTrack Database Seeding - All Services
echo ====================================================
echo.

setlocal enabledelayedexpansion

REM Define services
set "SERVICES=user-service appointment-service pharmacy-service medical-service payment-service analytics-service"

REM Counters
set SUCCESS=0
set FAILED=0

REM Run each seeder
for %%S in (%SERVICES%) do (
    echo [*] Starting seeding for %%S...
    
    if exist "%%S\" (
        if exist "%%S\cmd\seeder" (
            cd %%S
            
            echo   [^>] Downloading dependencies...
            go mod tidy
            
            if errorlevel 1 (
                echo [X] Failed to download dependencies for %%S
                set /a FAILED+=1
            ) else (
                echo   [^>] Building seeder...
                go build -o seeder.exe cmd\seeder\main.go
                
                if errorlevel 1 (
                    echo [X] Build failed for %%S
                    set /a FAILED+=1
                ) else (
                    echo   [^>] Running seeder...
                    seeder.exe
                    
                    if errorlevel 1 (
                        echo [X] %%S seeding failed
                        set /a FAILED+=1
                    ) else (
                        echo [OK] %%S seeding completed
                        set /a SUCCESS+=1
                    )
                    
                    if exist "seeder.exe" del seeder.exe
                )
            )
            
            cd ..
        ) else (
            echo [X] Error: cmd\seeder directory not found
            set /a FAILED+=1
        )
    ) else (
        echo [X] Error: %%S directory not found
        set /a FAILED+=1
    )
    echo.
)

REM Summary
echo.
echo ====================================================
echo   Seeding Summary
echo ====================================================
echo   Success: !SUCCESS!/6
echo   Failed:  !FAILED!/6
echo ====================================================
echo.

if %FAILED% equ 0 (
    echo [OK] All services seeded successfully!
    exit /b 0
) else (
    echo [ERROR] Some services failed to seed
    exit /b 1
)
