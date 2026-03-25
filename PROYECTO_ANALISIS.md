# 📋 Análisis Completo: API Generador Data

**Fecha de Análisis**: 25 de Marzo de 2026  
**Versión del Proyecto**: 1.0  
**Idioma**: ES

---

## 📌 Descripción General

**API Generador Data** es un servicio RESTful backend construido con **Laravel 12** que proporciona:

- ✅ **Generación de documentos españoles válidos** (DNI, NIE, CIF, NIF, SSN)
- ✅ **Gestión de productos** (CRUD completo)
- ✅ **Autenticación y autorización** con tokens JWT
- ✅ **Control de acceso basado en roles** (admin/user)
- ✅ **Totalmente containerizado** con Docker

**Caso de uso principal**: Backend API para testing, generación de datos de prueba y gestión de productos con acceso controlado mediante autenticación JWT.

---

## 🛠 Stack Tecnológico

| Componente | Tecnología | Versión |
|-----------|-----------|---------|
| **Framework Backend** | Laravel | 12 (PHP 8.2) |
| **Autenticación** | JWT (tymon/jwt-auth) | - |
| **Base de Datos** | MySQL | 8.0 |
| **ORM** | Eloquent | Incluido en Laravel |
| **Frontend Build** | Vite + Tailwind CSS | 4 |
| **Containerización** | Docker & Docker Compose | - |
| **Testing** | PHPUnit, Mockery | - |
| **Gestor de Paquetes** | Composer (PHP), npm (JS) | - |
| **Validación/Código** | Laravel Pint, Scribe Docs | - |

---

## 📦 Servicios Disponibles

### 1. **Autenticación** (`/api/auth`)
- `POST /api/register` - Registro de nuevos usuarios
- `POST /api/login` - Login y obtención de token JWT
- `POST /api/logout` - Logout e invalidación de token
- `GET /api/me` - Obtener información del usuario actual

### 2. **Generación de Documentos Españoles** (`/api/generate-*`)
Genera números válidos con los algoritmos oficiales:
- `GET /api/generate-dni` - Generar DNI válido
- `GET /api/generate-nie` - Generar NIE válido
- `GET /api/generate-cif` - Generar CIF válido
- `GET /api/generate-nif` - Generar NIF válido
- `GET /api/generate-ssn` - Generar SSN válido

### 3. **Gestión de Productos** (`/api/product`)
- `GET /api/products` - Listar todos los productos
- `POST /api/product` - Crear producto (admin)
- `GET /api/product/{id}` - Obtener producto por ID (admin)
- `PUT /api/product/{id}` - Actualizar producto (admin)
- `DELETE /api/product/{id}` - Eliminar producto (admin)

---

## 🏗 Estructura del Proyecto

```
apigeneradordata/
│
├── src/                              # Aplicación Laravel principal
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/          # Controladores API
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── ProductController.php
│   │   │   │   └── GenerateDocumentController.php
│   │   │   └── Middleware/           # Middleware de autenticación
│   │   │       ├── IsUserAuth.php    # Verifica JWT válido
│   │   │       └── IsAdmin.php       # Verifica rol admin
│   │   ├── Models/                   # Modelos Eloquent
│   │   │   ├── User.php              # Modelo Usuario (con JWT)
│   │   │   ├── Product.php           # Modelo Producto
│   │   │   └── GenerateDocument.php  # Generador de documentos
│   │   └── Exceptions/
│   │
│   ├── config/                       # Configuraciones
│   │   ├── jwt.php                   # Configuración JWT (TTL, algoritmo)
│   │   ├── auth.php                  # Guards de autenticación
│   │   ├── database.php              # Conexión MySQL
│   │   └── [...]
│   │
│   ├── database/
│   │   ├── migrations/               # Migraciones BD
│   │   │   ├── users_table.php
│   │   │   └── products_table.php
│   │   ├── seeders/                  # Seeders para datos iniciales
│   │   └── factories/
│   │
│   ├── helpers/
│   │   └── DocumentHelper.php        # Funciones generación documentos
│   │
│   ├── routes/
│   │   ├── api.php                   # Rutas API (protegidas y públicas)
│   │   └── web.php                   # Rutas web
│   │
│   ├── resources/
│   │   ├── views/                    # Plantillas Blade
│   │   └── css/js/                   # Assets frontend
│   │
│   ├── storage/                      # Logs, cache, uploads
│   ├── tests/                        # Tests PHPUnit
│   │
│   ├── Dockerfile                    # Configuración Docker (PHP-FPM)
│   ├── docker-compose.yml            # Orquestación MySQL + Laravel
│   ├── composer.json                 # Dependencias PHP
│   ├── package.json                  # Dependencias npm
│   ├── vite.config.js                # Configuración build frontend
│   ├── .env                          # Variables de entorno
│   └── README.md                     # Documentación
```

