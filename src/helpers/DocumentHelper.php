<?php

/**
 * Generate valid Spanish identification numbers (DNI, NIE, CIF).
 *
 * @return string
 */
function generateValidSpanishDni(): string
{
    // Array de letras de control para el DNI
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";

    // Generar un número aleatorio de 8 dígitos
    $dniNumber = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Calcular el índice de la letra de control
    $index = intval($dniNumber) % 23;

    // Obtener la letra de control correspondiente
    $controlLetter = $controlLetters[$index];

    // Retornar el DNI completo
    return $dniNumber . $controlLetter;
}

/**
   NIE (Número de Identidad de Extranjero):
   - Starts with `X`, `Y`, or `Z`.
   - The numeric equivalent of the prefix is used to calculate the control letter.
 */
function generateValidSpanishNie(): string
{
    // Array de letras de control para el NIE
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";

    // Generate a random starting letter (X, Y, or Z)
    $prefix = ['X', 'Y', 'Z'][rand(0, 2)];

    // Generate a random 7-digit number
    $number = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

    // Replace the prefix with its numeric equivalent for control letter calculation
    $numericPrefix = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], $prefix);

    // Calculate the control letter
    $index = intval($numericPrefix . $number) % 23;
    $controlLetter = $controlLetters[$index];

    // Return the NIE
    return $prefix . $number . $controlLetter;
}

/**
 * **CIF**:
 *  - Starts with a letter indicating the type of entity.
 *  - The control character is calculated based on the sum of digits at odd and even positions.
 *  - The control character can be a digit or a letter depending on the prefix.
 *
 * @return string
 */
function generateValidSpanishCif(): string
{
    // Array of valid CIF starting letters
    $entityTypes = "ABCDEFGHJKLMNPQRSUVW";

    // Generate a random starting letter
    $prefix = $entityTypes[rand(0, strlen($entityTypes) - 1)];

    // Generate a random 7-digit number
    $number = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);

    // Calculate the control character
    $sumEven = $sumOdd = 0;
    for ($i = 0; $i < 7; $i++) {
        $digit = intval($number[$i]);
        if ($i % 2 === 0) { // Odd positions (1-based index)
            $double = $digit * 2;
            $sumOdd += ($double > 9) ? $double - 9 : $double;
        } else { // Even positions
            $sumEven += $digit;
        }
    }
    $totalSum = $sumEven + $sumOdd;
    $controlDigit = (10 - ($totalSum % 10)) % 10;

    // Determine if the control character is a digit or letter
    $controlChar = ctype_alpha($prefix) && in_array($prefix, ['K', 'P', 'Q', 'S', 'W'])
        ? chr(65 + $controlDigit) // Convert to letter (A-J)
        : strval($controlDigit);  // Use digit

    // Return the CIF
    return $prefix . $number . $controlChar;
}

/**
 * Genera un CIF válido según el tipo de entidad especificado por $strType.
 * El parámetro $strType debe ser una letra que indique el tipo de entidad (por ejemplo, 'A' para sociedades anónimas, 'B' para sociedades de responsabilidad limitada,
 * 
 * Sociedades Mercantiles y de Capital
 * A: Sociedades Anónimas (S.A.).
 * B: Sociedades de Responsabilidad Limitada (S.L. o S.R.L.).
 * C: Sociedades Colectivas.
 * D: Sociedades Comanditarias.
 * 
 * Entidades Sociales, Civiles y Sin Personalidad Jurídica
 * E: Comunidades de bienes, herencias yacentes y otras entidades sin personalidad jurídica.
 * F: Sociedades Cooperativas.
 * G: Asociaciones y Fundaciones.
 * H: Comunidades de propietarios en régimen de propiedad horizontal.
 * J: Sociedades Civiles (con o sin personalidad jurídica).
 * U: Uniones Temporales de Empresas (UTE).
 * V: Otros tipos de entidades no definidos en el resto de claves.
 * 
 * Administración y Entidades Públicas
 * P: Corporaciones Locales (Ayuntamientos, Diputaciones).
 * Q: Organismos públicos.
 * S: Órganos de la Administración del Estado y de las Comunidades Autónomas.
 * 
 * Entidades Religiosas y Extranjeras
 * R: Congregaciones e instituciones religiosas.
 * N: Entidades extranjeras (empresas internacionales que operan en España pero no tienen domicilio social aquí).
 * W: Establecimientos permanentes de entidades no residentes en España.
 * 
 * Nota Adicional (Personas Físicas Especiales):
 * Aunque el CIF tradicionalmente aplicaba a empresas, dentro del sistema actual del NIF existen tres letras adicionales reservadas para personas físicas que se encuentran en situaciones especiales (no tienen DNI * ni NIE):
 * 
 * K: Personas físicas españolas, menores de 14 años, residentes en España sin DNI.
 * L: Personas físicas españolas, no residentes en España y sin DNI.
 * M: Personas físicas extranjeras que carecen de NIE.
 */
