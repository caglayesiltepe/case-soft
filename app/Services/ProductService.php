<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Exception;
use Illuminate\Support\Facades\DB;
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
     * @return array
     */
    public function getAllProducts(): array
    {
        $products = $this->productRepository->getProducts();
        return $this->getProductMap($products);
    }

    /**
     * @param $products
     * @return array
     */
    private function getProductMap($products): array
    {
        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'price' => number_format($product->price, 2),
                'stock' => $product->stock,
            ];
        })->toArray();
    }

    /**
     * @param array $request
     * @return Product
     * @throws Exception
     */
    public function productCreate(array $request): Product
    {
        DB::beginTransaction();
        try {
            $product = $this->productRepository->create([
                'name' => $request['name'],
                'category' => $request['category'],
                'stock' => $request['stock'],
                'price' => $request['price'],
            ]);

            DB::commit();

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed', ['error' => $e->getMessage()]);
            throw new Exception('Ürün oluşturulamadı'. $e->getMessage());
        }
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteProduct(int $id): bool
    {
        $productDelete = $this->productRepository->delete($id);

        if (!$productDelete) {
            Log::error('Product delete failed', ['error' => 'Product not found', 'product_id' => $id]);
            return false;
        }

        return true;
    }
}
