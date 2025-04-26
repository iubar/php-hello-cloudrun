@ECHO OFF

SET SCRIPT_DIR=%~dp0

:: Rimuove lo slash finale se c'è (solo se è una backslash alla fine)
if "%SCRIPT_DIR:~-1%"=="\" set SCRIPT_DIR=%SCRIPT_DIR:~0,-1%

ECHO Questo script si trova in: %SCRIPT_DIR%

SETLOCAL

CALL %SCRIPT_DIR%\config.bat

:: Chiedi all'utente il nome o ID del container
:: set /p CONTAINER_NAME=Inserisci il nome o ID del container da fermare ed eliminare: 

:: Ferma il container
ECHO Fermo il container: %CONTAINER_NAME%...
docker stop %CONTAINER_NAME%

IF %ERRORLEVEL% neq 0 (
    ECHO Il comando è fallito!
    EXIT /b 1
) ELSE (
    ECHO ...OK
)

:: Elimina il container
ECHO Elimino il container: %CONTAINER_NAME%...
docker rm %CONTAINER_NAME%

IF %ERRORLEVEL% neq 0 (
    ECHO Il comando è fallito!
    EXIT /b 1
) ELSE (
    ECHO ...OK
)

PAUSE
