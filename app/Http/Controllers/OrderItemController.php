<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Services\OrderItemService;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{

    private $orderItemService;

    function __construct(OrderItemService $orderItemService)
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
        $this->orderItemService = $orderItemService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->orderItemService->index($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->orderItemService->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  OrderItem $orderItem
     * @return \Illuminate\Http\Response
     */
    public function show(OrderItem $orderItem)
    {
        return $this->orderItemService->show($orderItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  OrderItem  $orderItem
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(OrderItem $orderItem, Request $request)
    {
        return $this->orderItemService->update($orderItem, $request);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  OrderItem $orderItem
     * @return \Illuminate\Http\Response
     */
    public function destroy(OrderItem $orderItem)
    {
        return $this->orderItemService->destroy($orderItem);
    }
}
