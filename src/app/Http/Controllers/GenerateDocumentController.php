<?php

namespace App\Http\Controllers;

use App\Models\GenerateDocument;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * GenerateDocumentController handles the generation of various types of documents such as DNI, CIF, NIE, NIF, and SSN. Each method in this controller generates a specific type of document and returns it as a JSON response. The endpoints for generating these documents are protected by authentication, ensuring that only authenticated users can access them.
 * @group 2. Document Generation
 */
class GenerateDocumentController extends Controller
{
    /**
     * Generate a random document number.
     * 
     * Permission: Only authenticated users can access this endpoint.
     * this endpoint generates a random DNI (Documento Nacional de Identidad) number, which is a unique identifier used in Spain for individuals. The generated DNI consists of 8 digits followed by a letter, and it is commonly used for identification purposes in various administrative and legal processes.
     * The "result" query parameter allows the user to specify how many DNI numbers to generate, with a default value of 1 and a maximum limit of 20. The generated DNI numbers are returned as a JSON array in the response.
     * 
     * @authenticated
     * @queryParam result integer The number of DNI numbers to generate. Default: 1. Min: 1. Max: 20.
     * 
     * @response 200 {
     *  ["12345678A", "87654321B"]
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDni(Request $request): JsonResponse
    {
        $result = (int) $request->query('result', 1);
        // Validate the result parameter (minimum 1, maximum 20)
        if ($result < 1 || $result > 20) {
            return response()->json(['message' => 'The result parameter must be between 1 and 20.'], 400);
        }
        $arrDnis = [];
        for ($i = 0; $i < $result; $i++) {
            $arrDnis[] = GenerateDocument::generateRandomDni();
        }
        // Return the generated DNI as a JSON response
        return response()->json($arrDnis, 200);
    }

    /**
     * Generate random CIF numbers.
     * 
     * Permission: Only authenticated users can access this endpoint.
     * This endpoint generates one or more random CIF (Código de Identificación Fiscal) numbers based on the "result" parameter.
     * The "result" parameter specifies how many CIFs to generate (default: 1, minimum: 1, maximum: 20).
     *
     * @authenticated
     * @queryParam result integer The number of CIFs to generate. Default: 1. Min: 1. Max: 20.
     * 
     * @response 200 {
     *   ["A12345678", "B87654321"]
     * }
     * @response 400 {
     *   "message": "The result parameter must be between 1 and 20."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateCif(Request $request): JsonResponse
    {
        // Get the result parameter from the query string, default to 1
        $result = (int) $request->query('result', 1);

        // Validate the result parameter (minimum 1, maximum 20)
        if ($result < 1 || $result > 20) {
            return response()->json(['message' => 'The result parameter must be between 1 and 20.'], 400);
        }

        // Generate multiple CIFs
        $cifs = [];
        for ($i = 0; $i < $result; $i++) {
            $cifs[] = GenerateDocument::generateRandomCif();
        }

        // Return the generated CIFs as a JSON array
        return response()->json($cifs, 200);
    }


    /**
     * Generate random CIF numbers by type.
     *
     * Permission: Only authenticated users can access this endpoint.
     * This endpoint generates one or more random CIF (Código de Identificación Fiscal) numbers
     * filtered by entity type. The "type" parameter determines the category of the CIF, and
     * "result" controls how many are generated (default: 1, min: 1, max: 20).
     *
     * **Sociedades Mercantiles y de Capital**
     * - `A`: Sociedades Anónimas (S.A.).
     * - `B`: Sociedades de Responsabilidad Limitada (S.L. o S.R.L.).
     * - `C`: Sociedades Colectivas.
     * - `D`: Sociedades Comanditarias.
     *
     * **Entidades Sociales, Civiles y Sin Personalidad Jurídica**
     * - `E`: Comunidades de bienes, herencias yacentes y otras entidades sin personalidad jurídica.
     * - `F`: Sociedades Cooperativas.
     * - `G`: Asociaciones y Fundaciones.
     * - `H`: Comunidades de propietarios en régimen de propiedad horizontal.
     * - `J`: Sociedades Civiles (con o sin personalidad jurídica).
     * - `U`: Uniones Temporales de Empresas (UTE).
     * - `V`: Otros tipos de entidades no definidos en el resto de claves.
     *
     * **Administración y Entidades Públicas**
     * - `P`: Corporaciones Locales (Ayuntamientos, Diputaciones).
     * - `Q`: Organismos públicos.
     * - `S`: Órganos de la Administración del Estado y de las Comunidades Autónomas.
     *
     * **Entidades Religiosas y Extranjeras**
     * - `R`: Congregaciones e instituciones religiosas.
     * - `N`: Entidades extranjeras (empresas internacionales que operan en España sin domicilio social aquí).
     * - `W`: Establecimientos permanentes de entidades no residentes en España.
     *
     * **Personas Físicas Especiales (sin DNI ni NIE)**
     * - `K`: Personas físicas españolas menores de 14 años, residentes en España sin DNI.
     * - `L`: Personas físicas españolas no residentes en España y sin DNI.
     * - `M`: Personas físicas extranjeras que carecen de NIE.
     *
     * @authenticated
     * @queryParam result integer The number of CIFs to generate. Default: 1. Min: 1. Max: 20. Example: 3
     * @queryParam type string The entity type of the CIF to generate. Valid values: A, B, C, D, E, F, G, H, J, K, L, M, N, P, Q, R, S, U, V, W. Default: A. Example: B
     *
     * @response 200 {
     *   ["B12345678", "B87654321", "B11223344"]
     * }
     * @response 400 scenario="Invalid result" {
     *   "message": "The result parameter must be between 1 and 20."
     * }
     * @response 400 scenario="Invalid type" {
     *   "message": "Invalid type parameter. Valid types are: A, B, C, D, E, F, G, H, J, K, L, M, N, P, Q, R, S, U, V, W."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     */
    public function generateCifByType(Request $request): JsonResponse
    {
        $result = (int) $request->query('result', 1);
        $strType = strtoupper(trim($request->query('type', 'A')));

        $validTypes = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'U', 'V', 'W'];

