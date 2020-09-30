<?php

namespace App\Services;

use App\Filters\Keyword;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\OrderCollection as OrderResourceCollection;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Services\Auth\LoginService;
use App\Services\Auth\PasswordService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class OrderService
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $query = QueryBuilder::for(Order::class)
            ->allowedFilters([
                AllowedFilter::exact('user_id'),
                AllowedFilter::custom(
                    'keyword',
                    Keyword::searchOn(['note'])
                )
            ])
            ->leftJoin('order_items', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products'   , 'products.id', '=', 'order_items.product_id')
            ->select('orders.*', 'order_items.quantity', 'order_items.total_value', 'products.description')
            ->defaultSort('id')
            ->allowedSorts(['user_id', 'note']);

        return new OrderResourceCollection(
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

        $reqOrder = $request->validate([
            'user_id' => 'required|exists:' . User::class . ',id',
            'note' => 'required',
            'order_items.*.product_id' => 'required|exists:' . Product::class . ',id',
            'order_items.*.quantity' => 'required',
            'order_items.*.total_value' => 'required',
        ]);

        $order = Order::create($reqOrder);

        $max = sizeof($request->order_items);
        $max -= 1;

        $reqOrderItem['order_id']    = $order->id;

        for ($i = 0; $i <= $max; $i++) {
            $reqOrderItem['product_id']  = $request->order_items[$i]['product_id'];
            $reqOrderItem['quantity']    = $request->order_items[$i]['quantity'];
            $reqOrderItem['total_value'] = $request->order_items[$i]['total_value'];
            OrderItem::create($reqOrderItem);
        }

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     *
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order): OrderResource
    {
        return new OrderResource($order);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Order  $order
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Order $order, Request $request)
    {

        $request->validate([
            'note' => 'sometimes|string',
        ]);

        $order->update($request->all());

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        $order->delete();
        $msg['success'] = 'Excluído com sucesso!';
        return new OrderResource($msg);
        // return response()->json('Excluído com sucesso!');
    }

}