function generateValidSpanishCifByType(string $strType): string
{
    $tipo = strtoupper(trim($strType));
    $letrasValidas = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'N', 'P', 'Q', 'R', 'S', 'U', 'V', 'W', 'L', 'M' ]; // Incluye letras para personas físicas especiales

    if (!in_array($tipo, $letrasValidas)) {
        throw new Exception("Tipo de letra no válido para un CIF. Letras válidas: " . implode(", ", $letrasValidas));
    }

    // 1. Generar 7 dígitos aleatorios
    // Los dos primeros suelen ser el código de la provincia (01-99)
    $provincia = str_pad(mt_rand(1, 99), 2, '0', STR_PAD_LEFT);
    // Los cinco siguientes son correlativos de inscripción
    $correlativo = str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
    $digitos = $provincia . $correlativo;

    // 2. Calcular el dígito de control
    $sumaPar = 0;
    $sumaImpar = 0;

    for ($i = 0; $i < 7; $i++) {
        $num = (int)$digitos[$i];
        
        if ($i % 2 === 0) {
            // Posiciones impares (índices 0, 2, 4, 6) -> Se multiplican por 2
            // Si el resultado tiene dos cifras, se suman entre sí.
            $multiplicado = (string)($num * 2);
            $sumaImpar += (int)($multiplicado[0]) + (isset($multiplicado[1]) ? (int)($multiplicado[1]) : 0);
        } else {
            // Posiciones pares (índices 1, 3, 5) -> Se suman tal cual
            $sumaPar += $num;
        }
    }

    $sumaTotal = $sumaPar + $sumaImpar;
    
    // Obtenemos las unidades de la suma total
    $unidades = $sumaTotal % 10;
    
    // El dígito de control numérico es 10 menos las unidades (si unidades es 0, el control es 0)
    $digitoControl = $unidades === 0 ? 0 : 10 - $unidades;

    // 3. Determinar si el carácter de control final debe ser Letra o Número
    // Mapa de correspondencia para letras de control: 0=J, 1=A, 2=B, 3=C...
    $mapaLetrasControl = 'JABCDEFGHI'; 
    
    $tiposQueUsanLetra = ['K', 'P', 'Q', 'S', 'W'];
    $tiposQueUsanNumero = ['A', 'B', 'E', 'H'];
    // El resto (C, D, F, G, J, N, R, U, V) pueden usar ambos. Por convención, usaremos número para ellos.

    if (in_array($tipo, $tiposQueUsanLetra)) {
        // Se devuelve con letra al final
        $caracterControl = $mapaLetrasControl[$digitoControl];
    } else {
        // Se devuelve con número al final
        $caracterControl = $digitoControl;
    }

    return $tipo . $digitos . $caracterControl;
}

function generateValidSpanishNif(): string
{
    // Array de letras de control para el NIF
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";

    // Generar un número aleatorio de 8 dígitos
    $nifNumber = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Calcular el índice de la letra de control
    $index = intval($nifNumber) % 23;

    // Obtener la letra de control correspondiente
    $controlLetter = $controlLetters[$index];

    // Retornar el NIF completo
    return $nifNumber . $controlLetter;
}

function generateValidSsn(): string
{
    // Generar un número aleatorio de 8 dígitos
    $ssnNumber = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);

    // Retornar el SSN completo
    return $ssnNumber;
}

