<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->select(['id', 'name', 'slug', 'description', 'price', 'created_at'])
            ->published()
            ->with('primaryImage')
            ->latest()
            ->paginate(12);

        return view('products.index', compact('products'));
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_published, 404);

        $product->load('media');

        return view('products.show', compact('product'));
    }
}
