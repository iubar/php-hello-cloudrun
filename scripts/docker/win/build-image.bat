@ECHO OFF

SET SCRIPT_DIR=%~dp0

:: Rimuove lo slash finale se c'è (solo se è una backslash alla fine)
if "%SCRIPT_DIR:~-1%"=="\" set SCRIPT_DIR=%SCRIPT_DIR:~0,-1%

ECHO Questo script si trova in: %SCRIPT_DIR%

SETLOCAL

CALL %SCRIPT_DIR%\config.bat

docker build -t %IMAGE_NAME%:latest %PROJECT_ROOT%

PAUSE