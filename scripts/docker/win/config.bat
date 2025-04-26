@echo off

SET SCRIPT_DIR=%~dp0

:: Rimuove lo slash finale se c'è (solo se è una backslash alla fine)
if "%SCRIPT_DIR:~-1%"=="\" set SCRIPT_DIR=%SCRIPT_DIR:~0,-1%

SET PROJECT_ROOT=%SCRIPT_DIR%\..\..\..

SET IMAGE_NAME=hello-cloudrun-image
SET CONTAINER_NAME=hello-cloudrun-container

SET LOCAL_PORT=81