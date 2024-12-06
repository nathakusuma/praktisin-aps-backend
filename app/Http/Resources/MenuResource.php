<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // add full url for the image
        if (isset($data['gambar_path'])) {
            $data['gambar_url'] = url('images/menus/' . $data['gambar_path']);

            unset($data['gambar_path']);
        }

        return $data;
    }
}
