<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\BotsManager;

class WebhookTelegramController extends Controller
{

    private BotsManager $botsManager;

    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        \Log::debug($request);
        $webhook = $this->botsManager->bot()->commandsHandler(true);
        \Log::debug('Webhook: ' . $webhook);
        return response(null, Response::HTTP_OK);
    }
}
