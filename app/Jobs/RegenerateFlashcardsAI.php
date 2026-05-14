<?php

namespace App\Jobs;

use App\Models\Workspace;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RegenerateFlashcardsAI implements ShouldQueue
{
    use Queueable;

    public $timeout = 120;

    public function __construct(
        public Workspace $workspace
    ) {}

    public function handle(): void
    {
        Log::info("Memulai regenerasi Flashcard untuk Workspace ID: {$this->workspace->id}");

        try {
            // Gunakan URL Public Railway ML-mu
            $mlUrl = 'https://ml-dicatatin-production.up.railway.app/flashcard';

            $response = Http::timeout(120)->post($mlUrl, [
                'clean_text' => $this->workspace->clean_text,
            ]);

            if ($response->successful()) {
                $aiData = $response->json();

                // Ambil data flashcards dari response
                $flashcards = $aiData['data']['flashcards'] ?? [];

                // Update HANYA kolom flashcards dan kembalikan status ke completed
                $this->workspace->update([
                    'ai_status'  => 'completed',
                    'flashcards' => $flashcards,
                ]);

                Log::info("Sukses regenerasi Flashcard Workspace ID: {$this->workspace->id}");
            } else {
                $this->workspace->update(['ai_status' => 'failed']);
                Log::error("FastAPI Flashcard error: ", ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            $this->workspace->update(['ai_status' => 'failed']);
            Log::error("Gagal menghubungi FastAPI Flashcard: " . $e->getMessage());
        }
    }
}
