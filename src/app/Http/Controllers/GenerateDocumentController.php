<?php

namespace App\Http\Controllers;

use App\Models\GenerateDocument;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
}
