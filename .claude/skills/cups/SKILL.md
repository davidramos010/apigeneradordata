---
name: cups
description: Genera y valida CUPS españoles (Código Universal del Punto de Suministro) de luz y gas. Ósala SIEMPRE que el trabajo toque CUPS, aunque no se nombre la skill: crear o revisar endpoints de generación/validación, calcular o comprobar las letras de control, trabajar con códigos de distribuidoras, o escribir tests de formato de CUPS.
---

# CUPS — generación y validación

El CUPS identifica un punto de suministro de electricidad o gas en España. Esta skill contiene la estructura y el algoritmo de control para no tener que explicarlos de nuevo.

## Estructura

Longitud de **20 o 22 caracteres**:

```
ES + DDDD + SSSSSSSSSSSS + CC + [F] + [T]
↑    ↑      ↑              ↑    ↑     ↑ letra de tipo (opcional): F, P, R, M…
↑    ↑      ↑              ↑    └───── dígito de frontera (opcional, 0–9)
↑    ↑      ↑              └────────── 2 letras de control (calculadas)
↑    ↑      └─────────────────────────  12 dígitos: número de suministro
↑    └───────────────────────────────── 4 dígitos: código de distribuidora
└────────────────────────────────────── prefijo fijo "ES"
```

Las posiciones que entran en el cálculo de control son los **16 dígitos centrales** (distribuidora + suministro, posiciones 3 a 18).

## Algoritmo de las letras de control

Tabla de referencia (23 caracteres): `TRWAGMYFPDXBNJZSQVHLCKE`

1. Tomar los 16 dígitos centrales como un entero `n`.
2. `resto = n mod 529`
3. `c = resto // 23` (división entera) → índice de la **primera** letra
4. `r = resto % 23` → índice de la **segunda** letra
5. Letras de control = `TABLA[c] + TABLA[r]`

Implementación de referencia en `scripts/cups.py` (genera y valida). Verifica el resultado con `python3 scripts/cups.py`.

## Códigos reales de distribuidoras

Para datos de prueba realistas: Iberdrola `0021`, Endesa `0031`, Naturgy `0029`.

## Casos de test obligatorios

Al validar, cubrir: longitud incorrecta, prefijo distinto de `ES`, dígitos no numéricos en las posiciones numéricas, letras de control incorrectas, CUPS de 22 caracteres válido, y sufijo de frontera inválido.

## Aviso

El algoritmo de letras de control es el estándar documentado, pero algunas distribuidoras han tenido variantes históricas en el sufijo de frontera. Valida contra 2–3 CUPS reales conocidos antes de dar por buena una API en producción.
