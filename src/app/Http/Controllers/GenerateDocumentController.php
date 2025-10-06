<?php

namespace App\Http\Controllers;

use App\Models\GenerateDocument;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

// Incluir el archivo del helper de validación
require_once __DIR__ . '/../../../helpers/DocumentValidationHelper.php';

class GenerateDocumentController extends Controller
{
    /**
     * Generate a random document number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDni() : JsonResponse
    {
        // Generate a random DNI
        $dni = GenerateDocument::generateRandomDni();

        // Return the generated DNI as a JSON response
        return response()->json(['dni' => $dni], 200);
    }

    /**
     * Validate a DNI number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateDni(Request $request): JsonResponse
    {
        $dni = $request->input('dni');
        $isValid = validateSpanishDni($dni);

        return response()->json(['dni' => $dni, 'valid' => $isValid], 200);
    }

    /**
     * Generate a random CIF number.
     *
     * @return JsonResponse
     */
    public function generateCif(): JsonResponse
    {
        // Generate a random DNI
        $cif = GenerateDocument::generateRandomCif();

        // Return the generated DNI as a JSON response
        return response()->json(['cif' => $cif], 200);
    }

    /**
     * Validate a CIF number.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateCif(Request $request): JsonResponse
    {
        $cif = $request->input('cif');
        $isValid = validateSpanishCif($cif);

        return response()->json(['cif' => $cif, 'valid' => $isValid], 200);
    }

    /**
     * Generate a random NIE number.
     *
     * @return JsonResponse
     */
    public function generateNie(): JsonResponse
    {
        // Generate a random DNI
        $nie = GenerateDocument::generateRandomNie();

        // Return the generated DNI as a JSON response
        return response()->json(['nie' => $nie], 200);
    }

    /**
     * Validate a NIE number.
     *
     * @param Request $request
     * @return \Illuminate\Http.JsonResponse
     */
    public function validateNie(Request $request): JsonResponse
    {
        $nie = $request->input('nie');
        $isValid = validateSpanishNie($nie);

        return response()->json(['nie' => $nie, 'valid' => $isValid], 200);
    }

    /**
     * generate a random NIF number.
     *
     * @return JsonResponse
     */
    public function generateNif(): JsonResponse
    {
        // Generate a random DNI
        $nif = GenerateDocument::generateRandomNif();

        // Return the generated DNI as a JSON response
        return response()->json(['nif' => $nif], 200);
    }

    /**
     * Validate a NIF number.
     *
     * @param Request $request
     * @return \Illuminate\Http.JsonResponse
     */
    public function validateNif(Request $request): JsonResponse
    {
        $nif = $request->input('nif');
        $isValid = validateSpanishNif($nif);

        return response()->json(['nif' => $nif, 'valid' => $isValid], 200);
    }

    /**
     * Generate a random SSN number.
     *
     * @return JsonResponse
     */
    public function generateSsn(): JsonResponse
    {
        // Generate a random DNI
        $ssn = GenerateDocument::generateRandomSsn();

        // Return the generated DNI as a JSON response
        return response()->json(['ssn' => $ssn], 200);
    }

    /**
     * Validate a SSN number.
     *
     * @param Request $request
     * @return \Illuminate\Http.JsonResponse
     */
    public function validateSsn(Request $request): JsonResponse
    {
        $ssn = $request->input('ssn');
        $isValid = validateSsn($ssn);

        return response()->json(['ssn' => $ssn, 'valid' => $isValid], 200);
    }
}
