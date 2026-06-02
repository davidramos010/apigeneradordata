---
name: api-conventions
description: Convenciones de diseño para las APIs de este proyecto (validación, formato de errores, OpenAPI, tests, entregables). Aplícala SIEMPRE al crear, revisar o documentar endpoints, definir respuestas de error, o montar la estructura de un servicio nuevo, aunque no se nombre la skill explícitamente.
---

# Convenciones de las APIs del proyecto

Patrones a seguir al construir cualquier endpoint, para no repetir las mismas instrucciones en cada tarea.

## Stack

Node.js + Express, o FastAPI si el proyecto es Python. Mantén la elección coherente dentro de un mismo servicio.

## Validación de input

- **Node**: `zod`.
- **Python**: `pydantic`.

Valida en el borde (al entrar la petición), nunca confíes en el cliente.

## Formato de respuestas de error

- `400` para input inválido, con detalle de qué campo falló.
- `500` con mensaje **genérico** (no filtrar trazas ni internals al cliente); registrar el detalle en logs del servidor.

## Documentación

- OpenAPI/Swagger expuesto en `/api/docs`.
- `README.md` con ejemplos de `curl` para cada endpoint.

## Tests

Cada endpoint con tests que cubran el camino feliz y los casos límite del dominio (longitud, prefijo, dígitos/letras de control, valores frontera). Los tests deben pasar antes de considerar la tarea terminada.

## Entregables de un servicio nuevo

1. Código completo del proyecto.
2. `package.json` (Node) o `pyproject.toml` (Python) con dependencias.
3. Tests pasando.
4. `README.md` con instrucciones de instalación y uso.
5. Colección de Postman o ejemplos de `curl` para probar.

## Skills de dominio relacionadas

Cuando el endpoint trabaje con identificadores españoles, apóyate en: `cups` (puntos de suministro), `iban-es` (cuentas bancarias) y `test-data-es` (DNI/NIE, tarjetas).
