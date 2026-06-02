---
name: test-data-es
description: Genera y valida datos de prueba españoles y de tarjetas de pago — DNI, NIE, NIF y números de tarjeta (Luhn) con sus campos asociados (caducidad, CVV). Ósala SIEMPRE que el trabajo toque generación o validación de identificadores españoles o tarjetas de prueba, aunque no se nombre la skill: sembrar datos de test, validar formularios, o construir endpoints/sandbox de pagos (Stripe, Redsys).
---

# Datos de prueba ES — DNI/NIE/NIF y tarjetas

Todo lo de aquí produce datos **matemáticamente válidos pero ficticios**, solo para desarrollo y sandbox.

## DNI / NIE

Tabla de letras (23): `TRWAGMYFPDXBNJZSQVHLCKE`.

- **DNI**: 8 dígitos + letra. `letra = TABLA[numero % 23]`.
- **NIE**: empieza por `X`, `Y` o `Z`. Sustituir por `0`, `1`, `2` respectivamente, concatenar con los 7 dígitos, y calcular la letra igual que el DNI.

## NIF (persona jurídica)

Empieza por letra de organización (`A`,`B`,`C`,`D`,`E`,`F`,`G`,`H`,`J`,`N`,`P`,`Q`,`R`,`S`,`U`,`V`,`W`) + 7 dígitos + carácter de control (dígito o letra según el tipo). Es más complejo que el DNI; ver detalle en el script si lo necesitas.

## Tarjetas de pago (ISO/IEC 7812 + Luhn)

Prefijos (BIN/IIN) por red:

| Red        | Prefijo            | Longitud |
| ---------- | ------------------ | -------- |
| Visa       | 4                  | 16       |
| Mastercard | 51–55, 2221–2720   | 16       |
| Amex       | 34, 37             | 15       |
| Discover   | 6011, 65           | 16       |

- **Dígito de control**: algoritmo de **Luhn** sobre todos los dígitos.
- **Caducidad**: `MM/YY`, mes 01–12, año actual en adelante.
- **CVV/CVC**: 3 dígitos (4 para Amex, llamado CID).

Implementación de referencia (DNI, NIE y tarjetas Luhn) en `scripts/datos.py`. Comprueba con `python3 scripts/datos.py`.

## Aviso

Datos no asociados a ninguna persona o cuenta real. Ósalos solo en entornos de prueba o sandbox de pasarelas (Stripe, Redsys).
