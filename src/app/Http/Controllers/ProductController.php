<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Exceptions\ResourceNotFoundException;
use App\Exceptions\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Retrieve all products.
     *
     * @return JsonResponse
     */
    public function getProducts(): JsonResponse
    {
        try {
            $products = $this->productService->getAllProducts();

            return $products->isEmpty()
                ? response()->json(['message' => 'No products found'], 404)
                : response()->json($products, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve products'], 500);
        }
    }

    /**
     * Retrieve a specific product by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function getProduct(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProductById($id);
            return response()->json($product, 200);
        } catch (ResourceNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve product'], 500);
        }
    }

    /**
     * Create a new product.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function addProduct(Request $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->all());
            return response()->json(['message' => 'Product created successfully', 'data' => $product], 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create product'], 500);
        }
    }

    /**
     * Update a product.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateProduct(Request $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($id, $request->all());
            return response()->json(['message' => 'Product updated successfully', 'data' => $product], 200);
        } catch (ResourceNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (ValidationException $e) {
            return response()->json(['errors' => json_decode($e->getMessage(), true)], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update product'], 500);
        }
    }

    /**
     * Delete a product.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function deleteProduct(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (ResourceNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete product'], 500);
        }
    }
}
