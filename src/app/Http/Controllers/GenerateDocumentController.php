<?php

namespace App\Http\Controllers;

use App\Services\DocumentGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenerateDocumentController extends Controller
{
    private DocumentGeneratorService $documentService;

    public function __construct(DocumentGeneratorService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Generate a random DNI number.
     *
     * @return JsonResponse
     */
    public function generateDni(): JsonResponse
    {
        try {
            $dni = $this->documentService->generateDni();
            return response()->json(['document' => 'dni', 'value' => $dni], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate DNI'], 500);
        }
    }

    /**
     * Validate a DNI number.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateDni(Request $request): JsonResponse
    {
        try {
            $dni = $request->input('dni');

            if (!$dni) {
                return response()->json(['error' => 'DNI is required'], 422);
            }

            $isValid = $this->documentService->validateDni($dni);
            return response()->json(['document' => 'dni', 'value' => $dni, 'valid' => $isValid], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to validate DNI'], 500);
        }
    }

    /**
     * Generate a random CIF number.
     *
     * @return JsonResponse
     */
    public function generateCif(): JsonResponse
    {
        try {
            $cif = $this->documentService->generateCif();
            return response()->json(['document' => 'cif', 'value' => $cif], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate CIF'], 500);
        }
    }

    /**
     * Validate a CIF number.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateCif(Request $request): JsonResponse
    {
        try {
            $cif = $request->input('cif');

            if (!$cif) {
                return response()->json(['error' => 'CIF is required'], 422);
            }

            $isValid = $this->documentService->validateCif($cif);
            return response()->json(['document' => 'cif', 'value' => $cif, 'valid' => $isValid], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to validate CIF'], 500);
        }
    }

    /**
     * Generate a random NIE number.
     *
     * @return JsonResponse
     */
    public function generateNie(): JsonResponse
    {
        try {
            $nie = $this->documentService->generateNie();
            return response()->json(['document' => 'nie', 'value' => $nie], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate NIE'], 500);
        }
    }

    /**
     * Validate a NIE number.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateNie(Request $request): JsonResponse
    {
        try {
            $nie = $request->input('nie');

            if (!$nie) {
                return response()->json(['error' => 'NIE is required'], 422);
            }

            $isValid = $this->documentService->validateNie($nie);
            return response()->json(['document' => 'nie', 'value' => $nie, 'valid' => $isValid], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to validate NIE'], 500);
        }
    }

    /**
     * Generate a random NIF number.
     *
     * @return JsonResponse
     */
    public function generateNif(): JsonResponse
    {
        try {
            $nif = $this->documentService->generateNif();
            return response()->json(['document' => 'nif', 'value' => $nif], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate NIF'], 500);
        }
    }

    /**
     * Validate a NIF number.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateNif(Request $request): JsonResponse
    {
        try {
            $nif = $request->input('nif');

            if (!$nif) {
                return response()->json(['error' => 'NIF is required'], 422);
            }

            $isValid = $this->documentService->validateNif($nif);
            return response()->json(['document' => 'nif', 'value' => $nif, 'valid' => $isValid], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to validate NIF'], 500);
        }
    }

    /**
     * Generate a random SSN number.
     *
     * @return JsonResponse
     */
    public function generateSsn(): JsonResponse
    {
        try {
            $ssn = $this->documentService->generateSsn();
            return response()->json(['document' => 'ssn', 'value' => $ssn], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate SSN'], 500);
        }
    }

    /**
     * Validate a SSN number.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validateSsn(Request $request): JsonResponse
    {
        try {
            $ssn = $request->input('ssn');

            if (!$ssn) {
                return response()->json(['error' => 'SSN is required'], 422);
            }

            $isValid = $this->documentService->validateSsn($ssn);
            return response()->json(['document' => 'ssn', 'value' => $ssn, 'valid' => $isValid], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to validate SSN'], 500);
        }
    }
}
