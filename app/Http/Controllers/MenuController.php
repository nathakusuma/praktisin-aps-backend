<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuCreateRequest;
use App\Http\Requests\MenuUpdateRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function create(MenuCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('menu-images', 'public');
            $data['gambar_path'] = $path;
        }

        // Remove image from data since we've processed it
        unset($data['gambar']);

        $menu = new Menu($data);
        $menu->save();

        return response()->json([
            'menu' => new MenuResource($menu)
        ], 201);
    }

    public function getAll(): JsonResponse
    {
        $menus = Menu::all();

        return response()->json([
            'menus' => MenuResource::collection($menus)
        ]);
    }

    public function getById(int $id): JsonResponse
    {
        $menu = Menu::where('id', $id)->first();
        if (!$menu) {
            return response()->json([
                'message' => 'Menu tidak ditemukaan',
                'ref_code' => 'RESOURCE_NOT_FOUND'
            ], 404);
        }

        return response()->json([
            'menu' => new MenuResource($menu)
        ]);
    }

    public function update(int $id, MenuUpdateRequest $request): JsonResponse
    {
        $menu = Menu::where('id', $id)->first();
        if (!$menu) {
            return response()->json([
                'message' => 'Menu tidak ditemukaan',
                'ref_code' => 'RESOURCE_NOT_FOUND'
            ], 404);
        }

        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($menu->gambar_path) {
                Storage::disk('public')->delete($menu->gambar_path);
            }

            $path = $request->file('gambar')->store('menu-images', 'public');
            $data['gambar_path'] = $path;
        }

        // Remove image from data since we've processed it
        unset($data['gambar']);

        $menu->fill($data);
        $menu->save();

        return response()->json([
            'menu' => new MenuResource($menu)
        ]);
    }

    public function delete(int $id): JsonResponse
    {
        $menu = Menu::where('id', $id)->first();
        if (!$menu) {
            return response()->json([
                'message' => 'Menu tidak ditemukaan',
                'ref_code' => 'RESOURCE_NOT_FOUND'
            ], 404);
        }

        // Delete the image file if it exists
        if ($menu->gambar_path) {
            Storage::disk('public')->delete($menu->gambar_path);
        }

        $menu->delete();

        return response()->json([], 204);
    }
}
