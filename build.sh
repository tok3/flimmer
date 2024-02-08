#!/bin/bash

# Führe das Build-Skript aus, das in deiner package.json-Datei definiert ist
echo "Starte den Build-Prozess..."
npm run build

# Überprüfe, ob der Build erfolgreich war
if [ $? -eq 0 ]; then
  echo "Build erfolgreich."
else
  echo "Build fehlgeschlagen."
fi