---

## 🔑 Detalles Técnicos

### Autenticación JWT
- **Algoritmo**: HS256 (HMAC SHA-256)
- **TTL Token**: 60 minutos
- **TTL Refresh**: 20,160 minutos (~14 días)
- **Blacklist Habilitada**: Sí (para logout seguro)
- **Guard**: Configurado como `api` con driver `jwt`

### Control de Acceso Basado en Roles
```
- admin  → Acceso completo (CRUD productos + generar docs)
- user   → Solo generar documentos + ver productos
- public → Solo login/register
```

### Esquema de Base de Datos

#### Tabla `users`
```sql
- id (PK, incremental)
- name (string)
- email (unique, string)
- role (string: 'admin' | 'user')
- password (string, hashed)
- created_at, updated_at (timestamps)
```

#### Tabla `products`
```sql
- id (PK, incremental)
- name (string, max 100)
- price (decimal: 8,2)
- created_at, updated_at (timestamps)
```

### Configuración Docker

**MySQL (Servicio BD)**
- Imagen: `mysql:8.0`
- Puerto Host: `3310` → Container `3306`
- Database: `laravel_db`
- Usuario: `laravel` / Contraseña: `secret`
- Root Password: `root`
- Timezone: `Europe/Madrid`

**Laravel App (Servicio API)**
- Imagen: PHP 8.2-FPM (Dockerfile custom)
- Puerto Host: `8000` → Container `8000`
- Comando: `php artisan serve --host=0.0.0.0 --port=8000`
- Volumen: `./ → /var/www/html` (desarrollo en vivo)
- JWT Key: `base64:45htVXDiQi9Dg6MtTd+MiUlVoko01W0jyFtApyI2gNs=`

---

## 🚀 Guía de Inicio con Docker

### Requisitos Previos
- Docker y Docker Compose instalados
- Git (para clonar el proyecto)
- Al menos 2GB de RAM disponible
- Puertos `8000` y `3310` disponibles

### Paso 1: Preparación del Proyecto

```bash
# 1. Navegar al directorio del proyecto
cd /home/david/Documentos/desarrollo/apigeneradordata

# 2. Verificar que docker-compose.yml existe
ls -la docker-compose.yml
```

### Paso 2: Configurar Variables de Entorno

```bash
# 1. Copiar archivo .env si no existe
cp .env.example .env 2>/dev/null || echo "Usando .env existente"

# 2. Editar .env (valores importantes)
# Asegurar que estos valores coinciden con docker-compose.yml:
# DB_HOST=mysql
# DB_PORT=3306
# DB_DATABASE=laravel_db
# DB_USERNAME=laravel
# DB_PASSWORD=secret
# APP_DEBUG=true
# APP_ENV=local
```

### Paso 3: Construir e Iniciar Contenedores

```bash
# 1. Construir las imágenes Docker (primera vez)
docker-compose build

# 2. Iniciar los servicios en background
docker-compose up -d

# 3. Verificar que los servicios están running
docker-compose ps

# Debe mostrar:
# - apigeneradordata-mysql-1    : RUNNING (puerto 3310)
# - apigeneradordata-laravel-1  : RUNNING (puerto 8000)
```

### Paso 4: Configurar la Aplicación Laravel

```bash
# 1. Acceder al contenedor de Laravel
docker-compose exec laravel bash

# Dentro del contenedor, ejecutar:

# 2. Generar clave de aplicación
php artisan key:generate

# 3. Ejecutar migraciones (crear tablas BD)
php artisan migrate

# 4. (Opcional) Sembrar datos iniciales
php artisan db:seed

# 5. Limpiar caché
php artisan cache:clear
php artisan config:clear

# 6. Salir del contenedor
exit
```

### Paso 5: Verificar la Instalación

```bash
# 1. Probar endpoint de registro
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'

# Respuesta esperada:
# {
#   "message": "User registered successfully",
#   "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
# }
```

### Paso 6: Obtener Token JWT y Probar API

```bash
# 1. Login para obtener token
TOKEN=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }' | jq -r '.token')

# 2. Verificar que tenemos el token
echo "Token: $TOKEN"

# 3. Usar el token en peticiones autenticadas
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer $TOKEN"

# 4. Generar un DNI válido
curl -X GET http://localhost:8000/api/generate-dni \
  -H "Authorization: Bearer $TOKEN"

# Respuesta esperada:
# {
#   "dni": "12345678Z"
# }
```

