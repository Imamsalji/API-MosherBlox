<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url', 'http://localhost:3000');
        $this->apiKey = config('services.whatsapp.key', '');
    }

    /**
     * Send a WhatsApp message
     *
     * @param string $number Phone number, e.g. 628123456789
     * @param string $message Message text
     * @return bool
     */
    public function sendMessage(string $number, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])
                ->timeout(15)
                ->post("{$this->baseUrl}/send-message", [
                    'number' => $number,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent', ['number' => $number]);
                return true;
            }

            Log::warning('WhatsApp send failed', [
                'number' => $number,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('WhatsApp service error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Get current WhatsApp connection status
     *
     * @return array
     */
    public function getStatus(): array
    {
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $this->apiKey,
            ])
                ->timeout(5)
                ->get("{$this->baseUrl}/status");

            return $response->json('data', []);
        } catch (\Exception $e) {
            Log::error('Failed to fetch WhatsApp status', ['error' => $e->getMessage()]);
            return ['status' => 'unknown'];
        }
    }
}
