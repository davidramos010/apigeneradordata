---
name: iban-es
description: Genera y valida IBAN y CCC (Código Cuenta Cliente) españoles. Ósala SIEMPRE que el trabajo toque cuentas bancarias españolas, aunque no se nombre la skill: calcular o comprobar dígitos de control de CCC o IBAN, generar cuentas de prueba, o escribir validadores/tests de formato bancario.
---

# IBAN y CCC español — generación y validación

El IBAN español tiene **24 caracteres**: `ES` + 2 dígitos de control de IBAN + 20 dígitos de CCC.

## Estructura del CCC (20 dígitos)

```
EEEE OOOO DD CCCCCCCCCC
↑    ↑    ↑  ↑ cuenta (10 dígitos)
↑    ↑    └─── 2 dígitos de control (DC1 + DC2, calculados)
↑    └──────── oficina/sucursal (4 dígitos)
└───────────── entidad/banco (4 dígitos)
```

## Dígitos de control del CCC (módulo 11)

Pesos: `[1, 2, 4, 8, 5, 10, 9, 7, 3, 6]`

- **DC1** (controla entidad+oficina): cadena `"00" + entidad + oficina` (10 dígitos). Multiplicar cada dígito por su peso, sumar, `dc = 11 - (suma % 11)`. Si `dc == 10` → `1`; si `dc == 11` → `0`.
- **DC2** (controla la cuenta): los 10 dígitos de cuenta × pesos, misma fórmula.

## Dígitos de control del IBAN (módulo 97)

1. Construir `CCC(20) + "1428" + "00"` (donde `14`=E, `28`=S de "ES").
2. `control = 98 - (n % 97)`, rellenado a 2 dígitos.

Implementación de referencia (generar + validar) en `scripts/iban.py`. Comprueba con `python3 scripts/iban.py`.

## Aviso

Genera IBAN **matemáticamente válidos** pero no asociados a ninguna cuenta real. Aptos solo para entornos de desarrollo y sandbox.