// ─── Validation functions ─────────────────────────────────────────────────────

/**
 * Validates a Spanish DNI (Documento Nacional de Identidad).
 *
 * @param string $dni
 * @return bool
 */
function validateSpanishDni(string $dni): bool
{
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";
    $dni = strtoupper(trim($dni));

    if (!preg_match('/^\d{8}[A-Z]$/', $dni)) return false;

    return $controlLetters[intval(substr($dni, 0, 8)) % 23] === substr($dni, -1);
}

/**
 * Validates a Spanish NIE (Número de Identificación de Extranjero).
 *
 * @param string $nie
 * @return bool
 */
function validateSpanishNie(string $nie): bool
{
    $controlLetters = "TRWAGMYFPDXBNJZSQVHLCKE";
    $nie = strtoupper(trim($nie));

    if (!preg_match('/^[XYZ]\d{7}[A-Z]$/', $nie)) return false;

    $numeric = str_replace(['X', 'Y', 'Z'], ['0', '1', '2'], $nie);
    return $controlLetters[intval(substr($numeric, 0, 8)) % 23] === substr($nie, -1);
}

/**
 * Validates a Spanish CIF (Código de Identificación Fiscal).
 *
 * @param string $cif
 * @return bool
 */
function validateSpanishCif(string $cif): bool
{
    $cif         = strtoupper(trim($cif));
    $validTypes  = ['A','B','C','D','E','F','G','H','J','K','L','M','N','P','Q','R','S','U','V','W'];
    $controlMap  = 'JABCDEFGHI';
    $letterTypes = ['K','P','Q','S','W'];
    $numberTypes = ['A','B','E','H'];

    if (!preg_match('/^[A-Z]\d{7}[0-9A-J]$/', $cif)) return false;

    $prefix = $cif[0];
    if (!in_array($prefix, $validTypes)) return false;

    $digits  = substr($cif, 1, 7);
    $control = substr($cif, -1);

    $sumaPar = $sumaImpar = 0;
    for ($i = 0; $i < 7; $i++) {
        $num = intval($digits[$i]);
        if ($i % 2 === 0) {
            $mult = strval($num * 2);
            $sumaImpar += intval($mult[0]) + (isset($mult[1]) ? intval($mult[1]) : 0);
        } else {
            $sumaPar += $num;
        }
    }

    $unidades      = ($sumaPar + $sumaImpar) % 10;
    $digitoControl = $unidades === 0 ? 0 : 10 - $unidades;

    if (in_array($prefix, $letterTypes))  return $control === $controlMap[$digitoControl];
    if (in_array($prefix, $numberTypes))  return $control === strval($digitoControl);

    return $control === $controlMap[$digitoControl] || $control === strval($digitoControl);
}

/**
 * Validates a Spanish NIF (Número de Identificación Fiscal).
 *
 * @param string $nif
 * @return bool
 */
function validateSpanishNif(string $nif): bool
{
    return validateSpanishDni($nif);
}

/**
 * Validates a Social Security Number (SSN).
 *
 * @param string $ssn
 * @return bool
 */
function validateSsn(string $ssn): bool
{
    $ssn = trim($ssn);
    return (bool) preg_match('/^\d{8}$/', $ssn)
        || (bool) preg_match('/^\d{3}-\d{2}-\d{4}$/', $ssn);
}

/**
 * Validates a passport number.
 *
 * @param string $pasaporte
 * @return bool
 */
function validatePasaporte(string $pasaporte): bool
{
    return (bool) preg_match('/^[A-Z]{2,3}\d{6}$/i', trim($pasaporte));
}

/**
 * Identifies the type of a given document.
 *
 * @param string $document
 * @return string|null
 */
function identifyDocumentType(string $document): ?string
{
    $doc = strtoupper(trim($document));

    if (validateSpanishNie($doc)) return 'NIE';
    if (validateSpanishCif($doc)) return 'CIF';
    if (validateSpanishDni($doc)) return 'DNI';
    if (validateSsn($doc))        return 'SSN';
    if (validatePasaporte($doc))  return 'PASAPORTE';

    return null;
}
