<?php

namespace App\Jobs;

use App\Models\Workspace;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TransformWorkspaceAI implements ShouldQueue
{
    use Queueable;

    public $timeout = 120;

    public function __construct(
        public Workspace $workspace,
        public string $newMethod
    ) {}

    public function handle(): void
    {
        Log::info("Memulai transformasi AI untuk Workspace ID: {$this->workspace->id} ke metode {$this->newMethod}");

        try {
            // Hit ke endpoint /transform FastAPI dengan JSON payload
            $response = Http::timeout(120)->post('http://dicatatin-ml:8000/transform', [
                'clean_text' => $this->workspace->clean_text,
                'new_method' => $this->newMethod,
            ]);

            if ($response->successful()) {
                $aiData = $response->json();
                $resultData = $aiData['data'] ?? [];

                $this->workspace->update([
                    'ai_status'  => 'completed',
                    'nodes'      => $resultData['nodes'] ?? null,
                    'edges'      => $resultData['edges'] ?? null,
                    'flashcards' => $resultData['flashcards'] ?? null,
                ]);

                Log::info("Sukses transformasi AI Workspace ID: {$this->workspace->id}");
            } else {
                $this->workspace->update(['ai_status' => 'failed']);
                Log::error("FastAPI Transform error: ", ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->workspace->update(['ai_status' => 'failed']);
            Log::error("Gagal menghubungi FastAPI Transform: " . $e->getMessage());
        }
    }
}