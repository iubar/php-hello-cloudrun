#!/bin/bash

# Ottieni la directory dello script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Rimuove lo slash finale se presente
SCRIPT_DIR="${SCRIPT_DIR%/}"

PROJECT_ROOT="$SCRIPT_DIR/../../.."

IMAGE_NAME="hello-cloudrun-image"

CONTAINER_NAME="hello-cloudrun-container"

LOCAL_PORT=81