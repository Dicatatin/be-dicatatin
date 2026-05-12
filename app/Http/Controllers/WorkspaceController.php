<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Jobs\ProcessImageToAI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Jobs\TransformWorkspaceAI;
use App\Http\Resources\WorkspaceResource;

class WorkspaceController extends Controller
{
    // GET /api/workspaces
    public function index(Request $request)
    {
        // Ambil semua workspace milik user yang sedang login, urutkan dari yang terbaru
        $workspaces = Workspace::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        // Gunakan Resource Collection agar format camelCase konsisten dengan frontend
        return WorkspaceResource::collection($workspaces)->additional([
            'success' => true,
            'message' => 'Data workspace berhasil diambil',
            'errors'  => null
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

    // GET /api/workspaces/{id} (Polling & Detail)
    public function show(Request $request, string $id)
    {
        // Cari workspace berdasarkan ID dan pastikan itu milik user yang sedang login
        $workspace = Workspace::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Kembalikan data melalui Resource agar otomatis terformat menjadi camelCase
        // additional() digunakan untuk menyuntikkan properti success, message, errors di luar 'data'
        return (new WorkspaceResource($workspace))->additional([
            'success' => true,
            'message' => 'Detail workspace diambil',
            'errors'  => null
        ]);
    }

   // PUT /api/workspaces/{id} (Auto-save)
    public function update(Request $request, string $id)
    {
        // Validasi data yang dikirim FE (bisa partial/sebagian)
        $request->validate([
            'name'       => 'nullable|string',
            'method'     => 'nullable|string',
            'nodes'      => 'nullable|array',
            'edges'      => 'nullable|array',
            'flashcards' => 'nullable|array',
        ]);

        $workspace = Workspace::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Update hanya field yang dikirim di request
        $workspace->update($request->only(['name', 'method', 'nodes', 'edges', 'flashcards']));

        return response()->json([
            'success' => true,
            'message' => 'Workspace berhasil disimpan',
            'data'    => [
                'updatedAt' => $workspace->updated_at->toISOString()
            ],
            'errors'  => null
        ]);
    }

    // POST /api/workspaces/{id}/transform
    public function transform(Request $request, string $id)
    {
        $request->validate([
            'new_method' => 'required|string'
        ]);

        $workspace = Workspace::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Kembalikan status ke processing
        $workspace->update([
            'ai_status' => 'processing',
            'method'    => $request->new_method
        ]);

        // Dispatch Job baru khusus untuk Transformasi (menggunakan clean_text)
        TransformWorkspaceAI::dispatch($workspace, $request->new_method);

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