<?php

use App\Models\Setting;

function sendNotification($fcmMsg, $registrationIDs_chunks, $customBodyFields = [], $title = "test title", $message = "test message", $type = "test type")
{
    $store_id = getStoreId();
    $store_id = isset($store_id) && !empty($store_id) ? $store_id : $customBodyFields['store_id'];
    // dd($store_id);
    $project_id = Setting::where('variable', 'firebase_project_id')
        ->value('value');

    $url = 'https://fcm.googleapis.com/v1/projects/' . $project_id . '/messages:send';

    $access_token = getAccessToken();

    $fcmFields = [];
    // dd($customBodyFields);

    foreach ($registrationIDs_chunks as $registrationIDs) {
        foreach ($registrationIDs as $registrationID) {
            if ($registrationID == "BLACKLISTED") {
                continue;
            }
            if ($registrationID == "") {
                continue;
            }
            $data = [
                "message" => [
                    "token" => $registrationID,
                    "notification" => [
                        "title" => $customBodyFields['title'],
                        "body" => $customBodyFields['body'],
                    ],
                    "data" => $customBodyFields,
                    "android" => [
                        "notification" => [
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                        "data" => [
                            "title" => $title,
                            "body" => $message,
                            "type" => $customBodyFields['type'],
                            "store_id" => strval($store_id),
                        ]
                    ],
                    "apns" => [
                        "headers" => [
                            "apns-priority" => "10"
                        ],
                        "payload" => [
                            "aps" => [
                                "alert" => [
                                    "title" => $customBodyFields['title'],
                                    "body" => $customBodyFields['body'],
                                ],
                                "user_id" => isset($customBodyFields['user_id']) ? $customBodyFields['user_id'] : '',
                                "store_id" => strval($store_id),
                                "data" => $customBodyFields,
                            ]
                        ]
                    ],
                ]
            ];
            // dd($data);
            $encodedData = json_encode($data);
            $headers = [
                'Authorization: Bearer ' . $access_token,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            // Disabling SSL Certificate support temporarily
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
            // Execute post
            $result = curl_exec($ch);

            if ($result == FALSE) {
                die('Curl failed: ' . curl_error($ch));
            }
            // Close connection
            curl_close($ch);
        }
    }

    return $fcmFields;
}


function getAccessToken()
{
    // Fetch the file name from the settings table
    $file_name = DB::table('settings')
        ->where('variable', 'service_account_file')
        ->value('value');

    // Construct the file path in the storage/app/public directory
    $file_path = storage_path('app/public/' . $file_name);

    // Check if the file exists
    if (!file_exists($file_path)) {
        throw new \Exception('Service account file not found.');
    }

    // Initialize the Google Client
    $client = new Client();
    $client->setAuthConfig($file_path);
    $client->setScopes(['https://www.googleapis.com/auth/firebase.messaging']);

    // Fetch the access token
    $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
    return $accessToken;
}
