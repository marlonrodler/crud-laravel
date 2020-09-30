<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Models\OrderItem;
use App\Services\OrderItemService;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    private $orderService;

    function __construct(OrderService $orderService, OrderItemService $orderItemService)
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
        $this->orderService = $orderService;
        $this->orderItemService = $orderItemService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->orderService->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->orderService->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        return $this->orderService->show($order);
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
        return $this->orderService->update($order, $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Order $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        return $this->orderService->destroy($order);
    }
}
