<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function getFirebaseCredentials()
    {
        $path = 'public/eshop-8c86d-aff6546e4ff8.json';

        if (!Storage::exists($path)) {
            \Log::debug('Credentials file not found');
        } else {
            $credentials = json_decode(Storage::get($path), true);
        }

        $firebase_settings = getSettings('firebase_settings');
        $firebase_settings = json_decode($firebase_settings, true);
        $firebase_settings['vapidKey'] = $credentials['vapid_key'];

        return $firebase_settings;
    }
}
