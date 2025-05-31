<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\Actions;
use Telegram\Bot\BotsManager;

class WebhookTelegramController extends Controller
{

    private BotsManager $botsManager;
    private Client $httpClient;

    public function __construct(BotsManager $botsManager, Client $httpClient)
    {
        $this->botsManager = $botsManager;
        $this->httpClient = $httpClient;
    }
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $webhook = $this->botsManager->bot()->commandsHandler(true);
        \Log::debug('Webhook: ' . $webhook);

        $message = $webhook->getMessage();

        $bot = $this->botsManager->bot();

        if ($message->isType('location')) {
            $location = $message->location;
            $chat = $message->chat;

            $bot->sendChatAction([
                'chat_id' => $chat->id,
                'action' => Actions::TYPING,
            ]);

            $weatherInfo = $this->weatherInformation($location->latitude, $location->longitude);

            $bot->sendMessage([
                'chat_id' => $chat->id,
                'text' => $weatherInfo,
            ]);
        }

        return response(null, Response::HTTP_OK);
    }

    private function weatherInformation($latitude, $longitude)
    {
        $apiToken = 'bb7cec90aea1692b0d715afbde9a8dc3';

        $requestURL = "https://api.openweathermap.org/data/2.5/weather?lat={$latitude}&lon={$longitude}&units=metric&appid={$apiToken}";

        $response = $this->httpClient->get($requestURL);
        $data = json_decode((string)$response->getBody(), false, 512, JSON_THROW_ON_ERROR);

        $city = $data->name . "\n\n";
        $temp = $data->main->temp . "\n\n";
        $pressure = $data->main->pressure . "\n\n";
        $humidity = $data->main->humidity . "\n\n";

        $weatherInfo = 'City: ' . $city;
        $weatherInfo .= 'Temperature: ' . $temp;
        $weatherInfo .= 'Pressure: ' . $pressure;
        $weatherInfo .= 'Humidity: ' . $humidity;

        return $weatherInfo;
    }
}