---

## 📋 Comandos Docker Útiles

```bash
# Ver logs de los contenedores
docker-compose logs -f laravel    # Logs de Laravel
docker-compose logs -f mysql      # Logs de MySQL

# Acceder a la shell del contenedor
docker-compose exec laravel bash
docker-compose exec mysql mysql -u root -p

# Detener servicios (sin eliminar volúmenes)
docker-compose down

# Detener y eliminar todo (incluyendo volúmenes)
docker-compose down -v

# Reconstruir imágenes (útil si cambió el Dockerfile)
docker-compose build --no-cache

# Ejecutar comandos artisan sin entrar al contenedor
docker-compose exec laravel php artisan [comando]

# Resetear base de datos
docker-compose exec laravel php artisan migrate:fresh --seed
```

---

## 🧪 Testing de la API

### Crear Usuario Admin (para gestionar productos)

```bash
# 1. Registrar como admin (modificar seeder si es necesario)
# O directamente en MySQL:
docker-compose exec mysql mysql -u laravel -psecret laravel_db -e \
  "INSERT INTO users (name, email, role, password) 
   VALUES ('Admin User', 'admin@example.com', 'admin', '$2y$12...');"
```

### Crear Producto (requiere token admin)

```bash
TOKEN="[tu_token_jwt_aqui]"

curl -X POST http://localhost:8000/api/product \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "name": "Laptop",
    "price": 999.99
  }'
```

### Listar Productos

```bash
curl -X GET http://localhost:8000/api/products \
  -H "Authorization: Bearer $TOKEN"
```

---

## 🔐 Variables de Entorno Importantes

```env
APP_NAME=ApigeneradorData
APP_ENV=local                    # local, staging, production
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=mysql                    # Nombre del servicio Docker
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel
DB_PASSWORD=secret

JWT_SECRET=base64:45htVXDiQi9Dg6MtTd+MiUlVoko01W0jyFtApyI2gNs=
JWT_ALGORITHM=HS256
JWT_TTL=60                       # Minutos
```

---

## 📊 Dependencias Principales

### PHP (Composer)
- `laravel/framework` - Framework principal
- `tymon/jwt-auth` - Autenticación JWT
- `laravel/sanctum` - API tokens
- `laravel/tinker` - REPL
- `laravel/pint` - Code style
- `scribe` - Documentación API

### JavaScript (npm)
- `vite` - Build tool
- `tailwindcss` - CSS framework
- `axios` - Cliente HTTP

---

## 📈 Escalabilidad y Mejoras Futuras

### Características Implementadas ✅
- JWT con refresh tokens
- RBAC (Role-Based Access Control)
- Validación de entrada
- Documentación API (Scribe)
- Containerización Docker

### Posibles Mejoras 🚀
- Redis para caching y session storage
- Rate limiting por usuario
- Logging estructurado (ELK Stack)
- CI/CD con GitHub Actions
- API Gateway (Kong, Nginx)
- Replicación y backup automático de BD
- Tests de carga (JMeter, K6)

---

## 🐛 Troubleshooting

### Puerto 8000 ya está en uso
```bash
docker-compose down
# Liberar puerto manualmente:
sudo lsof -ti:8000 | xargs sudo kill -9
docker-compose up -d
```

### Puerto 3310 ya está en uso
```bash
sudo lsof -ti:3310 | xargs sudo kill -9
docker-compose up -d
```

### Errores de conexión a BD
```bash
# Verificar estado de MySQL
docker-compose logs mysql

# Dar permiso de acceso
docker-compose exec mysql mysql -u root -proot -e \
  "GRANT ALL ON laravel_db.* TO 'laravel'@'%';"
```

### Permisos de archivo en storage
```bash
docker-compose exec laravel chown -R www-data:www-data /var/www/html/storage
```

### Limpiar caché de Laravel
```bash
docker-compose exec laravel php artisan cache:clear
docker-compose exec laravel php artisan config:clear
```

---

## 📞 Contacto y Referencias

**Proyecto**: API Generador Data  
**Ubicación**: `/home/david/Documentos/desarrollo/apigeneradordata`  
**Tecnología Principal**: Laravel 12 + Docker  

Para más información, revisar:
- `README.md` - Documentación original
- `docker-compose.yml` - Configuración de servicios
- `src/routes/api.php` - Definición de rutas
- `.env` - Variables de entorno

---

**Documento generado automáticamente el 25 de Marzo de 2026**
