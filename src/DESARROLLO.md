# Guía de Desarrollo - Refactorización SOLID

## 📋 Cambios Realizados

### Estructura Nueva

```
app/
├── Contracts/                          # Interfaces para inyección de dependencias
│   ├── DocumentGeneratorContract.php
│   └── ProductRepositoryContract.php
├── Exceptions/                         # Excepciones personalizadas
│   ├── Handler.php
│   ├── ResourceNotFoundException.php
│   └── ValidationException.php
├── Repositories/                       # Patrón Repository
│   └── ProductRepository.php
├── Services/                           # Lógica de negocio
│   ├── DocumentGeneratorService.php
│   └── ProductService.php
├── Validations/                        # Validadores
│   └── SpanishDocumentValidator.php
└── ...
```

## 🔧 Cómo Usar la Nueva Arquitectura

### Agregar un Nuevo Servicio

**1. Crear la interfaz** (`app/Contracts/`)

```php
namespace App\Contracts;

interface MiServicioContract
{
    public function hacer(): void;
}
```

**2. Crear la implementación** (`app/Services/`)

```php
namespace App\Services;

use App\Contracts\MiServicioContract;

class MiServicio implements MiServicioContract
{
    public function hacer(): void
    {
        // Lógica aquí
    }
}
```

**3. Registrar en el Service Provider**

```php
// app/Providers/AppServiceProvider.php
$this->app->bind(MiServicioContract::class, MiServicio::class);
```

**4. Inyectar en el Controller**

```php
class MiController
{
    public function __construct(MiServicioContract $servicio)
    {
        $this->servicio = $servicio;
    }
}
```

### Agregar un Nuevo Repositorio

**1. Crear la interfaz**

```php
interface MiRepositorioContract
{
    public function getAll();
    public function getById(int $id);
    public function create(array $data);
}
```

**2. Implementar el repositorio**

```php
class MiRepositorio implements MiRepositorioContract
{
    public function __construct(MiModelo $model)
    {
        $this->model = $model;
    }
    
    public function getAll()
    {
        return $this->model->all();
    }
    // ...
}
```

**3. Registrar el binding**

```php
$this->app->bind(MiRepositorioContract::class, MiRepositorio::class);
```

## 📦 Patrones Utilizados

### Service Locator (Anti-patrón a evitar)
❌ No hacer:
```php
$usuario = app('UsuarioService')->obtener(1);
```

### Inyección de Dependencias (✅ Patrón SOLID)
✅ Hacer:
```php
class Controller
{
    public function __construct(UsuarioService $usuario)
    {
        $this->usuario = $usuario;
    }
}
```

## 🧪 Testing

### Ejecutar Tests Unitarios
```bash
php artisan test tests/Unit/SpanishDocumentValidatorTest.php
```

### Ejecutar Tests Feature
```bash
php artisan test tests/Feature/ProductControllerTest.php
```

### Crear un Mock en Tests
```php
$mockRepository = Mockery::mock(ProductRepositoryContract::class);
$mockRepository->shouldReceive('getAll')->andReturn([]);

$service = new ProductService($mockRepository);
$result = $service->getAllProducts();
```

## 📝 Mejores Prácticas

### 1. Controllers Delgados
Los controladores solo deben:
- Validar el input (si no hay validador)
- Llamar al servicio
- Formatear la respuesta

```php
class ProductController
{
    public function __construct(ProductService $service) {}
    
    public function create(Request $request)
    {
        try {
            $product = $this->service->createProduct($request->all());
            return response()->json($product, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
```

### 2. Services Concretos
Los servicios contienen la lógica:

```php
class ProductService
{
    public function __construct(ProductRepositoryContract $repository) {}
    
    public function createProduct(array $data)
    {
        // Validación
        // Lógica de negocio
        // Llamar al repositorio
        return $this->repository->create($data);
    }
}
```

### 3. Repositories Simples
Los repositorios solo acceden a datos:

```php
class ProductRepository implements ProductRepositoryContract
{
    public function create(array $data)
    {
        return $this->model->create($data);
    }
}
```

## 🔄 Flujo de Datos

```
Request
  ↓
Route → Controller
  ↓
Service (lógica de negocio)
  ↓
Repository (acceso a datos)
  ↓
Model (Eloquent)
  ↓
Database
  ↓
Response
```

## 📚 Véase También

- [ARQUITECTURA.md](./ARQUITECTURA.md) - Documentación técnica
- [GEMINI.md](./GEMINI.md) - Overview del proyecto
- [Principios SOLID](https://notasweb.me/entrada/principios-solid-aplicado-a-una-api-rest-en-laravel/)
- [solid-api](https://github.com/PortilloDev/solid-api)

## ⚠️ Próximos Pasos

- [ ] Implementar DTOs para requests/responses
- [ ] Agregar caché en repositorios
- [ ] Implementar eventos de dominio
- [ ] Mejorar cobertura de tests
- [ ] Documentar API con Scribe
- [ ] Agregar validación con Form Requests
