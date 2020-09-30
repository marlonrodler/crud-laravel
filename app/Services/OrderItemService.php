<?php

namespace App\Services;

use App\Filters\Keyword;
use App\Http\Resources\OrderItem as OrderItemResource;
use App\Http\Resources\OrderItemCollection as OrderItemResourceCollection;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderItemService
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = QueryBuilder::for(OrderItem::class)
            ->allowedFilters([
                AllowedFilter::exact('product_id'),
                AllowedFilter::exact('order_id'),
                AllowedFilter::custom(
                    'keyword',
                    Keyword::searchOn(['quantity'])
                )
            ])
            ->defaultSort('order_id')
            ->allowedSorts(['product_id', 'order_id', 'quantity']);

        return new OrderItemResourceCollection(
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
            'order_id' => 'required|exists:' . Order::class . ',id',
            'product_id' => 'required|exists:' . Product::class . ',id',
            'quantity' => 'required',
            'total_value' => 'required',
        ]);

        $order = OrderItem::create($request->all());

        return new OrderItemResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  OrderItem $order
     * @return \Illuminate\Http\Response
     */
    public function show(OrderItem $order): OrderItemResource
    {
        return new OrderItemResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  OrderItem  $order
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(OrderItem $order, Request $request)
    {

        $request->validate([
            'quantity' => [
                'sometimes',
                'required',
                Rule::unique(OrderItem::class)->ignore($order->id),
            ],
        ]);

        $order->update($request->all());

        return new OrderItemResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  OrderItem $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderItem $order)
    {
        $order->delete();
        $msg['success'] = 'Excluído com sucesso!';
        return new OrderItemResource($msg);
        // return response()->json();
    }

}
