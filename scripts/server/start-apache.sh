#!/bin/bash

# Opzionale: mostra qualche info utile nei log
echo ">>> Avvio del container PHP con Apache su porta $PORT"

# Puoi inserire qui eventuali altre inizializzazioni (es: php artisan, symfony console, ecc.)

# Lancia Apache in foreground (richiesto da Cloud Run)
exec apache2-foreground
