<?php

namespace App\Contracts;

/**
 * Interface for Product Repository
 */
interface ProductRepositoryContract
{
    /**
     * Get all products
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll();

    /**
     * Get a product by ID
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function getById(int $id);

    /**
     * Create a new product
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data);

    /**
     * Update a product
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a product
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
