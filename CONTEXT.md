# API Generador de Datos — Contexto del Proyecto

## Descripción general

API REST construida con **Laravel 12 + PHP 8.2** que genera y valida datos de prueba para entornos de desarrollo y testing. Corre en Docker y se documenta con Scribe.

- **URL local:** `http://localhost:8001`
- **Docs:** `http://localhost:8001/docs`
- **Rama principal:** `main`
- **Rama activa:** `HX-03-CUPS`

---

## Stack tecnológico

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12 |
| PHP | 8.2 |
| Auth | JWT (`tymon/jwt-auth ^2.2`) |
| Docs | Scribe (`knuckleswtf/scribe ^5.10`) |
| Base de datos | MySQL 8.0 (puerto 3307 en host) |
| Contenedor | Docker Compose |
| Testing | PHPUnit 11 |

---

## Infraestructura Docker

Archivo: `src/docker-compose.yml`

- **Servicio `app`** — contenedor Laravel, expone `8001:8000`, corre con `user: "${UID:-1000}:${GID:-1000}"` para que los archivos creados desde Docker sean del usuario del host.
- **Servicio `mysql`** — MySQL 8.0, datos persistidos en volumen `db_data_apigeneradordata`.
- El volumen `./:/var/www/html:cached` monta todo `src/` dentro del contenedor.

Comandos clave:
```bash
# Iniciar
docker compose up -d

# Ejecutar artisan
docker exec app_apigeneradordata php artisan <comando>

# Migrations + seeders desde cero
docker exec app_apigeneradordata php artisan migrate:fresh --seed

# Solo seeders
docker exec app_apigeneradordata php artisan db:seed

# Regenerar documentación
docker exec app_apigeneradordata php artisan scribe:generate
```

---

## Estructura de directorios relevante

```
src/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── GenerateDocumentController.php
│   │   │   ├── GenerateFinancialController.php
│   │   │   ├── GenerateCupsController.php
│   │   │   └── ProductController.php
│   │   └── Middleware/
│   │       ├── IsAdmin.php       # rol === 'admin'
│   │       └── IsUserAuth.php    # cualquier usuario autenticado
│   └── Models/
│       ├── User.php
│       ├── Product.php
│       ├── GenerateDocument.php
│       ├── GenerateFinancial.php
│       └── GenerateCups.php
├── config/
│   ├── scribe.php                # config de docs (auth.enabled = true)
│   └── jwt.php
├── database/
│   ├── migrations/
│   └── seeders/DatabaseSeeder.php
└── routes/api.php
```

---

## Base de datos

### Tabla `users`
| Campo | Tipo | Notas |
|---|---|---|
| id | bigint PK | |
| name | string | |
| role | string(20) | `user`, `admin` |
| email | string unique | |
| password | string | hasheada |
| timestamps | | |

### Tabla `products`
| Campo | Tipo |
|---|---|
| id | bigint PK |
| name | string(100) |
| price | decimal(8,2) |
| timestamps | |

### Seeders (usuarios por defecto)
| Email | Password | Rol |
|---|---|---|
| `test@example.com` | (factory) | `user` |
| `admin@example.com` | (factory) | `admin` |
| `apiservice@generardordata.com` | `4piS3rv1c3P4ssw0rd.` | `user` |

---

## Autenticación

- **Tipo:** JWT Bearer token
- **Login:** `POST /api/login` → devuelve `{ token, message, status }`
- **Uso:** Header `Authorization: Bearer <token>`
- Las rutas `login` y `register` tienen `@unauthenticated` (no piden token en Scribe)
- Middleware `IsUserAuth` — verifica `auth('api')->user()`
- Middleware `IsAdmin` — verifica además que `role === 'admin'`

---

## Rutas de la API (`routes/api.php`)

### Públicas (sin token)
| Método | Ruta | Controlador | Descripción |
|---|---|---|---|
| POST | `/api/login` | AuthController@login | Obtener token JWT |
| POST | `/api/register` | AuthController@register | Registrar usuario |

