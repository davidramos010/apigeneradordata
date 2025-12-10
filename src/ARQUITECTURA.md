# Arquitectura SOLID - Guía de Implementación

## Descripción General

Este documento describe la arquitectura refactorizada del proyecto siguiendo los principios SOLID y patrones de diseño de Laravel 11.

## Estructura de Carpetas

```
app/
├── Http/
│   ├── Controllers/          # Controladores HTTP
│   └── Middleware/           # Middlewares de la aplicación
├── Models/                   # Modelos Eloquent
├── Services/                 # Lógica de negocio
├── Repositories/             # Acceso a datos
├── Contracts/                # Interfaces/Contratos
├── Validations/              # Validaciones
├── Exceptions/               # Excepciones personalizadas
└── Providers/                # Service Providers
```

## Principios Implementados

### 1. Single Responsibility Principle (SRP)

Cada clase tiene una única responsabilidad:

- **Controllers**: Manejo de HTTP (requests/responses)
- **Services**: Lógica de negocio
- **Repositories**: Acceso a datos
- **Validators**: Validación de datos

### 2. Open/Closed Principle (OCP)

Las clases están abiertas para extensión pero cerradas para modificación:

- Se usan **Interfaces/Contratos** para definir comportamientos
- Nuevas implementaciones pueden crearse sin modificar código existente

### 3. Liskov Substitution Principle (LSP)

Las implementaciones son intercambiables:

```php
interface ProductRepositoryContract { ... }
class ProductRepository implements ProductRepositoryContract { ... }
// Ambas pueden usarse indistintamente
```

### 4. Interface Segregation Principle (ISP)

Las interfaces son específicas y no incluyen métodos innecesarios.

### 5. Dependency Inversion Principle (DIP)

Las dependencias se inyectan por constructor:

```php
class ProductController
{
    public function __construct(ProductService $service) 
    {
        $this->service = $service;
    }
}
```

## Flujo de Datos

### Ejemplo: Crear un Producto

```
HTTP Request
    ↓
ProductController::addProduct()
    ↓
ProductService::createProduct()
    ↓
Validator (Validations/SpanishDocumentValidator)
    ↓
ProductRepository::create()
    ↓
Product Model (Eloquent)
    ↓
Database
    ↓
JSON Response
```

## Componentes Clave

### Services

Contienen la lógica de negocio de la aplicación:

- **ProductService**: Operaciones CRUD de productos
- **DocumentGeneratorService**: Generación y validación de documentos

### Repositories

Implementan el patrón Repository para acceso a datos:

- **ProductRepository**: Encapsula todas las queries de productos

### Validations

Centralizan la lógica de validación:

- **SpanishDocumentValidator**: Validación de documentos españoles

### Exceptions

Excepciones personalizadas para diferentes casos:

- **ResourceNotFoundException**: Recurso no encontrado
- **ValidationException**: Errores de validación

## Service Provider

El `AppServiceProvider` registra los bindings de dependencias:

```php
$this->app->bind(
    ProductRepositoryContract::class,
    ProductRepository::class
);
```

## Testing

Gracias a la inversión de dependencias, los tests son más fáciles:

```php
// Mock del repositorio
$mockRepository = Mockery::mock(ProductRepositoryContract::class);

// Inyectar mock en el servicio
$service = new ProductService($mockRepository);

// Hacer assertions
$this->assertTrue(...);
```

## Mejoras Futuras

1. **Eventos**: Implementar eventos de dominio
2. **DTOs**: Data Transfer Objects para requests/responses
3. **Actions**: Encapsular acciones complejas
4. **Queries**: Separar lógica de lectura
5. **Cache**: Implementar caché en repositorios
6. **Jobs**: Procesar operaciones pesadas de forma async

## Referencias

- [Principios SOLID](https://notasweb.me/entrada/principios-solid-aplicado-a-una-api-rest-en-laravel/)
- [solid-api](https://github.com/PortilloDev/solid-api)
- [Laravel Documentation](https://laravel.com/docs)
