<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Create a Ticket
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTicket(Request $request)
    {
        $data = $request->only('title', 'cost');
        $validator = Validator::make($data, [
            'title' => 'required|string|min:3',
            'cost' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $ticket = Ticket::where('title', $request->title)->first();
        if ($ticket) {
            return response()->json([
                'message' => 'Ticket Title Already Exist'
            ], 409);
        } else {
            $ticket = Ticket::create([
                'title' => $request->title,
                'cost' => $request->cost
            ]);
            $ticket->save();
            Log::info('Ticket Created Successfully');
            return response()->json([
                'message' => 'Ticket Created Successfully'
            ], 201);
        }
    }

    /**
     * Display Ticket By ID
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function displayTicketById(Request $request)
    {
        $id = $request->only('id');
        $validator = Validator::make($id, [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $ticket = Ticket::where('id', $request->id)->first();
        if (!$ticket) {
            Log::info('Ticket Not Found With This ID');
            return response()->json([
                'message' => 'Ticket Not Found With This ID'
            ], 404);
        } else {
            Log::info('Ticket Fetched Successfully');
            return response()->json([
                'message' => 'Ticket Fetched Successfully',
                'ticket' => $ticket
            ], 200);
        }
    }

    /**
     * Update Ticket By ID
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTicketById(Request $request)
    {
        $data = $request->only('id', 'title', 'cost');
        $validator = Validator::make($data, [
            'id' => 'required|integer',
            'title' => 'required|string|min:3',
            'cost' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $ticket = Ticket::where('id', $request->id)->first();
        if (!$ticket) {
            Log::info('Ticket Not Found With This ID');
            return response()->json([
                'message' => 'Ticket Not Found With This ID'
            ], 404);
        } else {
            $ticket = Ticket::where('title', $request->title)->first();
            $ticket1 = Ticket::where('title', $request->title)->where('id', $request->id)->first();
            if ($ticket1) {
                $ticket->title = $request->title;
                $ticket->cost = $request->cost;
            } elseif ($ticket) {
                return response()->json([
                    'message' => 'Ticket Title Already Exist'
                ], 409);
            } else {
                $ticket->title = $request->title;
                $ticket->cost = $request->cost;
            }
            if ($ticket->save()) {
                Log::info('Ticket Updated Successfully');
                return response()->json([
                    'message' => 'Ticket Updated Successfully'
                ], 201);
            } else {
                Log::info('Ticket Not Updated');
                return response()->json([
                    'message' => 'Ticket Not Updated'
                ], 202);
            }
        }
    }

    /**
     * Delete Ticket By ID
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTicketById(Request $request)
    {
        $id = $request->only('id');
        $validator = Validator::make($id, [
            'id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $ticket = Ticket::where('id', $request->id)->first();
        if (!$ticket) {
            Log::info('Ticket Not Found With This ID');
            return response()->json([
                'message' => 'Ticket Not Found With This ID'
            ], 404);
        } else {
            if ($ticket->delete($id)) {
                Log::info('Ticket Deleted Successfully');
                return response()->json([
                    'message' => 'Ticket Deleted Successfully'
                ], 201);
            } else {
                Log::info('Ticket Not Deleted');
                return response()->json([
                    'message' => 'Ticket Not Deleted'
                ], 202);
            }
        }
    }
}
