#!/bin/bash

# Script para inicializar Docker correctamente en sistemas con conflicto nf_tables/iptables

echo "🔧 Inicializando Docker..."

# Detener Docker
sudo systemctl stop docker
sleep 2

# Limpiar todas las reglas de iptables
echo "📋 Limpiando iptables..."
for table in filter nat mangle raw; do
  sudo iptables -F -t $table 2>/dev/null || true
  sudo iptables -X -t $table 2>/dev/null || true
  sudo iptables -P INPUT ACCEPT -t $table 2>/dev/null || true
  sudo iptables -P OUTPUT ACCEPT -t $table 2>/dev/null || true
  sudo iptables -P FORWARD ACCEPT -t $table 2>/dev/null || true
done

# Guardar las reglas (aunque estén vacías)
sudo iptables-save > /tmp/iptables.rules 2>/dev/null || true

# Iniciar Docker
echo "🚀 Reiniciando Docker..."
sudo systemctl start docker
sleep 5

# Verificar que Docker está corriendo
if sudo systemctl is-active --quiet docker; then
  echo "✅ Docker iniciado correctamente"
  exit 0
else
  echo "❌ Error: Docker no se inició"
  exit 1
fi

