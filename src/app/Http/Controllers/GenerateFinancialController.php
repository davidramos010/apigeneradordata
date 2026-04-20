<?php

namespace App\Http\Controllers;

use App\Models\GenerateFinancial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group 4. Financial Data
 *
 * Endpoints for generating and validating Spanish financial and banking data.
 */
class GenerateFinancialController extends Controller
{
    /**
     * Generate a valid Spanish IBAN.
     *
     * Permission: Only authenticated users can access this endpoint.
     * Generates a random but structurally valid Spanish IBAN following ISO 13616 and the CCC standard.
     * The BBAN (20 digits) is composed of: bank code (4) + branch code (4) + CCC control digits (2) + account number (10).
     * The 2 IBAN check digits are computed via MOD-97. The CCC control digits are computed via MOD-11.
     *
     * @authenticated
     *
     * @response 200 scenario="IBAN generado correctamente" {
     *   "iban": "ES7921000813610123456789",
     *   "formatted": "ES79 2100 0813 6101 2345 6789",
     *   "components": {
     *     "country": "ES",
     *     "check_digits": "79",
     *     "bank_code": "2100",
     *     "branch_code": "0813",
     *     "control_digits": "61",
     *     "account_number": "0123456789"
     *   }
     * }
     * @response 401 {"message": "Unauthenticated."}
     * @response 500 {"message": "Internal Server Error"}
     */
    public function generateIban(): JsonResponse
    {
        return response()->json(GenerateFinancial::generateRandomIban(), 200);
    }

    /**
     * Validate a Spanish IBAN.
     *
     * Permission: Only authenticated users can access this endpoint.
     * 
     * 
     * Validates a Spanish IBAN string checking: correct length (24 chars), ES country prefix,
     * MOD-97 checksum (ISO 13616), and CCC internal control digits (MOD-11).
     * Spaces in the input are ignored, allowing both compact and formatted IBANs.
     *
     * @authenticated
     *
     * @queryParam iban string required The IBAN to validate. Spaces are ignored. Example: ES79 2100 0813 6101 2345 6789
     *
     * @response 200 scenario="IBAN válido" {
     *   "iban": "ES7921000813610123456789",
     *   "formatted": "ES79 2100 0813 6101 2345 6789",
     *   "valid": true,
     *   "message": "VALIDO",
     *   "components": {
     *     "country": "ES",
     *     "check_digits": "79",
     *     "bank_code": "2100",
     *     "branch_code": "0813",
     *     "control_digits": "61",
     *     "account_number": "0123456789"
     *   }
     * }
     * @response 200 scenario="IBAN inválido" {
     *   "iban": "ES0000000000000000000000",
     *   "formatted": null,
     *   "valid": false,
     *   "message": "Los dígitos de control IBAN no son válidos (falla MOD-97).",
     *   "components": null
     * }
     * @response 400 {"message": "El parámetro 'iban' es requerido."}
     * @response 401 {"message": "Unauthenticated."}
     * @response 500 {"message": "Internal Server Error"}
     */
    public function validateIban(Request $request): JsonResponse
    {
        $iban = $request->query('iban');

        if (!$iban) {
            return response()->json(['message' => "El parámetro 'iban' es requerido."], 400);
        }

        return response()->json(GenerateFinancial::validateIban($iban), 200);
    }
}
