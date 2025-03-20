<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('user_id', Auth::id())->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $product = Product::create([
            'user_id' => Auth::id(),
            ...$validated
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produkt został dodany.');
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:20',
            'unit_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive'
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Produkt został zaktualizowany.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produkt został usunięty.');
    }
} 