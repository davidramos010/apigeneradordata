# 📚 GUÍA DE ESTÁNDARES: ARQUITECTURA HEXAGONAL PARA APIs REST
## API Generador Data - Laravel 12

**Documento oficial de estándares**
**Versión**: 1.0
**Última actualización**: 25 de Marzo de 2026
**Audiencia**: Equipo de desarrollo backend

---

## 📖 TABLA DE CONTENIDOS

1. [Introducción](#introducción)
2. [Estructura de Carpetas](#estructura-de-carpetas)
3. [Conceptos Fundamentales](#conceptos-fundamentales)
4. [Regla de Dependencias](#regla-de-dependencias)
5. [Flujo de Petición HTTP](#flujo-de-petición-http)
6. [Cómo Crear un Nuevo Endpoint](#cómo-crear-un-nuevo-endpoint)
7. [Ejemplos Prácticos](#ejemplos-prácticos)
8. [Gestión de Excepciones](#gestión-de-excepciones)
9. [Testing](#testing)
10. [Checklist de Validación](#checklist-de-validación)

---

## 🎯 INTRODUCCIÓN

Esta guía define el **único estándar** para construir nuevos endpoints en nuestro API REST. Implementamos **Arquitectura Hexagonal** con **Domain-Driven Design (DDD)** para lograr:

- ✅ **Desacoplamiento**: Lógica libre del framework
- ✅ **Testabilidad**: 80%+ de cobertura unitaria
- ✅ **Mantenibilidad**: Código limpio y legible
- ✅ **Escalabilidad**: Fácil agregar nuevas features
- ✅ **Colaboración**: Equipo alineado en patrones

**No hay excepciones: TODO nuevo endpoint sigue esta guía.**

---

## 🏗️ ESTRUCTURA DE CARPETAS

```
src/app/
│
├── Domain/                          # 🔷 CAPA DE DOMINIO (Negocio puro)
│   ├── Shared/
│   │   ├── ValueObjects/
│   │   │   ├── Email.php
│   │   │   ├── Money.php
│   │   │   └── ...VO...
│   │   ├── Exceptions/
│   │   │   ├── DomainException.php
│   │   │   └── ...Exceptions
│   │   └── Entities/
│   │       └── AggregateRoot.php
│   │
│   ├── [EntityName]/                # Agregado de negocio (ej: Users, Products)
│   │   ├── Entities/
│   │   │   ├── [Entity].php         # Entidad pura (sin Eloquent)
│   │   │   └── [Aggregate].php      # Agregado raíz
│   │   ├── ValueObjects/
│   │   │   ├── [ID].php
│   │   │   ├── [Property].php
│   │   │   └── ...VO especializados
│   │   ├── Repositories/
│   │   │   └── [Entity]RepositoryInterface.php  # PUERTO (Interfaz)
│   │   ├── Services/
│   │   │   └── [DomainService].php  # Servicios de dominio (si needed)
│   │   └── Exceptions/
│   │       ├── [Entity]NotFound.php
│   │       ├── Invalid[Property].php
│   │       └── ...Excepciones específicas
│   │
│   └── [OtherEntity]/               # Otros agregados (mismo patrón)
│
├── Application/                     # 🔶 CAPA DE APLICACIÓN (Orquestación)
│   ├── DTOs/
│   │   ├── Request/
│   │   │   ├── [ActionName]Request.php
│   │   │   └── ...RequestDTOs
│   │   └── Response/
│   │       ├── [Entity]Response.php
│   │       ├── TokenResponse.php
│   │       └── ...ResponseDTOs
│   │
│   ├── UseCases/                    # Application Services
│   │   ├── [Entity]/
│   │   │   ├── Create[Entity]UseCase.php
│   │   │   ├── Update[Entity]UseCase.php
│   │   │   ├── Delete[Entity]UseCase.php
│   │   │   ├── Get[Entity]UseCase.php
│   │   │   └── List[Entities]UseCase.php
│   │   └── [OtherEntity]/          # Otros agregados
│   │
│   └── Mappers/                     # Conversores DTO ↔ Entity
│       ├── [Entity]Mapper.php
│       └── ...Mappers
│
├── Infrastructure/                  # 🔴 CAPA DE INFRAESTRUCTURA (Adaptadores)
│   ├── Persistence/                 # Adaptadores de Persistencia
│   │   ├── Eloquent/
│   │   │   ├── Models/
│   │   │   │   ├── [Entity]EloquentModel.php
│   │   │   │   └── ...Models (NO LÓGICA)
│   │   │   ├── Repositories/
│   │   │   │   ├── [Entity]EloquentRepository.php
│   │   │   │   └── ...Repositories
│   │   │   └── Factories/
│   │   │       └── ...Factories
│   │   └── (Futuro: Redis, MongoDB, etc.)
│   │
│   ├── Http/                        # Adaptadores HTTP
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── [Entity]Controller.php  # THIN - solo orquestación
│   │   │   │   └── ...Controllers
│   │   │   └── Controller.php       # Clase base
│   │   ├── Middleware/
│   │   │   ├── AuthenticateWithJwt.php
│   │   │   ├── AuthorizeWithRole.php
│   │   │   └── ...Middleware
│   │   ├── Requests/                # Form Requests (validación Laravel)
│   │   │   ├── Store[Entity]Request.php
│   │   │   ├── Update[Entity]Request.php
│   │   │   └── ...Requests
│   │   ├── Resources/               # JSON Resources (serialización)
│   │   │   ├── [Entity]Resource.php
│   │   │   └── ...Resources
│   │   └── Responses/
│   │       └── ...Response helpers
│   │
│   ├── Providers/                   # Service Providers (DI)
│   │   ├── DomainServiceProvider.php
│   │   ├── ApplicationServiceProvider.php
│   │   ├── RepositoryServiceProvider.php
│   │   └── ...Providers
│   │
│   ├── Services/                    # Servicios de infraestructura
│   │   ├── [ExternalService].php
│   │   └── ...Services
│   │
│   └── Exceptions/
│       └── DomainExceptionHandler.php
│
├── Providers/
│   └── AppServiceProvider.php       # Registra todos los providers
│
├── routes/
│   ├── api.php                      # Rutas API
│   └── v1/                          # (FUTURA) versionado
│
├── config/
│   └── domain.php                   # Configuraciones de dominio
│
└── tests/
    ├── Unit/
    │   ├── Domain/
    │   │   └── [Entity]/
    │   │       ├── Entities/
    │   │       ├── ValueObjects/
    │   │       └── Exceptions/
    │   └── Application/
    │       └── UseCases/
    └── Feature/
        ├── Api/
        │   └── [Endpoint]/
        └── Application/
            └── UseCases/
```

### 🎨 REGLA DE CARPETAS

| Carpeta | Responsabilidad | Dependencias | ¿Eloquent? |
|---------|-----------------|--------------|-----------|
| **Domain** | Lógica pura del negocio | Ninguna (aislado) | ❌ NO |
| **Application** | Orquestación de casos de uso | Domain | ❌ NO |
| **Infrastructure** | Adaptadores e implementaciones | Domain, Application | ✅ SÍ |

---

## 🧠 CONCEPTOS FUNDAMENTALES

### 1️⃣ Value Objects (VO)

Un **Value Object** es un objeto immutable que representa un **concepto del dominio**. No hay ID único (se usa para serialización).

**Ejemplos**: Email, Money, DocumentNumber, Role, Status

**Características:**
- ✅ Encapsulan validación
- ✅ Immutables
- ✅ Comparable por valor (no por referencia)
- ✅ SIN persistencia directa

**Cuándo crear:**
- Concepto que aparece en múltiples agregados
- Requiere validación específica
- Tiene comportamiento específico

**EJEMPLO**: Value Object `Email`

```php
<?php
namespace App\Domain\Shared\ValueObjects;

use App\Domain\Shared\Exceptions\InvalidEmail;

class Email
{
    private readonly string $value;

    public function __construct(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmail::create();
        }
        $this->value = strtolower($email);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function domain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }
}
```

### 2️⃣ Entities (Entidades)

Una **Entity** es un objeto con identidad única. Representa un **agregado de dominio**.

**Ejemplos**: User, Product, Order

**Características:**
- ✓ Identidad única (ID)
- ✓ Mutable
- ✓ Encapsula la lógica de negocio
- ✓ Creada por factory o desde persistencia

**Cuándo crear:**
- Concepto con ciclo de vida
- Tiene cambios de estado
- Requiere agregación de properties

**EJEMPLO**: Entity `Product`

```php
<?php
namespace App\Domain\Products\Entities;

use App\Domain\Products\ValueObjects\ProductId;
use App\Domain\Products\ValueObjects\ProductName;
use App\Domain\Products\ValueObjects\Money;

class Product
{
    private ProductId $id;
    private ProductName $name;
    private Money $price;
    private \DateTimeImmutable $createdAt;

    private function __construct(
        ProductId $id,
        ProductName $name,
        Money $price,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->createdAt = $createdAt;
    }

    // Factory method para CREAR nuevo producto
    public static function create(string $name, float $price): self
    {
        return new self(
            ProductId::fromInt(0), // ID será asignado por BD
            new ProductName($name),
            new Money($price),
            new \DateTimeImmutable(),
        );
    }

    // Factory method para RECONSTRUIR desde BD
    public static function fromPersistence(
        int $id,
        string $name,
        float $price,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self(
            ProductId::fromInt($id),
            new ProductName($name),
            new Money($price),
            $createdAt,
        );
    }

    // Getters (solo lectura)
    public function id(): ProductId { return $this->id; }
    public function name(): ProductName { return $this->name; }
    public function price(): Money { return $this->price; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    // Métodos de negocio
    public function updatePrice(Money $newPrice): void
    {
        if ($newPrice->amount() <= 0) {
            throw InvalidPrice::create();
        }
        $this->price = $newPrice;
    }

    public function isExpensive(): bool
    {
        return $this->price->amount() > 1000;
    }
}
```

### 3️⃣ Repositories (Puertos)

Un **Repository** es una **interfaz** (contrato) que define cómo acceder a datos. Sirve para **desacoplar el dominio de la persistencia**.

**Responsabilidad**: Definir operaciones CRUD abstractas

**EJEMPLO**: Interface de Repositorio

```php
<?php
namespace App\Domain\Products\Repositories;

use App\Domain\Products\Entities\Product;
use App\Domain\Products\ValueObjects\ProductId;

interface ProductRepositoryInterface
{
    // C - Create
    public function save(Product $product): void;

    // R - Read
    public function findById(ProductId $id): ?Product;
    public function findAll(): array;

    // U - Update (via save)
    // D - Delete
    public function delete(ProductId $id): void;
}
```

### 4️⃣ Use Cases (Application Services)

Un **Use Case** es una **acción específica del usuario**. Orquesta el flujo:

```
Request DTO → Validación → Lógica de Dominio → Persistencia → Response DTO
```

**Responsabilidades:**
- Convertir DTO a Entidades (mapper)
- Invocar lógica de dominio
- Manejar transacciones
- Convertir Entidades a Response DTO

**Cuándo crear:**
- Cada acción distinta del usuario (crear, actualizar, listar, etc.)

**EJEMPLO**: Use Case

```php
<?php
namespace App\Application\UseCases\Products;

use App\Application\DTOs\Request\CreateProductRequest;
use App\Application\DTOs\Response\ProductResponse;
use App\Application\Mappers\ProductMapper;
use App\Domain\Products\Entities\Product;
use App\Domain\Products\Repositories\ProductRepositoryInterface;

class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $repository,
    ) {}

    public function execute(CreateProductRequest $request): ProductResponse
    {
        // 1. Crear entidad desde DTO
        $product = Product::create(
            name: $request->name,
            price: $request->price,
        );

        // 2. Persistir
        $this->repository->save($product);

        // 3. Retornar como Response
        return ProductMapper::toResponse($product);
    }
}
```

### 5️⃣ DTOs (Data Transfer Objects)

Un **DTO** es un objeto simple que **transporta datos entre capas**. Sin lógica de negocio.

**Responsabilidad**: Transportar datos de entrada/salida

**EJEMPLO**: DTOs

```php
<?php
// Request DTO (entrada HTTP)
namespace App\Application\DTOs\Request;

class CreateProductRequest
{
    public function __construct(
        public readonly string $name,
        public readonly float $price,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            price: (float)($data['price'] ?? 0),
        );
    }
}

// Response DTO (salida HTTP)
namespace App\Application\DTOs\Response;

class ProductResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly float $price,
        public readonly string $createdAt,
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'created_at' => $this->createdAt,
        ];
    }
}
```

---

## 🔄 REGLA DE DEPENDENCIAS

Esta es la **REGLA FUNDAMENTAL** de Arquitectura Hexagonal:

```
🔴 Infrastructure (Adaptadores)
    ↓ depende de ↓
🔶 Application (Orquestación)
    ↓ depende de ↓
🔷 Domain (Lógica pura)
```

### ✅ PERMITIDO

```
Domain → Domain (una entidad usa otra)
Application → Domain (use case usa entidades)
Application → Application (un use case usa otro)
Infrastructure → Application (controller invoca use case)
Infrastructure → Domain (repository implementa interfaz del domain)
```

### ❌ PROHIBIDO

```
Domain → Application ✗ (dominio no debe conocer orquestación)
Domain → Infrastructure ✗ (dominio no debe conocer BD/HTTP)
Application → Infrastructure ✗ (directo - debe ser inyectado)
```

### 📋 VERIFICACIÓN

**¿Mi código viola la regla?**

1. ¿Importa desde `Infrastructure`? → ✓ OK solo si implementa Interface del Domain
2. ¿Importa desde `Application`? → ✓ OK si es desde clase base o helper
3. ¿Importa desde `Domain`? → ✓ Siempre OK para usar entidades/valores

---

## 🌊 FLUJO DE PETICIÓN HTTP

### Paso a Paso: Crear un Producto

```
┌─ 1️⃣ HTTP REQUEST ──────────────────────────────┐
│ POST /api/products                              │
│ Content-Type: application/json                  │
│ Authorization: Bearer {token}                   │
│ {                                               │
│   "name": "Laptop",                             │
│   "price": 999.99                               │
│ }                                               │
└────────────────────────────────────────────────┘
         ⬇️ (1) Router envía a Controller
┌─ 2️⃣ INFRASTRUCTURE LAYER (HTTP Adapter) ──────┐
│ ProductController::store()                      │
│ ├─ Inyecta Use Case (DI)                        │
│ ├─ Convierte Request a DTO                      │
│ └─ Invoca Use Case                              │
└────────────────────────────────────────────────┘
         ⬇️ (2) Pasa DTO al Use Case
┌─ 3️⃣ APPLICATION LAYER ──────────────────────────┐
│ CreateProductUseCase::execute(DTO)              │
│ ├─ Mapper: DTO → Entidad                        │
│ ├─ Delega lógica a Entidad                      │
│ ├─ Manda guardar por Repository Interface       │
│ └─ Mapper: Entidad → ResponseDTO                │
└────────────────────────────────────────────────┘
         ⬇️ (3) Pasa Entidad al Repository (interfaz)
┌─ 4️⃣ INFRASTRUCTURE LAYER (Persistence) ────────┐
│ ProductEloquentRepository::save(Entity)         │
│ ├─ Convierte Entidad a Eloquent Model           │
│ ├─ Usa Eloquent para guardar en BD              │
│ └─ Asigna ID (genera IDENTITY)                  │
└────────────────────────────────────────────────┘
         ⬇️ (4) Regresa Entidad con ID
┌─ 5️⃣ APPLICATION LAYER ──────────────────────────┐
│ UseCase retorna ResponseDTO                     │
└────────────────────────────────────────────────┘
         ⬇️ (5) Pasa ResponseDTO al Controller
┌─ 6️⃣ INFRASTRUCTURE LAYER (HTTP Response) ──────┐
│ Controller retorna JSON                         │
└────────────────────────────────────────────────┘
         ⬇️ (6) Laravel envia HTTP Response
┌─ 7️⃣ HTTP RESPONSE ───────────────────────────────┐
│ HTTP/1.1 201 Created                            │
│ Content-Type: application/json                  │
│ {                                               │
│   "id": 123,                                    │
│   "name": "Laptop",                             │
│   "price": 999.99,                              │
│   "created_at": "2026-03-25T10:30:00Z"          │
│ }                                               │
└────────────────────────────────────────────────┘
```

---

## 🛠️ CÓMO CREAR UN NUEVO ENDPOINT

Paso a paso para crear un nuevo endpoint siguiendo esta arquitectura.

### 📋 CHECKLIST PRE-DESARROLLO

- [ ] ¿Qué acción de negocio hago? (crear/actualizar/listar/etc.)
- [ ] ¿Qué necesita el usuario? (HTTP Request)
- [ ] ¿Qué logica de negocio hay?
- [ ] ¿Qué datos devuelvo? (HTTP Response)
- [ ] ¿Qué errores pueden ocurrir?

### 🚀 PROCESO (6 PASOS)

#### PASO 1: Crear Dominio (si es nuevo agregado)

**A. Value Objects**

```php
// src/app/Domain/Invoices/ValueObjects/InvoiceId.php
namespace App\Domain\Invoices\ValueObjects;

class InvoiceId
{
    private readonly int $value;

    private function __construct(int $id)
    {
        $this->value = $id;
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    public function value(): int
    {
        return $this->value;
    }
}

// src/app/Domain/Invoices/ValueObjects/InvoiceNumber.php
namespace App\Domain\Invoices\ValueObjects;

use App\Domain\Invoices\Exceptions\InvalidInvoiceNumber;

class InvoiceNumber
{
    private readonly string $value;

    public function __construct(string $number)
    {
        if (!preg_match('/^INV-\d{6}$/', $number)) {
            throw InvalidInvoiceNumber::create();
        }
        $this->value = $number;
    }

    public function value(): string
    {
        return $this->value;
    }
}
```

**B. Entity**

```php
// src/app/Domain/Invoices/Entities/Invoice.php
namespace App\Domain\Invoices\Entities;

use App\Domain\Invoices\ValueObjects\InvoiceId;
use App\Domain\Invoices\ValueObjects\InvoiceNumber;
use App\Domain\Users\ValueObjects\UserId;

class Invoice
{
    private InvoiceId $id;
    private InvoiceNumber $number;
    private UserId $userId;
    private float $amount;
    private string $status; // draft, sent, paid
    private \DateTimeImmutable $createdAt;

    private function __construct(
        InvoiceId $id,
        InvoiceNumber $number,
        UserId $userId,
        float $amount,
        string $status,
        \DateTimeImmutable $createdAt,
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->userId = $userId;
        $this->amount = $amount;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }

    public static function create(
        InvoiceNumber $number,
        UserId $userId,
        float $amount,
    ): self {
        return new self(
            InvoiceId::fromInt(0),
            $number,
            $userId,
            $amount,
            'draft',
            new \DateTimeImmutable(),
        );
    }

    public static function fromPersistence(
        int $id,
        string $number,
        int $userId,
        float $amount,
        string $status,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self(
            InvoiceId::fromInt($id),
            new InvoiceNumber($number),
            UserId::fromInt($userId),
            $amount,
            $status,
            $createdAt,
        );
    }

    public function id(): InvoiceId { return $this->id; }
    public function number(): InvoiceNumber { return $this->number; }
    public function userId(): UserId { return $this->userId; }
    public function amount(): float { return $this->amount; }
    public function status(): string { return $this->status; }
    public function createdAt(): \DateTimeImmutable { return $this->createdAt; }

    public function markAsSent(): void
    {
        if ($this->status !== 'draft') {
            throw InvalidInvoiceState::create('Can only send from draft');
        }
        $this->status = 'sent';
    }

    public function markAsPaid(): void
    {
        if ($this->status !== 'sent') {
            throw InvalidInvoiceState::create('Can only mark as paid from sent');
        }
        $this->status = 'paid';
    }
}
```

**C. Repository Interface (PUERTO)**

```php
// src/app/Domain/Invoices/Repositories/InvoiceRepositoryInterface.php
namespace App\Domain\Invoices\Repositories;

use App\Domain\Invoices\Entities\Invoice;
use App\Domain\Invoices\ValueObjects\InvoiceId;
use App\Domain\Users\ValueObjects\UserId;

interface InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void;
    public function findById(InvoiceId $id): ?Invoice;
    public function findByUser(UserId $userId): array;
    public function findByStatus(string $status): array;
    public function delete(InvoiceId $id): void;
}
```

**D. Excepciones**

```php
// src/app/Domain/Invoices/Exceptions/InvoiceNotFound.php
namespace App\Domain\Invoices\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class InvoiceNotFound extends DomainException
{
    public static function create(): self
    {
        return new self('Invoice not found');
    }

    public function getHttpStatusCode(): int
    {
        return 404;
    }
}

// src/app/Domain/Invoices/Exceptions/InvalidInvoiceNumber.php
namespace App\Domain\Invoices\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;

class InvalidInvoiceNumber extends DomainException
{
    public static function create(): self
    {
        return new self('Invalid invoice number format');
    }

    public function getHttpStatusCode(): int
    {
        return 422;
    }
}
```

#### PASO 2: Crear DTOs (Application Layer)

```php
// src/app/Application/DTOs/Request/CreateInvoiceRequest.php
namespace App\Application\DTOs\Request;

class CreateInvoiceRequest
{
    public function __construct(
        public readonly string $invoiceNumber,
        public readonly int $userId,
        public readonly float $amount,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            invoiceNumber: $data['invoice_number'] ?? '',
            userId: (int)($data['user_id'] ?? 0),
            amount: (float)($data['amount'] ?? 0),
        );
    }
}

// src/app/Application/DTOs/Response/InvoiceResponse.php
namespace App\Application\DTOs\Response;

use App\Domain\Invoices\Entities\Invoice;

class InvoiceResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $number,
        public readonly int $userId,
        public readonly float $amount,
        public readonly string $status,
        public readonly string $createdAt,
    ) {}

    public static function fromEntity(Invoice $invoice): self
    {
        return new self(
            id: $invoice->id()->value(),
            number: $invoice->number()->value(),
            userId: $invoice->userId()->value(),
            amount: $invoice->amount(),
            status: $invoice->status(),
            createdAt: $invoice->createdAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'user_id' => $this->userId,
            'amount' => $this->amount,
            'status' => $this->status,
            'created_at' => $this->createdAt,
        ];
    }
}
```

#### PASO 3: Crear Mapper

```php
// src/app/Application/Mappers/InvoiceMapper.php
namespace App\Application\Mappers;

use App\Application\DTOs\Request\CreateInvoiceRequest;
use App\Application\DTOs\Response\InvoiceResponse;
use App\Domain\Invoices\Entities\Invoice;
use App\Domain\Invoices\ValueObjects\InvoiceNumber;
use App\Domain\Users\ValueObjects\UserId;

class InvoiceMapper
{
    public static function toDomain(CreateInvoiceRequest $request): Invoice
    {
        return Invoice::create(
            number: new InvoiceNumber($request->invoiceNumber),
            userId: UserId::fromInt($request->userId),
            amount: $request->amount,
        );
    }

    public static function toResponse(Invoice $invoice): InvoiceResponse
    {
        return InvoiceResponse::fromEntity($invoice);
    }
}
```

#### PASO 4: Crear Use Case

```php
// src/app/Application/UseCases/Invoices/CreateInvoiceUseCase.php
namespace App\Application\UseCases\Invoices;

use App\Application\DTOs\Request\CreateInvoiceRequest;
use App\Application\DTOs\Response\InvoiceResponse;
use App\Application\Mappers\InvoiceMapper;
use App\Domain\Invoices\Repositories\InvoiceRepositoryInterface;

class CreateInvoiceUseCase
{
    public function __construct(
        private InvoiceRepositoryInterface $repository,
    ) {}

    public function execute(CreateInvoiceRequest $request): InvoiceResponse
    {
        $invoice = InvoiceMapper::toDomain($request);
        $this->repository->save($invoice);

        return InvoiceMapper::toResponse($invoice);
    }
}
```

#### PASO 5: Crear Adaptador (Eloquent)

```php
// src/app/Infrastructure/Persistence/Eloquent/Models/InvoiceEloquentModel.php
namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceEloquentModel extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $fillable = ['number', 'user_id', 'amount', 'status'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

// src/app/Infrastructure/Persistence/Eloquent/Repositories/InvoiceEloquentRepository.php
namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Invoices\Entities\Invoice;
use App\Domain\Invoices\Repositories\InvoiceRepositoryInterface;
use App\Domain\Invoices\ValueObjects\InvoiceId;
use App\Domain\Users\ValueObjects\UserId;
use App\Infrastructure\Persistence\Eloquent\Models\InvoiceEloquentModel;

class InvoiceEloquentRepository implements InvoiceRepositoryInterface
{
    public function save(Invoice $invoice): void
    {
        InvoiceEloquentModel::updateOrCreate(
            ['id' => $invoice->id()->value()],
            [
                'number' => $invoice->number()->value(),
                'user_id' => $invoice->userId()->value(),
                'amount' => $invoice->amount(),
                'status' => $invoice->status(),
            ],
        );
    }

    public function findById(InvoiceId $id): ?Invoice
    {
        $model = InvoiceEloquentModel::find($id->value());
        return $model ? $this->toDomain($model) : null;
    }

    public function findByUser(UserId $userId): array
    {
        return InvoiceEloquentModel::where('user_id', $userId->value())
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function findByStatus(string $status): array
    {
        return InvoiceEloquentModel::where('status', $status)
            ->get()
            ->map(fn($model) => $this->toDomain($model))
            ->toArray();
    }

    public function delete(InvoiceId $id): void
    {
        InvoiceEloquentModel::destroy($id->value());
    }

    private function toDomain(InvoiceEloquentModel $model): Invoice
    {
        return Invoice::fromPersistence(
            $model->id,
            $model->number,
            $model->user_id,
            (float)$model->amount,
            $model->status,
            new \DateTimeImmutable($model->created_at->toAtomString()),
        );
    }
}
```

#### PASO 6: Crear Controller y Register en DI

```php
// src/app/Infrastructure/Http/Controllers/Api/InvoiceController.php
namespace App\Infrastructure\Http\Controllers\Api;

use App\Application\DTOs\Request\CreateInvoiceRequest;
use App\Application\UseCases\Invoices\CreateInvoiceUseCase;
use App\Infrastructure\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        private CreateInvoiceUseCase $createInvoiceUseCase,
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $dto = CreateInvoiceRequest::fromArray($request->all());
            $response = $this->createInvoiceUseCase->execute($dto);

            return response()->json($response->toArray(), 201);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }
}

// Registrar en Service Provider
// src/app/Infrastructure/Providers/RepositoryServiceProvider.php
public function register(): void
{
    $this->app->bind(
        InvoiceRepositoryInterface::class,
        InvoiceEloquentRepository::class,
    );
}

// src/app/Infrastructure/Providers/ApplicationServiceProvider.php
public function register(): void
{
    $this->app->bind(
        CreateInvoiceUseCase::class,
        fn($app) => new CreateInvoiceUseCase(
            $app->make(InvoiceRepositoryInterface::class),
        ),
    );
}

// src/routes/api.php
Route::post('/invoices', 'InvoiceController@store')->middleware('auth:api');
```

---

## 💎 EJEMPLOS PRÁCTICOS

Estos son ejemplos **REALES** del proyecto basado en casos existentes:

### ✅ CORRECTO: GenerateDocument (Documento)

```php
// Domain - Entidad pura
namespace App\Domain\Documents\Entities;

use App\Domain\Documents\ValueObjects\DocumentType;
use App\Domain\Documents\ValueObjects\DocumentNumber;

class SpanishDocument
{
    private DocumentType $type;
    private DocumentNumber $number;
    private \DateTimeImmutable $generatedAt;

    private function __construct(
        DocumentType $type,
        DocumentNumber $number,
        \DateTimeImmutable $generatedAt,
    ) {
        $this->type = $type;
        $this->number = $number;
        $this->generatedAt = $generatedAt;
    }

    public static function create(
        DocumentType $type,
        string $number,
    ): self {
        return new self(
            $type,
            new DocumentNumber($number, $type),
            new \DateTimeImmutable(),
        );
    }

    public function type(): DocumentType { return $this->type; }
    public function number(): DocumentNumber { return $this->number; }
    public function format(): string
    {
        return match($this->type) {
            DocumentType::DNI => 'Spanish DNI',
            DocumentType::NIE => 'Spanish NIE',
            DocumentType::CIF => 'Spanish CIF',
            DocumentType::NIF => 'Spanish NIF',
            DocumentType::SSN => 'US SSN',
        };
    }
}

// Application - Use Case
namespace App\Application\UseCases\Documents;

use App\Domain\Documents\Services\DocumentGeneratorInterface;
use App\Domain\Documents\ValueObjects\DocumentType;

class GenerateDocumentUseCase
{
    public function __construct(
        private DocumentGeneratorInterface $generator,
    ) {}

    public function execute(string $type): array
    {
        $documentType = DocumentType::from($type);
        $document = $this->generator->generate($documentType);

        return [
            'type' => $type,
            'number' => $document->number()->value(),
            'format' => $document->format(),
        ];
    }
}

// Infrastructure - Service
namespace App\Infrastructure\Services;

use App\Domain\Documents\Services\DocumentGeneratorInterface;
use App\Domain\Documents\ValueObjects\DocumentType;
use App\Domain\Documents\Entities\SpanishDocument;
use App\Helpers\DocumentHelper;

class DocumentGeneratorService implements DocumentGeneratorInterface
{
    public function generate(DocumentType $type): SpanishDocument
    {
        $number = match($type) {
            DocumentType::DNI => DocumentHelper::generateValidSpanishDni(),
            DocumentType::NIE => DocumentHelper::generateValidSpanishNie(),
            DocumentType::CIF => DocumentHelper::generateValidSpanishCif(),
            DocumentType::NIF => DocumentHelper::generateValidSpanishNif(),
            DocumentType::SSN => DocumentHelper::generateValidSsn(),
        };

        return SpanishDocument::create($type, $number);
    }
}

// Infrastructure - Controller (THIN)
namespace App\Infrastructure\Http\Controllers\Api;

use App\Application\UseCases\Documents\GenerateDocumentUseCase;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    public function __construct(
        private GenerateDocumentUseCase $generateDocumentUseCase,
    ) {}

    public function generateDni(): JsonResponse
    {
        $result = $this->generateDocumentUseCase->execute('dni');
        return response()->json($result, 200);
    }
}
```

### ❌ INCORRECTO (Viejo Patrón - NO HACER)

```php
// ❌ Controlador acoplado (ANTI-PATRÓN)
class GenerateDocumentController extends Controller
{
    public function generateDni(): JsonResponse
    {
        // ❌ Lógica directamente en el controller
        $dni = GenerateDocument::generateRandomDni();
        return response()->json(['dni' => $dni], 200);
    }
}

// ❌ Modelo con lógica estática (ANTI-PATRÓN)
class GenerateDocument
{
    public static function generateRandomDni()
    {
        // ❌ Lógica de negocio en modelo
        return generateValidSpanishDni();
    }
}
```

---

## 🚨 GESTIÓN DE EXCEPCIONES

### Excepciones de Dominio → HTTP Status Codes

Siempre mapear excepciones de negocio a códigos HTTP apropriados:

```php
class BaseException extends DomainException
{
    // Override en cada excepción específica:
    public function getHttpStatusCode(): int
    {
        // 400 - Bad Request (validación)
        // 401 - Unauthorized (autenticación)
        // 403 - Forbidden (autorización)
        // 404 - Not Found
        // 422 - Unprocessable Entity (lógica de negocio)
        // 500 - Internal Server Error (inesperado)
    }
}

// Ejemplos correctos:
class InvalidEmail extends DomainException
{
    public function getHttpStatusCode(): int { return 422; }
}

class UserNotFound extends DomainException
{
    public function getHttpStatusCode(): int { return 404; }
}

class InvalidPassword extends DomainException
{
    public function getHttpStatusCode(): int { return 400; }
}

class InsufficientPermissions extends DomainException
{
    public function getHttpStatusCode(): int { return 403; }
}
```

### Exception Handler

```php
// src/app/Infrastructure/Exceptions/DomainExceptionHandler.php
namespace App\Infrastructure\Exceptions;

use App\Domain\Shared\Exceptions\DomainException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class DomainExceptionHandler extends ExceptionHandler
{
    public function render($request, Throwable $exception)
    {
        // Manejar excepciones de dominio
        if ($exception instanceof DomainException && $request->expectsJson()) {
            return response()->json(
                [
                    'error' => class_basename($exception),
                    'message' => $exception->getMessage(),
                    'status' => $exception->getHttpStatusCode(),
                ],
                $exception->getHttpStatusCode(),
            );
        }

        // Validación Laravel
        if ($this->isHttpException($exception)) {
            return $this->renderHttpException($exception);
        }

        return parent::render($request, $exception);
    }
}
```

### En el Controller

```php
public function store(Request $request): JsonResponse
{
    try {
        $dto = CreateProductRequest::fromArray($request->all());
        $response = $this->createProductUseCase->execute($dto);
        return response()->json($response->toArray(), 201);
    } catch (ProductNotFound $e) {
        return response()->json(['error' => $e->getMessage()], 404);
    } catch (InvalidPrice $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }
    // Las excepciones no atrapadas se pasan al Handler global
}
```

---

## 🧪 TESTING

### Unit Tests: Dominio

```php
// tests/Unit/Domain/Products/Entities/ProductTest.php
namespace Tests\Unit\Domain\Products\Entities;

use App\Domain\Products\Entities\Product;
use App\Domain\Products\ValueObjects\ProductName;
use App\Domain\Products\ValueObjects\Money;
use App\Domain\Products\Exceptions\InvalidPrice;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function test_create_product_with_valid_data(): void
    {
        $product = Product::create(
            name: 'Laptop',
            price: 999.99,
        );

        $this->assertEquals('Laptop', $product->name()->value());
        $this->assertEquals(999.99, $product->price()->amount());
    }

    public function test_cannot_create_product_with_negative_price(): void
    {
        $this->expectException(InvalidPrice::class);

        Product::create(
            name: 'Laptop',
            price: -100,
        );
    }

    public function test_update_product_price(): void
    {
        $product = Product::create('Laptop', 999.99);
        $product->updatePrice(new Money(1299.99));

        $this->assertEquals(1299.99, $product->price()->amount());
    }
}
```

### Feature Tests: API

```php
// tests/Feature/Api/Products/CreateProductTest.php
namespace Tests\Feature\Api\Products;

use App\Domain\Products\Repositories\ProductRepositoryInterface;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    public function test_create_product_successfully(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Laptop',
            'price' => 999.99,
        ]);

        $response->assertStatus(201)
            ->assertJson(fn(AssertableJson $json) =>
                $json->where('name', 'Laptop')
                    ->where('price', 999.99)
                    ->has('id')
                    ->has('created_at')
            );

        // Verificar que se guardó en BD
        $this->assertDatabaseHas('products', [
            'name' => 'Laptop',
            'price' => 999.99,
        ]);
    }

    public function test_cannot_create_product_with_invalid_price(): void
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Laptop',
            'price' => -100,
        ]);

        $response->assertStatus(422);
    }
}
```

### Mocking Repositories

```php
public function test_use_case_with_mock_repository(): void
{
    $repository = $this->createMock(ProductRepositoryInterface::class);
    $repository->expects($this->once())->method('save');
    $repository->expects($this->once())
        ->method('findById')
        ->willReturn(Product::create('Test', 100));

    $useCase = new GetProductUseCase($repository);
    $result = $useCase->execute(1);

    $this->assertEquals('Test', $result->name);
}
```

---

## ✅ CHECKLIST DE VALIDACIÓN

Antes de hacer commit de un nuevo endpoint, verificar:

### Domain Layer
- [ ] ¿Creé Value Objects para validación encapsulada?
- [ ] ¿Creé Entities con factory methods (`create()`, `fromPersistence()`)?
- [ ] ¿Creé Repository Interface (Puerto)?
- [ ] ¿Creé excepciones de Dominio específicas?
- [ ] ¿Domain NO importa nada de Application/Infrastructure?
- [ ] ¿Domain tiene tests unitarios (80%+)?

### Application Layer
- [ ] ¿Creé DTOs Request/Response simples?
- [ ] ¿Creé Mapper para DTO ↔ Entity?
- [ ] ¿Creé Use Case con orquestación clara?
- [ ] ¿Use Case recibe Repositories inyectados?
- [ ] ¿Application NO importa Infrastructure directamente?
- [ ] ¿Use Case tiene tests de integración?

### Infrastructure Layer
- [ ] ¿Creé Eloquent Model sin lógica?
- [ ] ¿Creé Repository implementando Interface?
- [ ] ¿Repository convierte Entity ↔ EloquentModel?
- [ ] ¿Controllers son THIN (delegan todo a Use Cases)?
- [ ] ¿Registré bindings en Service Providers?
- [ ] ¿Controllers tienen tests?

### HTTP
- [ ] ¿Rutas en `routes/api.php` claras?
- [ ] ¿Middleware de autenticación/autorización?
- [ ] ¿Controller maneja excepciones?
- [ ] ¿Respuestas JSON con status codes correctos?
- [ ] ¿Documentación de endpoint (Scribe)?

### Testing
- [ ] ¿Tests unitarios del Dominio (80%)?
- [ ] ¿Tests de Casos de Uso?
- [ ] ¿Tests API end-to-end?
- [ ] ¿Cobertura >= 80%?

### Code Quality
- [ ] ¿Código sigue PSR-12 (Laravel Pint)?
- [ ] ¿Nombres claros y consistentes?
- [ ] ¿Sin warnings de PHP Stan?
- [ ] ¿Sin código muerto/comentado?
- [ ] ¿Documentación suficiente?

---

## 📞 FAQ - PREGUNTAS FRECUENTES

### P: ¿Por qué Value Objects en lugar de tipos primitivos?

**R**: Value Objects encapsulan validación. Si usas string, validar Email en 10 lugares diferentes. Con VO lo haces una vez.

```php
// ❌ SIN VO - validar en cada lugar
$email = $data['email'];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    throw new Error('Invalid email'); // En 10 controllers...
}

// ✅ CON VO - validar una vez
$email = new Email($data['email']);
```

### P: ¿Cuándo usar Service vs repositorio?

**R**:
- **Repository**: Acceso a datos (BD)
- **DomainService**: Lógica que involucra múltiples agregados
- **UseCase**: Orquestación de un acción de usuario

### P: ¿Qué pasa si necesito transaccionalidad?

**R**: Manejar en el Use Case:

```php
public function execute(Request $request): Response
{
    DB::beginTransaction();
    try {
        $invoice = $this->createInvoice($request);
        $this->updateInventory($invoice);
        DB::commit();
        return $response;
    } catch (Throwable $e) {
        DB::rollBack();
        throw $e;
    }
}
```

### P: ¿Pueden los DTOs tener métodos?

**R**: Sí, pero solo de TRANSFORMACIÓN de datos, no lógica de negocio:

```php
// ✅ OK - Métodos de transformación
class ProductResponse
{
    public function toArray(): array { ... }
    public function discountedPrice(float $percentage): float { ... }
}

// ❌ INCORRECTO - Lógica de negocio
class ProductResponse
{
    public function updatePrice(float $newPrice): void { ... } // ❌ NO
    public function calculateTax(): float { ... } // ❌ NO
}
```

### P: ¿Debo usar Events de dominio?

**R**: Sí, para acciones importantes:

```php
public class Order
{
    public function place(): void
    {
        // ... lógica
        event(new OrderPlaced($this));
    }
}
```

Pero para este proyecto, empieza sin events (KISS).

---

## 🎓 CONCLUSIÓN

Esta arquitectura nos proporciona:

✅ **Código testeable** - Lógica pura sin framework
✅ **Mantenible** - Cambios localizados
✅ **Escalable** - Fácil agregar features
✅ **Profesional** - Align con DDD standards
✅ **Team-friendly** - Toda el equipo entiende

**Cada nuevo endpoint que no siga esta guía será rechazado en code review.**

¡Bienvenido a arquitectura de nivel empresarial! 🚀

---

**Documento: hexagonal_api_guidelines.md**
**Versión**: 1.0
**Próxima revisión**: Q2 2026
