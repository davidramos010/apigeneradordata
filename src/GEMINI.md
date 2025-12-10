# Documentación del Proyecto - API Generador de Datos

## 📋 Descripción General

Este es un proyecto de **Laravel 11** con base de datos **MySQL**, alojado en [GitHub - apigeneradordata](https://github.com/davidramos010/apigeneradordata). Funciona localmente con **Docker** y está diseñado para ser desplegado en un servidor proporcionando servicios de **APIs REST**.

### Objetivo Principal

Desarrollar una API REST que implemente las mejores prácticas de Laravel 11, utilizando Eloquent y siguiendo los principios de arquitectura limpia y patrones de diseño establecidos.

---

## 📚 Referencias y Bases del Proyecto

El proyecto se fundamenta en las siguientes referencias:

1. **Estructura Base**: [restapi-auth-roles](https://github.com/cesarsebastiandev/restapi-auth-roles)
   - Referencia inicial para la autenticación y gestión de roles

2. **Principios SOLID**: [Principios SOLID en API REST con Laravel](https://notasweb.me/entrada/principios-solid-aplicado-a-una-api-rest-en-laravel/)
   - Documentación teórica de aplicación de SOLID

3. **Arquitectura Referente**: [solid-api](https://github.com/PortilloDev/solid-api)
   - Estructura y patrón a seguir para refactorización y nuevos módulos

---

## 🔄 Estado Actual

### Endpoints Implementados

**Generación de Documentos:**
- `GET api/generate-dni` - Generar DNI
- `GET api/generate-nie` - Generar NIE
- `GET api/generate-nif` - Generar NIF
- `GET api/generate-ssn` - Generar SSN

**Validación de Documentos:**
- `POST api/validate-dni` - Validar DNI
- `POST api/validate-nie` - Validar NIE
- `POST api/validate-nif` - Validar NIF
- `POST api/validate-cif` - Validar CIF

**Gestión de Productos:**
- `GET api/products` - Listar productos
- `POST api/product` - Crear producto
- `GET api/product/{id}` - Obtener producto específico
- `PUT api/product/{id}` - Actualizar producto
- `DELETE api/product/{id}` - Eliminar producto

---

## 🚀 Plan de Refactorización

### Objetivo

Aplicar arquitectura **SOLID** y patrones de diseño siguiendo como referencia principal la estructura del proyecto [solid-api](https://github.com/PortilloDev/solid-api).

### Áreas de Enfoque

1. **Separación de responsabilidades**: Implementar Controllers, Services, Repositories
2. **Inyección de dependencias**: Utilizar contenedor de IoC de Laravel
3. **Interfaces y contratos**: Definir contratos para componentes reutilizables
4. **Validación y manejo de errores**: Centralizar lógica de validación
5. **Documentación y testing**: Mejorar cobertura de tests

---

## 📝 Notas

- Aplicar cambios de forma incremental para no romper funcionalidad actual
- Revisar buenas prácticas de Laravel 11 en cada refactorización
- Mantener la documentación actualizada durante el proceso
