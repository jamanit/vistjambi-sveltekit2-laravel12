<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destination;
use App\Http\Resources\DestinationResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('page');
        $limit = $request->input('limit', 10);
        $destinations = Destination::orderBy('id', 'desc')
            ->when($page, function ($query) use ($limit) {
                return $query->paginate($limit);
            }, function ($query) use ($limit) {
                return $query->limit($limit)->get();
            });

        // return response()->json($destinations, 200);
        return DestinationResource::collection($destinations);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'     => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('destinations', 'public');
        }

        $destination = Destination::create([
            'user_id'     => $request->user_id,
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'location'    => $request->location,
            'image'       => $imagePath,
            'description' => $request->description,
        ]);

        // return response()->json($destination, 201);
        return new DestinationResource($destination);
    }

    public function show($id)
    {
        try {
            $destination = Destination::findOrFail($id);

            // return response()->json($destination, 200);
            return new DestinationResource($destination);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Destination not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $destination = Destination::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id'     => 'nullable|exists:users,id',
                'category_id' => 'nullable|exists:categories,id',
                'name'        => 'required|string|max:255',
                'location'    => 'required|string|max:255',
                'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'description' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            if ($request->hasFile('image')) {
                if ($destination->image) {
                    Storage::disk('public')->delete($destination->image);
                }
                $imagePath = $request->file('image')->store('destinations', 'public');
                $destination->image = $imagePath;
            }

            $destination->update([
                'user_id'     => $request->user_id,
                'category_id' => $request->category_id,
                'name'        => $request->name,
                'location'    => $request->location,
                'description' => $request->description,
            ]);

            // return response()->json($destination, 200);
            return new DestinationResource($destination);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Destination not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $destination = Destination::findOrFail($id);

            if ($destination->image) {
                Storage::disk('public')->delete($destination->image);
            }

            $destination->delete();

            return response()->json(['message' => 'Destination deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Destination not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong'], 500);
        }
    }
}
