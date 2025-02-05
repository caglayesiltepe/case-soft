<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Log;

class ProductService
{
    /**
     * @var ProductRepository
     */
    private ProductRepository $productRepository;

    /**
     * @param ProductRepository $productRepository
     */
    public function __construct(
        ProductRepository $productRepository
    )
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param int $page ,
     * @param int $perPage
     * @return array
     */
    public function getAllProducts(int $page, int $perPage): array
    {
        $products = $this->productRepository->getAll($page, $perPage);

        return $this->getProductMap($products);
    }

    /**
     * @param $products
     * @return array
     */
    private function getProductMap($products): array
    {
        return [
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category,
                    'price' => number_format($product->price, 2),
                    'stock' => $product->stock,
                ];
            })->toArray(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'last_page' => $products->lastPage(),
            ]
        ];
    }

    /**
     * @param array $request
     * @return Product
     */
    public function productCreate(array $request): Product
    {
        return $this->productRepository->create([
            'name' => $request['name'],
            'category' => $request['category'],
            'stock' => $request['stock'],
            'price' => $request['price'],
        ]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }
}
