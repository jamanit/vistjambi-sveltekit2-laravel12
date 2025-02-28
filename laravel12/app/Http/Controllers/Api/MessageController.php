<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Http\Resources\MessageResource;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $page     = $request->input('page');
        $limit    = $request->input('limit', 10);
        $messages = Message::orderBy('id', 'desc')
            ->when($page, function ($query) use ($limit) {
                return $query->paginate($limit);
            }, function ($query) use ($limit) {
                return $query->limit($limit)->get();
            });

        // return response()->json($messages, 200);
        return MessageResource::collection($messages);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $message = Message::create([
            'name'    => $request->name,
            'email'   => $request->email,
            'message' => $request->message,
        ]);

        // return response()->json($message, 201);
        return new MessageResource($message);
    }

    public function show($id)
    {
        try {
            $message = Message::findOrFail($id);

            // return response()->json($message, 200);
            return new MessageResource($message);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Message not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $message = Message::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name'    => 'required|string|max:255',
                'email'   => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            $message->update([
                'name'    => $request->name,
                'email'   => $request->email,
                'message' => $request->message,
            ]);

            // return response()->json($message, 200);
            return new MessageResource($message);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Message not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $message = Message::findOrFail($id);
            $message->delete();

            return response()->json(['message' => 'Message deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Message not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
