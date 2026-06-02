#!/usr/bin/env python3
"""Generación y validación de IBAN y CCC españoles."""

import random
import re

PESOS = [1, 2, 4, 8, 5, 10, 9, 7, 3, 6]


def _dc(diez_digitos: str) -> int:
    suma = sum(int(d) * p for d, p in zip(diez_digitos, PESOS))
    dc = 11 - (suma % 11)
    return {10: 1, 11: 0}.get(dc, dc)


def dc_ccc(entidad: str, oficina: str, cuenta: str) -> str:
    dc1 = _dc("00" + entidad + oficina)
    dc2 = _dc(cuenta)
    return f"{dc1}{dc2}"


def dc_iban(ccc20: str) -> str:
    n = int(ccc20 + "1428" + "00")  # 14=E, 28=S
    return f"{98 - (n % 97):02d}"


def generar() -> str:
    entidad = f"{random.randint(0, 9999):04d}"
    oficina = f"{random.randint(0, 9999):04d}"
    cuenta = f"{random.randint(0, 9999999999):010d}"
    ccc = entidad + oficina + dc_ccc(entidad, oficina, cuenta) + cuenta
    return "ES" + dc_iban(ccc) + ccc


def validar(iban: str) -> bool:
    iban = iban.replace(" ", "").upper()
    if not re.fullmatch(r"ES\d{22}", iban):
        return False
    ccc = iban[4:]
    entidad, oficina, dc, cuenta = ccc[:4], ccc[4:8], ccc[8:10], ccc[10:]
    if dc_ccc(entidad, oficina, cuenta) != dc:
        return False
    return dc_iban(ccc) == iban[2:4]


if __name__ == "__main__":
    for _ in range(3):
        i = generar()
        print(f"{i}  válido={validar(i)}")
    print(f"ES000000000000000000000  válido={validar('ES000000000000000000000')}")
