#!/usr/bin/env bash
# Busca secretos en los archivos versionados (el repositorio es público).
# Imprime solo archivo y línea, nunca el valor encontrado. Sale con 1 si hay algo.
set -euo pipefail

patrones=(
  'EAA[A-Za-z0-9]{40,}'                 # token de Meta (Facebook)
  'IGAA[A-Za-z0-9_-]{40,}'              # token de Instagram
  'IGQ[A-Za-z0-9_-]{40,}'               # token antiguo de Instagram
  'AKIA[0-9A-Z]{16}'                    # AWS
  'AIza[0-9A-Za-z_-]{35}'               # clave de API de Google
  'gh[pousr]_[A-Za-z0-9]{36,}'          # GitHub
  'github_pat_[A-Za-z0-9_]{60,}'        # GitHub (fino)
  'xox[baprs]-[A-Za-z0-9-]{10,}'        # Slack
  '-----BEGIN [A-Z ]*PRIVATE KEY-----'  # llaves privadas
)

encontrado=0
for patron in "${patrones[@]}"; do
  # -l: solo el nombre del archivo; el valor nunca se muestra.
  if coincidencias=$(git ls-files -z | xargs -0 grep -lIE -- "$patron" 2>/dev/null | grep -v '^scripts/revisar-secretos.sh$'); then
    if [ -n "$coincidencias" ]; then
      echo "Posible secreto (patrón ${patron:0:12}…) en:"
      echo "$coincidencias" | sed 's/^/  - /'
      encontrado=1
    fi
  fi
done

if [ "$encontrado" -eq 1 ]; then
  echo "Revisa esos archivos: si es un secreto real, revócalo y bórralo del historial."
  exit 1
fi
echo "Sin secretos en los archivos versionados."
