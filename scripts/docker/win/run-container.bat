@ECHO OFF

SET SCRIPT_DIR=%~dp0

:: Rimuove lo slash finale se c'è (solo se è una backslash alla fine)
if "%SCRIPT_DIR:~-1%"=="\" set SCRIPT_DIR=%SCRIPT_DIR:~0,-1%

ECHO Questo script si trova in: %SCRIPT_DIR%

SETLOCAL

CALL %SCRIPT_DIR%\config.bat

:: Il flag -d nel comando docker run -d significa "detached mode", cioè esegui il container in background
docker run -d --env-file %PROJECT_ROOT%/.env -p %LOCAL_PORT%:8080 --name %CONTAINER_NAME% %IMAGE_NAME%:latest

IF %ERRORLEVEL% neq 0 (
    ECHO Il comando è fallito!
    EXIT /b 1
) ELSE (
    ECHO ...container avviato con successo
)
