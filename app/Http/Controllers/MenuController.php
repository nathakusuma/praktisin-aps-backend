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

        $menu->delete();

        return response()->json([], 204);
    }
}
