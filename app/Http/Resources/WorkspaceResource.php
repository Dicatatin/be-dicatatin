<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'method' => $this->method,
            // Data nodes, edges, & flashcards otomatis ter-decode karena $casts di Model
            'nodes' => $this->nodes,
            'edges' => $this->edges,
            'flashcards' => $this->flashcards,
            // Format camelCase untuk FE dan ISO String untuk waktu
            'createdAt' => $this->created_at?->toISOString(),
            'updatedAt' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Tambahkan metadata statis seperti "status": "success"
     */
    public function with(Request $request): array
    {
        return [
            'status' => 'success',
        ];
    }
}