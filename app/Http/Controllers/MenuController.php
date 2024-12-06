<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuCreateRequest;
use App\Http\Requests\MenuUpdateRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function create(MenuCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $menu = new Menu($data);
        $menu->save();

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = $menu->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/menus'), $filename);
            $menu->gambar_path = $filename;
            $menu->save();
        }

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

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = $menu->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/menus'), $filename);

            // delete old image if exists
            if ($menu->gambar_path) {
                $image_path = public_path('images/menus') . '/' . $menu->gambar_path;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $data['gambar_path'] = $filename;
        }

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

        // delete image if exists
        if ($menu->gambar_path) {
            $image_path = public_path('images/menus') . '/' . $menu->gambar_path;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        $menu->delete();

        return response()->json([], 204);
    }
}