### Protegidas — usuario autenticado (`IsUserAuth`)
| Método | Ruta | Controlador | Descripción |
|---|---|---|---|
| GET | `/api/me` | AuthController@user | Info del usuario actual |
| POST | `/api/logout` | AuthController@logout | Cerrar sesión |
| GET | `/api/products` | ProductController@getProducts | Listar productos |
| GET | `/api/generate-dni` | GenerateDocumentController@generateDni | Generar DNI(s) |
| GET | `/api/generate-cif` | GenerateDocumentController@generateCif | Generar CIF(s) |
| GET | `/api/generate-cif-by-type` | GenerateDocumentController@generateCifByType | Generar CIF por tipo de entidad |
| GET | `/api/generate-nie` | GenerateDocumentController@generateNie | Generar NIE(s) |
| GET | `/api/generate-nif` | GenerateDocumentController@generateNif | Generar NIF(s) |
| GET | `/api/generate-ssn` | GenerateDocumentController@generateSsn | Generar SSN(s) |
| GET | `/api/validate-document` | GenerateDocumentController@validateDocument | Validar documento (auto-detect o tipo) |
| GET | `/api/generate-iban` | GenerateFinancialController@generateIban | Generar IBAN español |
| GET | `/api/validate-iban` | GenerateFinancialController@validateIban | Validar IBAN |
| GET | `/api/generate-cuenta` | GenerateFinancialController@generateCuenta | Generar CCC |
| GET | `/api/generate-tarjeta` | GenerateFinancialController@generateTarjeta | Generar tarjeta (VISA/MC/AMEX) |
| POST | `/api/cups/generate` | GenerateCupsController@generate | Generar CUPS |
| POST | `/api/cups/validate` | GenerateCupsController@validate | Validar CUPS |

### Protegidas — solo admin (`IsAdmin`)
| Método | Ruta | Controlador |
|---|---|---|
| GET | `/api/products` | ProductController@getProducts |
| POST | `/api/product` | ProductController@addProduct |
| GET | `/api/product/{id}` | ProductController@getProduct |
| PUT | `/api/product/{id}` | ProductController@updateProduct |
| DELETE | `/api/product/{id}` | ProductController@deleteProduct |

---

## Grupos de documentación Scribe

| Grupo | Controlador |
|---|---|
| 1. Authentication | AuthController |
| 2. Document Generation | GenerateDocumentController |
| 3. Product Management | ProductController |
| 4. Financial Data | GenerateFinancialController |
| 5. CUPS | GenerateCupsController |

---

## Parámetros comunes de generación

La mayoría de endpoints de generación aceptan:
- `?result=N` (query param) — cuántos elementos generar. Default: 1, Max: 20 (documentos/financiero) o 100 (CUPS).

---

## Módulos de generación

### Documentos (`GenerateDocumentController`)
- **DNI:** 8 dígitos + letra control
- **CIF:** letra entidad + 7 dígitos + dígito/letra control. Tipos: A, B, C, D, E, F, G, H, J, K, L, M, N, P, Q, R, S, U, V, W
- **NIE:** X/Y/Z + 7 dígitos + letra control
- **NIF:** igual que DNI
- **SSN:** formato `AAA-BB-CCCC` (EE.UU.)
- **Validar documento:** auto-detecta tipo o valida contra uno específico (DNI, NIF, NIE, CIF, SSN, PASAPORTE)

### Financiero (`GenerateFinancialController`)
- **IBAN español:** ISO 13616, MOD-97, CCC MOD-11. Devuelve `iban`, `formatted`, `components`
- **Validar IBAN:** verifica longitud, prefijo ES, MOD-97 y dígitos control CCC
- **CCC (Cuenta):** banco(4) + sucursal(4) + control(2) + cuenta(10)
- **Tarjeta:** VISA (16d, prefijo 4xxx), MASTERCARD (16d, prefijo 51-55), AMEX (15d, prefijo 34/37). Algoritmo Luhn. Devuelve número, formatted, type, expiry, cvv

### CUPS (`GenerateCupsController`)
- **Generar CUPS:** tipo `electricidad` o `gas`, distribuidora opcional (4 dígitos), cantidad 1-100, sufijo opcional (22 chars)
- **Validar CUPS:** verifica longitud (20 o 22 chars), prefijo ES, letras control (algoritmo mod 529)

---

## Configuración Scribe (`config/scribe.php`)

```php
'type' => 'static',             // genera HTML estático en public/docs
'auth' => [
    'enabled' => true,          // muestra campo Bearer token en docs
    'default' => true,          // todas las rutas son @authenticated por defecto
    'in' => AuthIn::BEARER,
],
'base_url' => env('APP_URL'),   // http://localhost:8001
```

Las rutas públicas deben anotarse con `@unauthenticated` en el PHPDoc del método.

---

## Convenciones del proyecto

- Respuestas siempre en JSON
- Códigos de error estándar: 400 (validación), 401 (no auth), 404 (no encontrado), 422 (validación Laravel), 500 (error interno)
- Los parámetros de generación se pasan por **query string** en GET y por **body (JSON)** en POST
- Los modelos contienen la lógica de generación/validación (no en los controladores)
- Documentación inline con anotaciones Scribe en PHPDoc de cada método
