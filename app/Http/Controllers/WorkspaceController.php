<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WorkspaceController extends Controller
{
    // GET /api/workspaces
    public function index(Request $request)
    {
        // Dummy data untuk FE
        return response()->json([
            'success' => true,
            'message' => 'Data workspace diambil',
            'data' => [
                [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Mock Catatan Biologi',
                    'method' => 'mind_map',
                    'ai_status' => 'completed',
                    'created_at' => now()->toIso8601String()
                ]
            ],
            'errors' => null
        ]);
    }

    // POST /api/workspaces (Upload)
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:5120', // Max 5MB
            'method' => 'required|string',
            'name' => 'nullable|string'
        ]);

        $workspaceId = Str::uuid()->toString();

        // Nanti di sini logic upload Cloudinary dan dispatch Job
        // ProcessImageToAI::dispatch($workspaceId, ...);

        return response()->json([
            'success' => true,
            'message' => 'Gambar diterima, AI sedang memproses...',
            'data' => [
                'id' => $workspaceId,
                'ai_status' => 'processing'
            ],
            'errors' => null
        ], 202);
    }

    // GET /api/workspaces/{id} (Polling)
    public function show($id)
    {
        // Mock Response: Simulasi seolah-olah sudah selesai diproses (completed)
        // FE bisa merender canvas dari mock ini
        return response()->json([
            'success' => true,
            'message' => 'Detail workspace diambil',
            'data' => [
                'id' => $id,
                'name' => 'Mock Catatan Biologi',
                'method' => 'mind_map',
                'ai_status' => 'completed',
                'nodes' => [
                    ['id' => '1', 'type' => 'default', 'position' => ['x' => 100, 'y' => 100], 'data' => ['label' => 'Sel']]
                ],
                'edges' => [],
                'flashcards' => [
                    ['id' => 'fc1', 'question' => 'Apa itu sel?', 'answer' => 'Unit terkecil kehidupan']
                ],
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ],
            'errors' => null
        ]);
    }

    // PUT /api/workspaces/{id} (Auto-save)
    public function update(Request $request, $id)
    {
        // Bypass update real DB untuk saat ini
        return response()->json([
            'success' => true,
            'message' => 'Workspace berhasil disimpan',
            'data' => null,
            'errors' => null
        ]);
    }

    // POST /api/workspaces/{id}/transform
    public function transform(Request $request, $id)
    {
        $request->validate([
            'new_method' => 'required|string'
        ]);

        // Nanti dispatch Job Transform Method di sini
        // TransformWorkspaceAI::dispatch($id, $request->new_method);

        return response()->json([
            'success' => true,
            'message' => 'Transformasi sedang diproses...',
            'data' => [
                'id' => $id,
                'ai_status' => 'processing'
            ],
            'errors' => null
        ], 202);
    }
}
