<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class PetController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category.id' => 'required|exists:categories,id',
            'photoUrls' => 'nullable|array',
            'photoUrls.*' => 'nullable|string|url',
            'tags' => 'nullable|array',
            'tags.*.id' => 'nullable|exists:tags,id',
            'status' => 'required|in:available,pending,sold',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'Invalid input'], 405);
        }

        $pet = new Pet();

        $this->updateOrAddFieldsToPet($request, $pet);

        return response()->json($pet, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        if(!is_numeric($id) || $id < 0){
            return response()->json(['message'=>'Invalid ID supplied'], 400);
        }

        $pet = Pet::with('tags')->find($id);

        if(!$pet){
            return response()->json(['message'=>'Pet not found'], 404);
        }

        $category = Category::find($pet->category_id);
        $pet->category_id = $category->name;

        return response()->json($pet);
    }

    /**
     * Display pets with specific status
     *
     */
    public function showByStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $statusString = $request->input('status');

        if (!$statusString) {
            return response()->json([
                'message' => 'Invalid status value'
            ], 400);
        }

        $statuses = explode(',', $statusString);
        $statuses = array_map('trim', $statuses);
        $pets = Pet::whereIn('status', $statuses)->get();

        return response()->json($pets);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\JsonResponse
    {

        if(!is_numeric($id) || $id < 0){
            return response()->json(['message'=>'Invalid ID supplied'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category.id' => 'required|exists:categories,id',
            'photoUrls' => 'nullable|array',
            'photoUrls.*' => 'nullable|string|url',
            'tags' => 'nullable|array',
            'tags.*.id' => 'nullable|exists:tags,id',
            'status' => 'required|in:available,pending,sold',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'Validation exception'], 405);
        }

        $pet = Pet::find($id);

        if(!$pet){
            return response()->json(['message'=>'Pet not found'], 404);
        }

        $this->updateOrAddFieldsToPet($request, $pet);

        return response()->json($pet, 200);
    }

    public function updatePetInStore(Request $request, string $id): \Illuminate\Http\JsonResponse
    {

        if(!is_numeric($id) || $id < 0){
            return response()->json(['message'=>'Invalid ID supplied'], 400);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'status' => 'required|in:available,pending,sold',
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>'Invalid input'], 405);
        }

        $pet = Pet::find($id);

        if(!$pet){
            return response()->json(['message'=>'Pet not found'], 404);
        }

        $pet->name = $request->name;
        $pet->status = $request->status;

        $pet->save();

        return response()->json($pet, 200);
    }

    public function addImage(Request $request)
    {
        $uploadFolder = 'pets';
        $validatedData = $request->validate([
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $img = $request->file('image');
        if (!$img || !$img->isValid()) {
            return response()->json(['message' => 'File upload failed or file is not readable'], 400);
        }

        try {
            $imgPath = $img->store($uploadFolder, 'public');
            return $imgPath;
        } catch (\Exception $e) {
            return response()->json(['message' => 'File storage error: ' . $e->getMessage()], 500);
        }
    }

    public function uploadImage(Request $request, string $id): \Illuminate\Http\JsonResponse
    {
        if (!is_numeric($id) || $id < 0) {
            return response()->json(['message' => 'Invalid ID supplied'], 400);
        }

        $pet = Pet::find($id);
        if (!$pet) {
            return response()->json(['message' => 'Pet not found'], 404);
        }

        try {
            $imgPath = $this->addImage($request);
            if (is_string($imgPath)) {
                $photoUrls = $pet->photoUrls ?? [];
                if (is_string($photoUrls)) {
                    $photoUrls = json_decode($photoUrls, true);
                }

                $photoUrls = is_array($photoUrls) ? $photoUrls : [];
                $photoUrls[] = $imgPath;

                $pet->photoUrls = json_encode($photoUrls);
                $pet->save();

                return response()->json($pet, 200);
            } else {
                return response()->json(['message' => 'Image upload failed'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Image upload error: ' . $e->getMessage()], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        if(!is_numeric($id) || $id < 0){
            return response()->json(['message'=>'Invalid ID supplied'], 400);
        }

        $deleted = Pet::destroy($id);

        if ($deleted == 0) {
            return response()->json(['message'=>'Pet not found'], 404);
        }

        return response()->json(['message'=>'Pet deleted successfully'], 200);
    }

    /**
     * @param Request $request
     * @param Pet $pet
     * @return void
     */
    public function updateOrAddFieldsToPet(Request $request, Pet $pet): void
    {
        $pet->name = $request->name;
        $pet->category_id = $request->category['id'];
        $pet->status = $request->status;
        $pet->photoUrls = $request->photoUrls ?? [];

        $pet->save();

        if (isset($request->tags) && is_array($request->tags)) {
            $tagIds = array_map(fn($tag) => $tag['id'], $request->tags);
            $pet->tags()->sync($tagIds);
        }
    }


}
