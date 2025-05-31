<?php

namespace App\Livewire\RegisterAndLogin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\ReferralCodeTrait;

class Login extends Component
{
    use ReferralCodeTrait;

    public $mobile = "";
    public $password = "";

    public function mount()
    {
        $this->mobile = (config('constants.ALLOW_MODIFICATION') == 0) ? "9876543210" : "";
        $this->password = (config('constants.ALLOW_MODIFICATION') == 0) ? "12345678" : "";
    }
    public function render()
    {
        $system_settings = getSettings('system_settings', true, true);
        $system_settings = json_decode($system_settings);
        $authentication_method = $system_settings->authentication_method ?? "";


        return view('livewire.' . config('constants.theme') . '.register-and-login.login', [
            'authentication_method' => $authentication_method,
            'title' => 'Sign In |'
        ]);
    }

    public function telegram_get_user(Request $request)
    {
        $system_settings = getSettings('system_settings', true, true);
        $system_settings = json_decode($system_settings);

        $telegram_id = $request->id;
        $validator = Validator::make([
            'telegram_id' => $telegram_id,
        ], [
            'telegram_id' => ['required', Rule::exists('users', 'telegram_id')],
            'telegram_id' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['error' => true, 'message' => $errors->first('telegram_id')]);
        }

        $checkHash = $request->hash;
        $telegramData['id'] = $request->id;
        $telegramData['first_name'] = $request->first_name;
        $telegramData['last_name'] = $request->last_name;
        $telegramData['username'] = $request->username;
        $telegramData['photo_url'] = $request->photo_url;
        $telegramData['auth_date'] = $request->auth_date;


        $dataCheckString = '';
        foreach ($telegramData as $key => $value) {
            $dataCheckString .= "$key=$value\n";
        }
        $dataCheckString = trim($dataCheckString);
        $secretKey = hash('sha256', $system_settings->tg_bot_token, true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        if (strcmp($hash, $checkHash) !== 0) {
            $_SESSION['telegram_user'] = $request;
        } else {
            return response()->json(['error' => true, 'message' => 'Error Telegram Authorization' . print_r($telegramData, true)]);
        }
        $user = User::where('telegram_id', '=', $telegram_id)->first();
        if (!$user) {
            return response()->json(['error' => true, 'message' => 'User not found! Sign Up First']);
        }

        $device = $request->header('sec-ch-ua-platform');
        $date = new \DateTime();
        $currentDateTime = $date->format('Y-m-d H:i:s');
        $timeZone = $date->getTimezone()->getName();
        $data = [
            'device' => $device,
            'currentDateTime' => $currentDateTime,
            'timeZone' => $timeZone
        ];

        \Illuminate\Support\Facades\Auth::login($user);


        try {
            sendTelegramMessage($user->telegram_id, 'A new sign-in');
        } catch (\Throwable $th) {
        }

        try {
            sendMailTemplate(to: $user['email'], template_key: "user_login", data: [
                "username" => $user['username'],
                "device" => $data['device'],
                "currentDateTime" => $data['currentDateTime'],
                "timeZone" => $data['timeZone']
            ]);
        } catch (\Throwable $th) {
        }
        return response()->json(['success' => false, 'message' => 'Login Successfully']);
        // return redirect('/');
    }


    public function login(Request $request)
    {
        $validator = Validator::make([
            'mobile' => $this->mobile,
            'password' => $this->password,
        ], [
            'mobile' => ['required', Rule::exists('users', 'mobile')],
            'password' => 'required'
        ], [
            'mobile.exists' => 'Mobile Number is Not Registered'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $this->dispatch('validationErrorshow', ['data' => $errors]);
            return;
        }

        $user = User::where('mobile', $this->mobile)->first();

        // Перевірка та генерація referral_code
        if (empty($user->referral_code)) {
            try {
                $user->referral_code = $this->generateUniqueReferralCode();
                $user->save();
            } catch (\Exception $e) {
                // Логуємо помилку, але продовжуємо процес логіну
                Log::error('Failed to generate referral code: ' . $e->getMessage());
            }
        }

        $device = $request->header('sec-ch-ua-platform');
        $date = new \DateTime();
        $currentDateTime = $date->format('Y-m-d H:i:s');
        $timeZone = $date->getTimezone()->getName();
        $data = [
            'device' => $device,
            'currentDateTime' => $currentDateTime,
            'timeZone' => $timeZone
        ];

        $validate['mobile'] = $this->mobile;
        $validate['password'] = $this->password;

        if (Auth::attempt($validate)) {
            try {
                sendTelegramMessage($user->telegram_id, 'A new sign-in');
            } catch (\Throwable $th) {
            }
            try {
                sendMailTemplate(to: $user->email, template_key: "user_login", data: [
                    "username" => $user->username,
                    "device" => $data['device'],
                    "currentDateTime" => $data['currentDateTime'],
                    "timeZone" => $data['timeZone']
                ]);
            } catch (\Throwable $th) {
                // Логування помилки відправки email
            }
            $this->dispatch('showSuccess', 'User Loggedin Successfully');
            return redirect('/');
        }
        return $this->dispatch('showError', 'Invalid Credentials');
    }

    public function check_telegram_authorization(Request $request)
    {
        $system_settings = getSettings('system_settings', true, true);
        $system_settings = json_decode($system_settings);
        $auth_data = $request->user;
        $check_hash = $auth_data['hash'];

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

        // Перевірка та генерація referral_code для існуючого користувача
        if ($request->check_type != "register") {
            $telegram_id = $auth_data['id'];
            $user = User::where('telegram_id', '=', $telegram_id)->first();

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
}
