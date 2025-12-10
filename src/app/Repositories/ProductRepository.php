<?php

namespace App\Repositories;

use App\Contracts\ProductRepositoryContract;
use App\Models\Product;

/**
 * Product Repository for database operations
 */
class ProductRepository implements ProductRepositoryContract
{
    private Product $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    /**
     * Get all products
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get a product by ID
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function getById(int $id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Update a product
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $product = $this->model->find($id);
        
        if (!$product) {
            return false;
        }

        return $product->update($data);
    }

    /**
     * Delete a product
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $product = $this->model->find($id);
        
        if (!$product) {
            return false;
        }

        return $product->delete();
    }
}
