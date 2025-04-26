#!/bin/bash

# Ottieni la directory dello script
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# Rimuove lo slash finale se presente
SCRIPT_DIR="${SCRIPT_DIR%/}"

echo "Questo script si trova in: $SCRIPT_DIR"

# Includi il file di configurazione (dove viene definito CONTAINER_NAME)
source "$SCRIPT_DIR/config.sh"

# Se vuoi chiedere all'utente di inserire manualmente un nome o ID del container:
# read -p "Inserisci il nome o ID del container da fermare ed eliminare: " CONTAINER_NAME

# Ferma il container
echo " 🐳 Fermo il container: $CONTAINER_NAME..."
docker stop "$CONTAINER_NAME"

if [ $? -ne 0 ]; then
    echo "❌ Il comando è fallito!"
    exit 1
else
    echo "...OK ✅"
fi

# Elimina il container
echo "🐳 Elimino il container: $CONTAINER_NAME..."
docker rm "$CONTAINER_NAME"

if [ $? -ne 0 ]; then
    echo "❌ Il comando è fallito!"
    exit 1
else
    echo "...OK ✅"
fi

# Pausa opzionale (simile a PAUSE in batch)
# read -p "Premi invio per uscire..."
