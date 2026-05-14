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
            'ai_status' => $this->ai_status, 
            'clean_text' => $this->clean_text,
            'nodes' => $this->nodes,
            'edges' => $this->edges,
            'flashcards' => $this->flashcards,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
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
