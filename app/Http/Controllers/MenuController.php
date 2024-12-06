<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuCreateRequest;
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
}
