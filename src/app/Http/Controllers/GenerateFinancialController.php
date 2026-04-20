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

    /**
     * Generate a valid Spanish bank account number (CCC).
     *
     * Permission: Only authenticated users can access this endpoint.
     * Generates a random CCC (Código Cuenta Cliente) with valid MOD-11 control digits.
     * The CCC is the BBAN part of the Spanish IBAN: bank(4) + branch(4) + control(2) + account(10).
     *
     * @authenticated
     *
     * @response 200 scenario="Cuenta generada correctamente" {
     *   "ccc": "21000813610123456789",
     *   "formatted": "2100-0813-61-0123456789",
     *   "components": {
     *     "bank_code": "2100",
     *     "branch_code": "0813",
     *     "control_digits": "61",
     *     "account_number": "0123456789"
     *   }
     * }
     * @response 401 {"message": "Unauthenticated."}
     * @response 500 {"message": "Internal Server Error"}
     */
    public function generateCuenta(): JsonResponse
    {
        return response()->json(GenerateFinancial::generateRandomCuenta(), 200);
    }

    /**
     * Generate a valid credit card number.
     *
     * Permission: Only authenticated users can access this endpoint.
     * Generates a random credit card number using the Luhn algorithm.
     * Supported types: VISA (16 digits, prefix 4xxx), MASTERCARD (16 digits, prefix 51-55xx),
     * AMEX (15 digits, prefix 34xx or 37xx). If no type is provided, one is chosen at random.
     *
     * @authenticated
     *
     * @queryParam type string optional Tipo de tarjeta. Valores válidos: VISA, MASTERCARD, AMEX. Si se omite, se elige aleatoriamente. Example: VISA
     *
     * @response 200 scenario="Tarjeta VISA generada" {
     *   "card_number": "4532123456789010",
     *   "formatted": "4532 1234 5678 9010",
     *   "type": "VISA",
     *   "expiry": "08/28",
     *   "cvv": "123",
     *   "components": {
     *     "prefix": "4532",
     *     "length": 16,
     *     "network": "Visa International"
     *   }
     * }
     * @response 200 scenario="Tarjeta AMEX generada" {
     *   "card_number": "378282246310005",
     *   "formatted": "3782 822463 10005",
     *   "type": "AMEX",
     *   "expiry": "11/27",
     *   "cvv": "1234",
     *   "components": {
     *     "prefix": "3782",
     *     "length": 15,
     *     "network": "American Express"
     *   }
     * }
     * @response 400 {"message": "Tipo no válido. Tipos soportados: VISA, MASTERCARD, AMEX."}
     * @response 401 {"message": "Unauthenticated."}
     * @response 500 {"message": "Internal Server Error"}
     */
    public function generateTarjeta(Request $request): JsonResponse
    {
        $type = $request->query('type');

        try {
            return response()->json(GenerateFinancial::generateRandomTarjeta($type), 200);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
