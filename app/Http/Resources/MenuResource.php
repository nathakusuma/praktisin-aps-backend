<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

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

        // Add full URL for the image
        if ($this->gambar_path) {
            $data['gambar_url'] = Storage::disk('public')->url($this->gambar_path);

            // Remove the path since we've added the full URL
            unset($data['gambar_path']);
        }

        return $data;
    }
}
