<?php

namespace App\Livewire\RegisterAndLogin;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Traits\ReferralCodeTrait;
use Illuminate\Support\Facades\Cookie;
use Chatify\Facades\ChatifyMessenger as Chatify; // Додаємо фасад Chatify
use Pusher\Pusher; // Додаємо Pusher
use App\Models\UserFcm; // Для FCM-повідомлень


class Register extends Component
{
    use ReferralCodeTrait;

    public $username = "";
    public $otp = "";
    public $mobile = "";
    public $password = "";
    public $password_confirmation = "";
    public $referral_code = "";
    public $friends_code = "";

    public function render()
    {
        $system_settings = getSettings('system_settings', true, true);
        $system_settings = json_decode($system_settings);
        $authentication_method = $system_settings->authentication_method ?? "";
        return view('livewire.' . config('constants.theme') . '.register-and-login.register', [
            'authentication_method' => $authentication_method,
            'title' => 'Sign Up |'
        ]);
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'username' => 'required',
                    'mobile' => 'required|numeric',
                    'email' => ['required', 'email', Rule::unique('users', 'email')],
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'telegram_id' => ['required', 'unique:users,telegram_id'],
                    'telegram_username' => 'required',
                    'password' => 'required|confirmed|min:8',
                    'referral_code' => 'nullable|string|unique:users,referral_code|max:255|regex:/^[A-Za-z0-9]+$/',
                    'friends_code' => 'nullable|string|max:255',
                ]
            );

            if ($validator->fails()) {
                $errors = $validator->errors();
                $response['error'] = true;
                $response['message'] = $errors;
                return $response;
            }




            // отримуємо referral_code з сесії або cookies
            $friends_code = $request->session()->get('referral_code') ?? Cookie::get('referral_code');

            // Перевірка friends_code
            if ($friends_code) {
                $friend = User::where('referral_code', $friends_code)->first();
                if (!$friend) {
                    $friends_code = null;
                }
            }

            // генерація referral_code
            $referral_code = $this->generateUniqueReferralCode();

            $data['username'] = $request['username'];
            $data['country_code'] = $request['country_code'];
            $data['mobile'] = $request['mobile'];
            $data['email'] = $request['email'];
            $data['first_name'] = $request['first_name'];
            $data['last_name'] = $request['last_name'];
            $data['telegram_id'] = $request['telegram_id'];
            $data['telegram_username'] = $request['telegram_username'];
            $data['password'] = bcrypt($request['password']);
            $data['role_id'] = "2";
            $data['referral_code'] = $referral_code;
            $data['friends_code'] = $friends_code ?? null;

            $user = User::create($data);

            auth()->login($user);

            // Відправка повідомлення від імені адміністратора
            $this->sendWelcomeMessage($user);

            $response = [
                'error' => false,
                'message' => "Welcome " . $request['username'],
            ];


            try {
                sendMailTemplate(to: $data['email'], template_key: "welcome", data: [
                    "username" => $data['username']
                ]);
            } catch (\Throwable $th) {
                // Логування помилки відправки email
            }

            return $response;
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    public function check_mobile_number(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mobile' => ['required', Rule::unique('users', 'mobile')],
            ]
        );
        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['error'] = true;
            $response['message'] = $errors;
            return $response;
        }
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->user();
        $finduser = User::where('email', $user->email)->first();
        if ($finduser) {
            Auth::login($finduser);
            return redirect('/')->with('message', 'Logged In Successfully');;
        } else {
            $newUser = User::create([
                'username' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'image' => $user->avatar,
                'role_id' => "2",
                'type' => "google",
            ]);
            Auth::login($newUser);
            $this->sendWelcomeMessage($newUser); // Додаємо відправку повідомлення
            redirect("/")->with('message', 'Registered Successfully');
            try {
                sendMailTemplate(to: $newUser['email'], template_key: "welcome", data: [
                    "username" => $newUser['username']
                ]);
            } catch (\Throwable $th) {
            }
            return;
        }
    }
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $user = Socialite::driver('facebook')->user();
        $finduser = User::where('email', $user->email)->first();
        if ($finduser) {
            Auth::login($finduser);
            return redirect('/')->with('message', 'Logged In Successfully');;
        } else {
            $newUser = User::create([
                'username' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'image' => $user->avatar,
                'role_id' => "2",
                'type' => "facebook",
            ]);
            Auth::login($newUser);
            $this->sendWelcomeMessage($newUser); // Додаємо відправку повідомлення
            redirect("/")->with('message', 'Registered Successfully');
            try {
                sendMailTemplate(to: $newUser['email'], template_key: "welcome", data: [
                    "username" => $newUser['username']
                ]);
            } catch (\Throwable $th) {
            }
            return;
        }
    }

    public function check_telegram_authorization(Request $request)
    {
        $system_settings = getSettings('system_settings', true, true);
        $system_settings = json_decode($system_settings);
        $auth_data = $request->user;
        $check_hash = $auth_data['hash'];
        // return response()->json(['error' => true, 'message' => json_encode($auth_data)]);

        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $system_settings->tg_bot_token ?? "", true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            return response()->json(['error' => true, 'message' => 'Data is NOT from Telegram']);
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            return response()->json(['error' => true, 'message' => 'Data is outdated']);
        }

        if ($request->check_type == "register") {
            $telegram_id = $auth_data['id'];
            $user = User::where('telegram_id', '=', $telegram_id)->first();
            if ($user) {
                return response()->json(['error' => true, 'message' => 'User already exists! Sign In First']);
            }
        }

        if ($request->check_type != "register") {
            // Перевірка та генерація referral_code тільки для існуючих користувачів
            $user = User::where('telegram_id', '=', $auth_data['id'])->first();
            if ($user && empty($user->referral_code)) {
                try {
                    $user->referral_code = $this->generateUniqueReferralCode();
                    $user->save();
                } catch (\Exception $e) {
                    Log::error('Failed to generate referral code for Telegram user: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['error' => false, 'message' => 'Telegram validation success', 'user' => $auth_data]);
    }



    /**
     * Відправка привітального повідомлення від імені адміністратора
     *
     * @param User|null $user Новий зареєстрований користувач
     * @return void
     */
    private function sendWelcomeMessage($user)
    {
        try {
            if (!$user || !isset($user->id)) {
                Log::error('Invalid user object in sendWelcomeMessage', ['user' => $user]);
                return;
            }

            $adminId = config('app.admin_id'); // ID адміністратора
            $admin = User::find($adminId);
            if (!$admin) {
                Log::error('Admin user not found', ['admin_id' => $adminId]);
                return;
            }

            $messageText = 'Congratulations on registering on our site! If you have any questions, you can always contact our support team.';

            // Створюємо повідомлення
            $message = Chatify::newMessage([
                'from_id' => $adminId,
                'to_id' => $user->id,
                'body' => htmlentities(trim($messageText), ENT_QUOTES, 'UTF-8'),
                'attachment' => null,
            ]);

            // Парсимо повідомлення для відображення
            $messageData = Chatify::parseMessage($message);

            // Налаштування Pusher
            $settings = getSettings('pusher_settings', true);
            $settings = json_decode($settings, true);
            $pusher_channel_name = $settings['pusher_channel_name'] ?? '';

            if (empty($settings['pusher_app_key']) || empty($settings['pusher_app_secret']) || empty($settings['pusher_app_id'])) {
                Log::warning('Pusher settings are incomplete, skipping Pusher notification', $settings);
            } else {
                $pusher = new Pusher(
                    $settings['pusher_app_key'],
                    $settings['pusher_app_secret'],
                    $settings['pusher_app_id'],
                    ['cluster' => $settings['pusher_app_cluster'] ?? '']
                );

                // Відправляємо повідомлення через Pusher
                $pusher->trigger($pusher_channel_name . '.' . $user->id, 'messaging', [
                    'from_id' => $adminId,
                    'to_id' => $user->id,
                    'formatted_message' => Chatify::messageCard($messageData, true),
                    'message' => $messageData,
                ]);
                Log::info('Pusher notification sent', ['user_id' => $user->id, 'admin_id' => $adminId]);
            }

            // Відправка push-повідомлення через FCM
            $fcm_ids = UserFcm::join('users', 'user_fcm.user_id', '=', 'users.id')
                ->where('user_fcm.user_id', $user->id)
                ->where('users.is_notification_on', 1)
                ->pluck('user_fcm.fcm_id')
                ->toArray();

            if (!empty($fcm_ids)) {
                $fcmMsg = [
                    'title' => 'New Message From ' . ($admin->username ?? 'Administrator'),
                    'body' => $messageText,
                    'image' => '',
                    'type' => 'message',
                    'user_id' => (string) $adminId,
                ];
                $registrationIDs_chunks = array_chunk($fcm_ids, 1000);
                sendNotification('', $registrationIDs_chunks, $fcmMsg);
                Log::info('FCM notification sent', ['user_id' => $user->id, 'admin_id' => $adminId]);
            }

            Log::info('Welcome message sent to user', ['user_id' => $user->id, 'admin_id' => $adminId]);
        } catch (\Exception $e) {
            Log::error('Failed to send welcome message: ' . $e->getMessage());
        }
    }
}
