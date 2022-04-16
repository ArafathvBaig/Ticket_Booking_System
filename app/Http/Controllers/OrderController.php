<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Create a Order
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOrder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'ticket_id' => 'required|integer',
                'ticket_count' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'message' => 'Invalid Authorization Token'
                ], 401);
            } else {
                $ticket = Ticket::where('id', $request->ticket_id)->first();
                if (!$ticket) {
                    return response()->json([
                        'message' => 'Ticket Not Found'
                    ], 404);
                } else {
                    $order = Order::create([
                        'user_id' => $user->id,
                        'ticket_id' => $request->ticket_id,
                        'ticket_count' => $request->ticket_count
                    ]);
                    if ($order->save()) {
                        return response()->json([
                            'message' => 'Order Added Sucessfully',
                        ], 201);
                    } else {
                        return response()->json([
                            'message' => 'Order Not Added',
                        ], 202);
                    }
                }
            }
        } catch (JWTException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Display Order By ID
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function displayOrderById(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'message' => 'Invalid authorization token'
                ], 401);
            } else {
                $order = Order::where('user_id', $user->id)->get();
                if (!$order) {
                    return response()->json([
                        'message' => 'Orders Not Found'
                    ], 404);
                } else {
                    return response()->json([
                        'message' => 'Orders Retrieved Successfully.',
                        'Orders' => $order
                    ], 201);
                }
            }
        } catch (JWTException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Update Ticket By ID
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrderById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'ticket_id' => 'required|integer',
                'ticket_count' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'message' => 'Invalid Authorization Token'
                ], 401);
            } else {
                $order = Order::where('id', $request->id)->where('user_id', $user->id)->first();
                if (!$order) {
                    return response()->json([
                        'message' => 'Order Not Found For This User'
                    ], 404);
                } else {
                    $order->ticket_id = $request->ticket_id;
                    $order->ticket_count = $request->ticket_count;
                    if ($order->save()) {
                        return response()->json([
                            'message' => "Order Updated Successfully"
                        ], 201);
                    } else {
                        return response()->json([
                            'message' => "Order Not Updated"
                        ], 202);
                    }
                }
            }
        } catch (JWTException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Delete Order By ID
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOrderById(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json([
                    'message' => 'Invalid Authorization Token'
                ], 401);
            } else {
                $order = Order::where('id', $request->id)->where('user_id', $user->id)->first();
                if (!$order) {
                    return response()->json([
                        'message' => 'Order Not Found For This User'
                    ], 404);
                } else {
                    if ($order->delete($request->id)) {
                        return response()->json([
                            'message' => "Order Deleted Successfully"
                        ], 201);
                    } else {
                        return response()->json([
                            'message' => "Order Not Deleted"
                        ], 202);
                    }
                }
            }
        } catch (JWTException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
