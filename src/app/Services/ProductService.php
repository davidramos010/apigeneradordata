<?php

namespace App\Services;

use App\Contracts\ProductRepositoryContract;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ValidationException;

/**
 * Service for Product operations
 */
class ProductService
{
    private ProductRepositoryContract $repository;

    public function __construct(ProductRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get all products
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllProducts()
    {
        return $this->repository->getAll();
    }

    /**
     * Get a product by ID
     *
     * @param int $id
     * @return \App\Models\Product
     * @throws ResourceNotFoundException
     */
    public function getProductById(int $id)
    {
        $product = $this->repository->getById($id);

        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        return $product;
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return \App\Models\Product
     * @throws ValidationException
     */
    public function createProduct(array $data)
    {
        $validated = $this->validateProductData($data);
        return $this->repository->create($validated);
    }

    /**
     * Update a product
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Product
     * @throws ResourceNotFoundException
     * @throws ValidationException
     */
    public function updateProduct(int $id, array $data)
    {
        // Verify product exists
        $product = $this->repository->getById($id);
        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        $validated = $this->validateProductData($data, true);
        $this->repository->update($id, $validated);

        return $this->repository->getById($id);
    }

    /**
     * Delete a product
     *
     * @param int $id
     * @return bool
     * @throws ResourceNotFoundException
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->repository->getById($id);

        if (!$product) {
            throw new ResourceNotFoundException('Product not found');
        }

        return $this->repository->delete($id);
    }

    /**
     * Validate product data
     *
     * @param array $data
     * @param bool $isUpdate
     * @return array
     * @throws ValidationException
     */
    private function validateProductData(array $data, bool $isUpdate = false): array
    {
        $rules = [
            'name' => $isUpdate ? 'sometimes|string|min:10|max:100' : 'required|string|min:10|max:100',
            'price' => $isUpdate ? 'sometimes|numeric' : 'required|numeric',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator->errors()->toArray());
        }

        return $validator->validated();
    }
}
