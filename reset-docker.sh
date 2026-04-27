#!/bin/bash

# Detener todos los contenedores
sudo docker-compose down -v

# Eliminar todos los contenedores e imágenes
sudo docker system prune -af --volumes

# Eliminar archivos de red
sudo rm -rf /var/lib/docker/network/files/

# Reiniciar Docker
sudo systemctl restart docker

# Esperar a que Docker esté listo
sleep 5

# Reconstruir y levantar
sudo docker-compose build --no-cache
sudo docker-compose up -d 