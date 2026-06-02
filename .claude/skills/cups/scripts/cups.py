#!/usr/bin/env python3
"""Generación y validación de CUPS españoles (luz y gas)."""

import random
import re

TABLA = "TRWAGMYFPDXBNJZSQVHLCKE"  # 23 caracteres
DISTRIBUIDORAS = ("0021", "0031", "0029")  # Iberdrola, Endesa, Naturgy


def letras_control(centrales16: str) -> str:
    """Calcula las 2 letras de control a partir de los 16 dígitos centrales."""
    if len(centrales16) != 16 or not centrales16.isdigit():
        raise ValueError("Se esperan 16 dígitos centrales")
    resto = int(centrales16) % 529
    c, r = divmod(resto, 23)
    return TABLA[c] + TABLA[r]


def generar(distribuidora: str | None = None, frontera: bool = False) -> str:
    """Genera un CUPS válido. Si frontera=True añade dígito (1) + letra (F)."""
    dist = distribuidora or random.choice(DISTRIBUIDORAS)
    suministro = "".join(str(random.randint(0, 9)) for _ in range(12))
    centrales = dist + suministro
    cups = "ES" + centrales + letras_control(centrales)
    if frontera:
        cups += f"{random.randint(0, 9)}F"
    return cups


def validar(cups: str) -> bool:
    """Valida formato y letras de control de un CUPS de 20 o 22 caracteres."""
    cups = cups.strip().upper()
    if not re.fullmatch(r"ES\d{16}[A-Z]{2}(\d[A-Z])?", cups):
        return False
    if len(cups) not in (20, 22):
        return False
    centrales = cups[2:18]
    return cups[18:20] == letras_control(centrales)


if __name__ == "__main__":
    for frontera in (False, True):
        c = generar(frontera=frontera)
        print(f"{c}  válido={validar(c)}  ({len(c)} chars)")
    # Casos negativos esperados (False):
    for malo in ("ES0021000000000000AA", "1234567890123456789X", "ESxx"):
        print(f"{malo}  válido={validar(malo)}")
