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
            // 1. Ambil file fisik gambar dari URL Cloudinary
            $imageResponse = Http::get($this->imageUrl);
            
            if (!$imageResponse->successful()) {
                throw new \Exception("Gagal mengunduh gambar dari Cloudinary. Status: " . $imageResponse->status());
            }

            $imageContent = $imageResponse->body();
            // Ambil nama file asli dari URL (misal: image.jpg)
            $filename = basename(parse_url($this->imageUrl, PHP_URL_PATH)) ?: 'upload.jpg';

            // 2. Kirim ke FastAPI menggunakan multipart/form-data (attach file)
            $response = Http::timeout(120)
                ->attach('file', $imageContent, $filename) // Kirim sebagai file fisik
                ->post('http://dicatatin-ml:8000/process', [
                    'method' => $this->workspace->method,
                ]);

            if ($response->successful()) {
                $aiData = $response->json();

                // Asumsi respons dari ML: {"status": "success", "data": {"nodes": [], "edges": [], "flashcards": [], "clean_text": ""}}
                // Kita ambil dari array 'data'
                $resultData = $aiData['data'] ?? [];

                $this->workspace->update([
                    'ai_status' => 'completed',
                    'nodes' => $resultData['nodes'] ?? null,
                    'edges' => $resultData['edges'] ?? null,
                    'flashcards' => $resultData['flashcards'] ?? null,
                    'clean_text' => $resultData['clean_text'] ?? null,
                ]);

                Log::info("Sukses memproses AI untuk Workspace ID: {$this->workspace->id}");
            } else {
                $this->workspace->update(['ai_status' => 'failed']);
                Log::error("FastAPI mengembalikan error: ", ['status' => $response->status(), 'body' => $response->body()]);
            }
            
        } catch (\Exception $e) {
            $this->workspace->update(['ai_status' => 'failed']);
            Log::error("Gagal menghubungi FastAPI: " . $e->getMessage());
        }
    }
}