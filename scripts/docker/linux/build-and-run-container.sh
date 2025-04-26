#!/bin/bash

# Ottieni la directory dello script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Rimuove lo slash finale se presente
SCRIPT_DIR="${SCRIPT_DIR%/}"

echo "🛠️ ⚙️ Questo script si trova in: $SCRIPT_DIR"

source $SCRIPT_DIR/build-image.sh

source $SCRIPT_DIR/build-image.sh

source $SCRIPT_DIR/run-container.sh