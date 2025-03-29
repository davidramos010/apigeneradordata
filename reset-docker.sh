#!/bin/bash

# Detener y eliminar contenedores
docker-compose down -v

# Limpiar sistema Docker
docker system prune -af

# Eliminar imágenes
docker rmi $(docker images -q) -f

# Eliminar volúmenes
docker volume prune -f

# Eliminar redes
docker network prune -f

# Reiniciar Docker
sudo systemctl restart docker

# Esperar a que Docker esté listo
sleep 10

# Reconstruir y levantar
docker-compose build --no-cache
docker-compose up -d 