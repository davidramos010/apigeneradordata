<?php

namespace App\Http\Controllers;

use App\Models\GenerateCups;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GenerateCupsController handles the generation and validation of Spanish CUPS
 * (Código Universal del Punto de Suministro) for electricity and gas supply points.
 * All endpoints require authentication.
 *
 * @group 5. CUPS
 */
class GenerateCupsController extends Controller
{
    /**
     * Generate one or more valid Spanish CUPS codes.
     *
     * Permission: Only authenticated users can access this endpoint.
     * Generates random CUPS (Código Universal del Punto de Suministro) codes for electricity
     * or gas supply points in Spain. The control letters are calculated using the official
     * algorithm (mod 529 over the 16 central digits).
     *
     * @authenticated
     * @bodyParam tipo string required The type of supply. Must be 'electricidad' or 'gas'. Example: electricidad
     * @bodyParam distribuidora string optional 4-digit distributor code. If omitted, a valid one is chosen automatically. Example: 0021
     * @bodyParam cantidad integer optional Number of CUPS to generate. Default: 1. Min: 1. Max: 100. Example: 3
     * @bodyParam incluirSufijo boolean optional If true, appends a frontier digit and type letter (22-char CUPS). Default: false. Example: false
     *
     * @response 200 scenario="Electricidad sin sufijo" {
     *   "tipo": "electricidad",
     *   "cups": ["ES0021123456789012AB", "ES0031987654321098CD"]
     * }
     * @response 200 scenario="Gas con sufijo" {
     *   "tipo": "gas",
     *   "cups": ["ES0067123456789012AB1F"]
     * }
     * @response 400 scenario="Tipo inválido" {
     *   "message": "El parámetro 'tipo' es requerido y debe ser 'electricidad' o 'gas'."
     * }
     * @response 400 scenario="Cantidad fuera de rango" {
     *   "message": "El parámetro 'cantidad' debe estar entre 1 y 100."
     * }
     * @response 400 scenario="Distribuidora inválida" {
     *   "message": "El parámetro 'distribuidora' debe ser un código de exactamente 4 dígitos."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     */
    public function generate(Request $request): JsonResponse
    {
        $tipo          = strtolower(trim($request->input('tipo', '')));
        $distribuidora = trim($request->input('distribuidora', ''));
        $cantidad      = (int) $request->input('cantidad', 1);
        $incluirSufijo = (bool) $request->input('incluirSufijo', false);

        if (!in_array($tipo, ['electricidad', 'gas'], true)) {
            return response()->json([
                'message' => "El parámetro 'tipo' es requerido y debe ser 'electricidad' o 'gas'.",
            ], 400);
        }

        if ($cantidad < 1 || $cantidad > 100) {
            return response()->json([
                'message' => "El parámetro 'cantidad' debe estar entre 1 y 100.",
            ], 400);
        }

        if ($distribuidora !== '' && !preg_match('/^\d{4}$/', $distribuidora)) {
            return response()->json([
                'message' => "El parámetro 'distribuidora' debe ser un código de exactamente 4 dígitos.",
            ], 400);
        }

        $result = GenerateCups::generateCups($distribuidora, $cantidad, $incluirSufijo, $tipo);

        return response()->json($result, 200);
    }

    /**
     * Validate the format and control letters of a Spanish CUPS code.
     *
     * Permission: Only authenticated users can access this endpoint.
     * Validates a CUPS string checking: length (20 or 22 chars), ES prefix, numeric digits,
     * valid control letters, and correct control letter calculation using the official mod 529 algorithm.
     * For 22-char CUPS also validates the frontier digit and type letter.
     *
     * @authenticated
     * @bodyParam cups string required The CUPS string to validate. Example: ES0021000000000001RK
     *
     * @response 200 scenario="CUPS válido (20 chars)" {
     *   "valido": true,
     *   "cups": "ES0021000000000001RK",
     *   "detalles": {
     *     "pais": "ES",
     *     "distribuidora": "0021",
     *     "suministro": "000000000001",
     *     "controlEsperado": "RK",
     *     "controlRecibido": "RK",
     *     "sufijoFrontera": null,
     *     "tipoFrontera": null
     *   },
     *   "errores": []
     * }
     * @response 200 scenario="CUPS válido (22 chars)" {
     *   "valido": true,
     *   "cups": "ES0021000000000001RK1F",
     *   "detalles": {
     *     "pais": "ES",
     *     "distribuidora": "0021",
     *     "suministro": "000000000001",
     *     "controlEsperado": "RK",
     *     "controlRecibido": "RK",
     *     "sufijoFrontera": "1",
     *     "tipoFrontera": "F"
     *   },
     *   "errores": []
     * }
     * @response 200 scenario="CUPS inválido" {
     *   "valido": false,
     *   "errores": ["Los dígitos de control no coinciden"]
     * }
     * @response 400 scenario="Parámetro cups ausente" {
     *   "message": "El parámetro 'cups' es requerido."
     * }
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     * @response 500 {
     *   "message": "Internal Server Error"
     * }
     */
    public function validate(Request $request): JsonResponse
    {
        $cups = strtoupper(trim($request->input('cups', '')));

        if (empty($cups)) {
            return response()->json([
                'message' => "El parámetro 'cups' es requerido.",
            ], 400);
        }

        $result = GenerateCups::validateCups($cups);

        return response()->json($result, 200);
    }
}