        // Validate the result parameter (minimum 1, maximum 20)
        if ($result < 1 || $result > 20) {
            return response()->json(['message' => 'The result parameter must be between 1 and 20.'], 400);
        }

        // Validate the type parameter
        if (!in_array($strType, $validTypes)) {
            return response()->json([
                'message' => 'Invalid type parameter. Valid types are: ' . implode(', ', $validTypes) . '.'
            ], 400);
        }

        // Generate a random CIF by type
        $cifs = [];
        for ($i = 0; $i < $result; $i++) {
            $cifs[] = GenerateDocument::generateRandomCifByType($strType);
        }

        // Return the generated CIF as a JSON response
        return response()->json($cifs, 200);
    }

    /**
     * Generate random NIE numbers.
     *
     * Permission: Only authenticated users can access this endpoint.
     * This endpoint generates one or more random NIE (Número de Identidad de Extranjero) numbers,
     * which are unique identifiers used in Spain for foreign residents. Each NIE consists of an
     * initial letter (X, Y or Z), seven digits and a control letter (e.g. X1234567A).
     * The "result" parameter controls how many NIEs are generated (default: 1, min: 1, max: 20).
     *
     * @authenticated
     * @queryParam result integer The number of NIEs to generate. Default: 1. Min: 1. Max: 20. Example: 2
     *
     * @response 200 {
     *   ["X1234567A", "Y7654321B"]
     * }
     * @response 400 {
     *   "message": "The result parameter must be between 1 and 20."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     */
    public function generateNie(Request $request): JsonResponse
    {
        $result = (int) $request->query('result', 1);

        if ($result < 1 || $result > 20) {
            return response()->json(['message' => 'The result parameter must be between 1 and 20.'], 400);
        }

        $nies = [];
        for ($i = 0; $i < $result; $i++) {
            $nies[] = GenerateDocument::generateRandomNie();
        }

        return response()->json($nies, 200);
    }

    /**
     * Generate random NIF numbers.
     *
     * Permission: Only authenticated users can access this endpoint.
     * This endpoint generates one or more random NIF (Número de Identificación Fiscal) numbers,
     * which are unique identifiers used in Spain for tax purposes. Each NIF consists of 8 digits
     * followed by a control letter (e.g. 12345678A).
     * The "result" parameter controls how many NIFs are generated (default: 1, min: 1, max: 20).
     *
     * @authenticated
     * @queryParam result integer The number of NIFs to generate. Default: 1. Min: 1. Max: 20. Example: 2
     *
     * @response 200 {
     *   ["12345678A", "87654321B"]
     * }
     * @response 400 {
     *   "message": "The result parameter must be between 1 and 20."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     */
    public function generateNif(Request $request): JsonResponse
    {
        $result = (int) $request->query('result', 1);

        if ($result < 1 || $result > 20) {
            return response()->json(['message' => 'The result parameter must be between 1 and 20.'], 400);
        }

        $nifs = [];
        for ($i = 0; $i < $result; $i++) {
            $nifs[] = GenerateDocument::generateRandomNif();
        }

        return response()->json($nifs, 200);
    }

    /**
     * Validate a document number.
     *
     * Permission: Only authenticated users can access this endpoint.
     * This endpoint validates a document string and returns whether it is valid or not.
     * If the optional "type" parameter is omitted, the system auto-detects the document type
     * by trying each known format in order: NIE → CIF → DNI → SSN → PASAPORTE.
     * If "type" is provided, only that specific format is evaluated.
     *
     * Supported types:
     * - `DNI`: Documento Nacional de Identidad (8 digits + control letter).
     * - `NIF`: Número de Identificación Fiscal — same algorithm as DNI for individuals.
     * - `NIE`: Número de Identidad de Extranjero (X/Y/Z + 7 digits + control letter).
     * - `CIF`: Código de Identificación Fiscal (entity letter + 7 digits + control character).
     * - `SSN`: Social Security Number — 8 digits or AAA-BB-CCCC format.
     * - `PASAPORTE`: Spanish passport — 2 or 3 letters followed by 6 digits.
     *
     * @authenticated
     * @queryParam document string required The document string to validate. Example: 12345678Z
     * @queryParam type string The document type to validate against. If omitted, auto-detection is used. Valid values: DNI, NIF, NIE, CIF, SSN, PASAPORTE. Example: DNI
     *
     * @response 200 scenario="Valid document (auto-detect)" {
     *   "document": "12345678Z",
     *   "type": "DNI",
     *   "message": "VALIDO"
     * }
     * @response 200 scenario="Invalid document (auto-detect)" {
     *   "document": "00000000X",
     *   "type": null,
     *   "message": "INVALIDO"
     * }
     * @response 200 scenario="Valid document (explicit type)" {
     *   "document": "X1234567L",
     *   "type": "NIE",
     *   "message": "VALIDO"
     * }
     * @response 200 scenario="Invalid document (explicit type)" {
     *   "document": "X1234567L",
     *   "type": "DNI",
     *   "message": "INVALIDO"
     * }
     * @response 400 scenario="Missing document" {
     *   "message": "The document parameter is required."
     * }
     * @response 400 scenario="Invalid type" {
     *   "message": "Invalid type. Valid types: DNI, NIF, NIE, CIF, SSN, PASAPORTE."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     */
    public function validateDocument(Request $request): JsonResponse
    {
        $document = strtoupper(trim($request->query('document', '')));
        $type     = $request->query('type');

        if (empty($document)) {
            return response()->json(['message' => 'The document parameter is required.'], 400);
        }

        $validTypes = ['DNI', 'NIF', 'NIE', 'CIF', 'SSN', 'PASAPORTE'];

        if ($type !== null) {
            $type = strtoupper(trim($type));
            if (!in_array($type, $validTypes)) {
                return response()->json([
                    'message' => 'Invalid type. Valid types: ' . implode(', ', $validTypes) . '.',
                ], 400);
            }
        }

        if ($type === null) {
            $detectedType = GenerateDocument::identifyDocumentType($document);
            return response()->json([
                'document' => $document,
                'type'     => $detectedType,
                'message'  => $detectedType !== null ? 'VALIDO' : 'INVALIDO',
            ], 200);
        }

        $isValid = match ($type) {
            'DNI'       => GenerateDocument::validateDni($document),
            'NIF'       => GenerateDocument::validateNif($document),
            'NIE'       => GenerateDocument::validateNie($document),
            'CIF'       => GenerateDocument::validateCif($document),
            'SSN'       => GenerateDocument::validateSsn($document),
            'PASAPORTE' => GenerateDocument::validatePasaporte($document),
            default     => false,
        };

        return response()->json([
            'document' => $document,
            'type'     => $type,
            'message'  => $isValid ? 'VALIDO' : 'INVALIDO',
        ], 200);
    }

    /**
     * Generate random SSN numbers.
     *
     * Permission: Only authenticated users can access this endpoint.
     * This endpoint generates one or more random SSN (Social Security Number) numbers,
     * which are unique identifiers used in the United States for social security purposes.
     * Each SSN follows the format AAA-BB-CCCC (e.g. 123-45-6789).
     * The "result" parameter controls how many SSNs are generated (default: 1, min: 1, max: 20).
     *
     * @authenticated
     * @queryParam result integer The number of SSNs to generate. Default: 1. Min: 1. Max: 20. Example: 2
     *
     * @response 200 {
     *   ["123-45-6789", "987-65-4321"]
     * }
     * @response 400 {
     *   "message": "The result parameter must be between 1 and 20."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     */
    public function generateSsn(Request $request): JsonResponse
    {
        $result = (int) $request->query('result', 1);

        if ($result < 1 || $result > 20) {
            return response()->json(['message' => 'The result parameter must be between 1 and 20.'], 400);
        }

        $ssns = [];
        for ($i = 0; $i < $result; $i++) {
            $ssns[] = GenerateDocument::generateRandomSsn();
        }

        return response()->json($ssns, 200);
    }
}
