#!/bin/bash

# Ottieni la directory dello script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Rimuove lo slash finale se presente
SCRIPT_DIR="${SCRIPT_DIR%/}"

echo "🛠️ ⚙️ Questo script si trova in: $SCRIPT_DIR"

# Includi config.sh se esiste
source "$SCRIPT_DIR/config.sh"

# Costruzione dell'immagine Docker

echo "🚀 📦 🐳 Building the image..."
echo "... ⏳ wait please ..."
docker build -t "${IMAGE_NAME}:latest" "$PROJECT_ROOT"

if [ $? -ne 0 ]; then
    echo "❌ Il comando è fallito!"
    exit 1
else
    echo "✅✔️👍 ...immagine creata con successo 🐳"
fi
