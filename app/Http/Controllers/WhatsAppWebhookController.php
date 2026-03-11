<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;
use App\Services\WaChatbotService;

class WhatsAppWebhookController extends Controller
{
    public function __construct(private WhatsAppService $whatsapp) {}
    /**
     * Receive incoming WhatsApp messages forwarded from the Node.js handler.
     *
     * Payload:
     * {
     *   "number":    "628123456789",
     *   "message":   "Halo, saya mau order",
     *   "timestamp": "2024-01-01T10:00:00.000Z"
     * }
     */
    public function receive(Request $request, WaChatbotService $chatbot)
    {
        // Validate webhook secret
        $secret = config('services.whatsapp.webhook_secret');
        if ($secret && $request->header('X-Webhook-Secret') !== $secret) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'number'    => 'required|string',
            'message'   => 'required|string',
            'timestamp' => 'required|string',
        ]);

        Log::info('WhatsApp message received', $validated);

        // Your business logic here:
        // - Save to database
        // - Trigger auto-reply
        // - Route to support agent
        if ($request['number'] = "6285714817990") {
            $message = "✅ pesan masuknya ini : " . $request['message'];
            $response = $chatbot->process('6285714817990', $request['message']);
            $attr = $this->whatsapp->sendMessage('6285714817990', $response);

            return response()->json([
                'success' => true,
                'message' => $message,
                'reply' => $response,
                'responsenwa' => $attr
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $request->all()
        ]);
    }
}
