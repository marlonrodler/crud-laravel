<?php

namespace App\Services;

use App\Filters\Keyword;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\ProductCollection as ProductResourceCollection;
use App\Models\Product;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductService
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::custom(
                    'keyword',
                    Keyword::searchOn(['description']),
                )
            ])
            ->defaultSort('description')
            ->allowedSorts(['description', 'price', 'quantity']);

        return new ProductResourceCollection(
            $query->paginate(
                (int) $request->per_page
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'description' => 'required',
            'quantity' => 'required',
            'price' => 'required',
        ]);

        $product = Product::create($request->all());

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Product  $product
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Product $product, Request $request)
    {

        $request->validate([
            'quantity' => [
                'required'
            ],
            'description' => [
                'required'
            ],
            'price' => [
                'required'
            ],
        ]);

        $product->update($request->all());

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json();
    }

}
