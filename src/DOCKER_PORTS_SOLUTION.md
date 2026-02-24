# Solución: Problema de Puertos Ocupados en Docker

## 📋 El Problema

Cada vez que reiniciabas tu PC, los puertos 8001 y 3311 estaban ocupados por procesos `docker-proxy` huérfanos, requiriendo cambiar los puertos en `docker-compose.yml`.

### Causa Raíz

El sistema tiene un conflicto entre **nf_tables** e **iptables** que impide que Docker cree redes normales. Esto causaba:

1. Errores al crear redes de Docker compose
2. Procesos docker-proxy quedaban en estado zombie
3. Puertos bloqueados después de apagar Docker

## ✅ Solución Implementada

Se cambió la configuración de Docker Compose a usar `network_mode: host` para ambos servicios:

```yaml
services:
  mysql:
    network_mode: host
  
  app:
    network_mode: host
    environment:
      DB_HOST: 127.0.0.1      # Usa localhost del host
      DB_PORT: 3311            # Puerto expuesto directamente
```

### Ventajas

- ✅ **Sin conflictos de iptables**: No crea redes Docker que requieren iptables
- ✅ **Puertos permanentes**: 8001 (API) y 3311 (MySQL) siempre disponibles
- ✅ **Sin procesos zombie**: Los puertos se liberan correctamente al apagar
- ✅ **Mejor rendimiento**: Network mode host tiene menor overhead

### Desventajas (mínimas)

- Los puertos se mapean directamente al host (no un problema para desarrollo)
- Los contenedores comparten el namespace de red del host

## 🚀 Uso

Simplemente levanta los contenedores normalmente:

```bash
cd /home/david/Documentos/desarrollo/apigeneradordata/src
docker-compose up -d --build
```

**La API estará disponible en:** `http://localhost:8001`  
**MySQL estará disponible en:** `localhost:3311`

## 🔧 Si tienes problemas aún

Si los puertos siguen bloqueados después de un reinicio (por procesos huérfanos), ejecuta:

```bash
bash docker-init.sh
```

Este script limpia iptables y reinicia Docker correctamente.

## 📝 Archivo de Configuración

Ver [docker-compose.yml](docker-compose.yml) para la configuración exacta.
