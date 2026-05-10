<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Jobs\ProcessImageToAI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class WorkspaceController extends Controller
{
    // GET /api/workspaces
    public function index(Request $request)
    {
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
        'file'   => 'required|image|max:5120', // Max 5MB
        'method' => 'required|string',
        'name'   => 'nullable|string'
    ]);

    try {
        DB::beginTransaction();

        // 1. Upload ke Cloudinary via Storage Driver (v3.x)
        $file = $request->file('file');
        $path = $file->store('dicatatin_workspaces', 'cloudinary');
        $uploadedFileUrl = Storage::disk('cloudinary')->url($path);

        if (!$uploadedFileUrl) {
            throw new \Exception('Upload ke Cloudinary gagal, URL tidak ditemukan.');
        }

        // 2. Simpan record di Database (status: processing)
        $workspace = Workspace::create([
            'user_id'   => $request->user()->id,
            'name'      => $request->name ?? 'Untitled Note',
            'method'    => $request->method,
            'ai_status' => 'processing',
            'image_url' => $uploadedFileUrl,
        ]);

        DB::commit();

        // 3. Dispatch Job ke Queue (background process)
        // Kirim URL gambar, bukan file fisik (mencegah memory exhausted)
        ProcessImageToAI::dispatch($workspace, $uploadedFileUrl);

        // 4. Return instan ke Frontend (202 Accepted)
        return response()->json([
            'success' => true,
            'message' => 'Gambar diterima, AI sedang memproses...',
            'data'    => [
                'id'        => $workspace->id,
                'ai_status' => 'processing'
            ],
            'errors' => null
        ], 202);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal memproses gambar.',
            'data'    => null,
            'errors'  => ['server' => [$e->getMessage()]]
        ], 500);
    }
}
    // GET /api/workspaces/{id} (Polling)
    public function show(string $id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail workspace diambil',
            'data'    => [
                'id'         => $id,
                'name'       => 'Mock Catatan Biologi',
                'method'     => 'mind_map',
                'ai_status'  => 'completed',
                'nodes'      => [
                    [
                        'id'       => '1',
                        'type'     => 'default',
                        'position' => ['x' => 100, 'y' => 100],
                        'data'     => ['label' => 'Sel']
                    ]
                ],
                'edges'      => [],
                'flashcards' => [
                    [
                        'id'       => 'fc1',
                        'question' => 'Apa itu sel?',
                        'answer'   => 'Unit terkecil kehidupan'
                    ]
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
        return response()->json([
            'success' => true,
            'message' => 'Workspace berhasil disimpan',
            'data'    => null,
            'errors'  => null
        ]);
    }

    // POST /api/workspaces/{id}/transform
    public function transform(Request $request, string $id)
    {
        $request->validate([
            'new_method' => 'required|string'
        ]);

        // TransformWorkspaceAI::dispatch($id, $request->new_method);

        return response()->json([
            'success' => true,
            'message' => 'Transformasi sedang diproses...',
            'data'    => [
                'id'        => $id,
                'ai_status' => 'processing'
            ],
            'errors' => null
        ], 202);
    }
}
