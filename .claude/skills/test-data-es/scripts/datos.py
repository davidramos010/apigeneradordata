#!/usr/bin/env python3
"""Generación y validación de DNI, NIE y tarjetas de pago (Luhn)."""

import random
from datetime import date

LETRAS = "TRWAGMYFPDXBNJZSQVHLCKE"
PREFIJOS = {"visa": ("4", 16), "mastercard": ("51", 16),
            "amex": ("34", 15), "discover": ("6011", 16)}


# ── DNI / NIE ─────────────────────────────────────────────────
def generar_dni() -> str:
    n = random.randint(0, 99999999)
    return f"{n:08d}{LETRAS[n % 23]}"


def validar_dni(dni: str) -> bool:
    dni = dni.strip().upper()
    if len(dni) != 9 or not dni[:8].isdigit():
        return False
    return dni[8] == LETRAS[int(dni[:8]) % 23]


def validar_nie(nie: str) -> bool:
    nie = nie.strip().upper()
    if len(nie) != 9 or nie[0] not in "XYZ" or not nie[1:8].isdigit():
        return False
    num = str("XYZ".index(nie[0])) + nie[1:8]
    return nie[8] == LETRAS[int(num) % 23]


# ── Tarjetas (Luhn) ───────────────────────────────────────────
def _luhn_check_digit(sin_control: str) -> str:
    suma, par = 0, True
    for d in reversed(sin_control):
        x = int(d) * 2 if par else int(d)
        suma += x - 9 if x > 9 else x
        par = not par
    return str((10 - suma % 10) % 10)


def validar_luhn(numero: str) -> bool:
    numero = numero.replace(" ", "")
    if not numero.isdigit():
        return False
    suma, par = 0, False
    for d in reversed(numero):
        x = int(d) * 2 if par else int(d)
        suma += x - 9 if x > 9 else x
        par = not par
    return suma % 10 == 0


def generar_tarjeta(red: str = "visa") -> str:
    prefijo, longitud = PREFIJOS[red]
    cuerpo = prefijo + "".join(
        str(random.randint(0, 9)) for _ in range(longitud - len(prefijo) - 1))
    return cuerpo + _luhn_check_digit(cuerpo)


def caducidad() -> str:
    hoy = date.today()
    return f"{random.randint(1, 12):02d}/{(hoy.year + random.randint(0, 4)) % 100:02d}"


def cvv(amex: bool = False) -> str:
    return "".join(str(random.randint(0, 9)) for _ in range(4 if amex else 3))


if __name__ == "__main__":
    dni = generar_dni()
    print(f"DNI {dni}  válido={validar_dni(dni)}")
    print(f"NIE X1234567L  válido={validar_nie('X1234567L')}")
    for red in PREFIJOS:
        t = generar_tarjeta(red)
        print(f"{red:11s} {t}  luhn={validar_luhn(t)}  exp={caducidad()} cvv={cvv(red=='amex')}")
