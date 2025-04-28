#!/bin/bash

# Ottieni la directory dello script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Rimuove lo slash finale se presente
SCRIPT_DIR="${SCRIPT_DIR%/}"

echo "🛠️ ⚙️ Questo script si trova in: $SCRIPT_DIR"

# Includi config.sh se esiste
source "$SCRIPT_DIR/config.sh"



echo "🚀 🐳 Starting the container... "
# Esegui il container in background (detached mode)
# -d = avvia in background (detached)
docker run -d --env-file ${PROJECT_ROOT}/.env -p $LOCAL_PORT:8080 --name "$CONTAINER_NAME" "${IMAGE_NAME}:latest"

if [ $? -ne 0 ]; then
    echo "❌ Il comando è fallito!"
    exit 1
else
    echo "✅✔️👍 ...container avviato con successo 🐳 "
fi

# Ottieni l'indirizzo IP locale (IPv4)
ip_address=$(hostname -I | awk '{print $1}')

echo "🌍 Indirizzo IP del server: $ip_address"
echo ""
echo "🌐 🖥️ Apri il browser all'indirizzo: http://$ip_address:$LOCAL_PORT"
echo ""