<?php

namespace App\Jobs;

use App\Models\Workspace;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProcessImageToAI implements ShouldQueue
{
    use Queueable;

    // Timeout untuk Job ini (dalam detik), kita set agak lama karena AI butuh waktu berpikir
    public $timeout = 120;

    public function __construct(
        public Workspace $workspace,
        public string $imageUrl
    ) {}

    public function handle(): void
    {
        Log::info("Memulai proses AI untuk Workspace ID: {$this->workspace->id}");

        try {
            // Ganti URL ini dengan Public Domain ML kamu dari Railway!
            $mlUrl = 'https://ml-dicatatin-production.up.railway.app/process';

            $response = Http::timeout(120)->post($mlUrl, [
                'image_url' => $this->imageUrl, // Kirim string URL saja
                'method'    => $this->workspace->method,
            ]);

            if ($response->successful()) {
                $aiData = $response->json();
                $resultData = $aiData['data'] ?? [];

                $this->workspace->update([
                    'ai_status'  => 'completed',
                    'nodes'      => $resultData['nodes'] ?? null,
                    'edges'      => $resultData['edges'] ?? null,
                    'flashcards' => $resultData['flashcards'] ?? null,
                    'clean_text' => $resultData['clean_text'] ?? null,
                ]);
                Log::info("Sukses memproses AI untuk Workspace ID: {$this->workspace->id}");
            } else {
                $this->workspace->update(['ai_status' => 'failed']);
                Log::error("ML Error: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->workspace->update(['ai_status' => 'failed']);
            Log::error("Gagal koneksi ke ML: " . $e->getMessage());
        }
    }
}
