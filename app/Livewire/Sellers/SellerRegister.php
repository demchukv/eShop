<?php

namespace App\Livewire\Sellers;

use App\Models\SellerInvite;
use App\Models\User;
use App\Models\Seller;
use Livewire\Component;
use Spatie\LivewireFilepond\WithFilePond;
use App\Models\City;
use App\Models\Zipcode;
use Illuminate\Support\Facades\File;
use App\Models\Media;
use App\Models\StorageType;
use App\Models\Store;
use Exception;
use App\Traits\ReferralCodeTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class SellerRegister extends Component
{
    use WithFilePond;
    use ReferralCodeTrait;

    public $file;

    public $link;
    public $invite;
    public $message = '';
    public $user_info;
    public $telegram_id = '';
    public $telegram_username = '';
    public $username = '';
    public $mobile = '';
    public $phone_full = '';
    public $country_code = '';
    public $email = '';
    public $first_name = '';
    public $last_name = '';
    public $password = '';
    public $password_confirmation = '';
    public $address = '';
    public $agree = false;
    public $friends_code = '';

    public $account_number = '';
    public $account_name = '';
    public $bank_name = '';
    public $bank_code = '';

    public $store_name = '';
    public $store_url = '';
    public $description = '';

    public $city = '';
    public $zipcode = '';

    public $tax_name = '';
    public $tax_number = '';
    public $pan_number = '';
    public $latitude = '';
    public $longitude = '';

    public $profile_image = null;
    public $address_proof = null;
    public $authorized_signature = null;
    public $store_logo = null;
    public $store_thumbnail = null;
    public $other_document = null;
    public $other_documents = [];
    public $national_identity_card = null;

    protected $rules = [
        'username' => 'required|string|max:255|unique:users,username',
        'mobile' => 'required|string|min:10',
        'phone_full' => 'required|numeric|digits_between:8,15|unique:users,mobile',
        'country_code' => 'required|numeric|digits_between:1,3',
        'email' => 'required|email|max:255|unique:users,email',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required',
        'address' => 'required|string|min:10',
        'account_number' => 'required|string|min:10',
        'account_name' => 'required|string|min:10',
        'bank_name' => 'required|string|min:10',
        'bank_code' => 'required|string|min:5',
        'store_name' => 'required|string|min:4',
        'store_url' => 'required|string|min:4',
        'description' => 'required|string|min:10',

        'city' => 'string',
        'zipcode' => 'string',

        'tax_name' => 'nullable|string',
        'tax_number' => 'nullable|string',
        'pan_number' => 'nullable|string',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',

        'profile_image' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
        'address_proof' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
        'authorized_signature' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
        'store_logo' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
        'store_thumbnail' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
        'national_identity_card' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
        'other_document' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',

        'other_documents.*' => 'required|mimetypes:image/jpg,image/jpeg,image/png|max:3000',
    ];

    protected $messages = [
        'password.confirmed' => 'The password confirmation does not match.',
    ];

    public function mount($link)
    {
        Log::info('Current Route Name: ' . Route::currentRouteName());
        Log::info('Current Route URI: ' . Route::current()->uri());

        if (Route::current()->uri() != 'seller-register/success') {

            $this->invite = SellerInvite::where('link', $this->link)->first();

            if (!$this->invite || $this->invite->isExpired() || $this->invite->status !== SellerInvite::STATUS_ACTIVE) {
                $this->message = 'Invalid or expired invitation link.';
                return redirect()->route('seller.telegram.verify', ['link' => $this->link]);
            }

            $telegramData = session()->get('seller_telegram_data');
            if (!$telegramData || $telegramData['invite_link'] !== $this->link) {
                $this->message = 'Please verify your Telegram account first.';
                return redirect()->route('seller.telegram.verify', ['link' => $this->link]);
            }

            $this->telegram_id = $telegramData['telegram_id'];
            $this->telegram_username = $telegramData['telegram_username'];
            $this->username = $this->telegram_username; // Заповнюємо за замовчуванням
            $this->first_name = $telegramData['first_name'];
            $this->last_name = $telegramData['last_name'];
            $this->user_info = User::where('id', $this->invite->user_id)->first();
            if ($this->user_info->referral_code == "") {
                $new_referral_code = $this->generateUniqueReferralCode();
                $this->user_info->update(['referral_code' => $new_referral_code]);
                $this->user_info->referral_code = $new_referral_code;
            }
            $this->friends_code = $this->user_info->referral_code;
        }
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'other_document') {
            $this->other_documents[] = $this->other_document;
        }
    }

    public function register()
    {

        $this->validate();

        if (!$this->invite || $this->invite->status !== SellerInvite::STATUS_ACTIVE) {
            $this->message = 'Invalid or used invitation link.';
            $this->dispatch('show-error', message: $this->message);
            return;
        }

        $storeImgPath = public_path(config('constants.SELLER_IMG_PATH'));

        if (!File::exists($storeImgPath)) {
            File::makeDirectory($storeImgPath, 0755, true);
        }

        $seller_data = [];
        $seller_store_data = [];
        $store_id = isset($request->store_id) && !empty($request->store_id) ? $request->store_id : getStoreId();
        $user = User::where('mobile', $this->mobile)->where('role_id', 4)->first();

        $media_storage_settings = fetchDetails('storage_types', ['is_default' => 1], '*');
        $mediaStorageType = isset($media_storage_settings) && !empty($media_storage_settings) ? $media_storage_settings[0]->id : 1;
        $disk = isset($media_storage_settings) && !empty($media_storage_settings) ? $media_storage_settings[0]->name : 'public';

        $media = StorageType::find($mediaStorageType);

        try {
            if ($this->other_documents) {
                foreach ($this->other_documents as $file) {
                    $other_documents = $media->addMedia($file)
                        ->sanitizingFileName(function ($fileName) use ($media) {
                            $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                            $uniqueId = time() . '_' . mt_rand(1000, 9999);
                            $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                            $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);

                            return "{$baseName}-{$uniqueId}.{$extension}";
                        })
                        ->toMediaCollection('sellers', $disk);
                    $other_document_file_names[] = $other_documents->file_name;
                    $mediaIds[] = $other_documents->id;
                }
            }
            if ($this->profile_image) {
                $profile_image = $this->profile_image;
                $profile_image = $media->addMedia($profile_image)
                    ->sanitizingFileName(function ($fileName) use ($media) {
                        $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        $uniqueId = time() . '_' . mt_rand(1000, 9999);
                        $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                        $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
                        return "{$baseName}-{$uniqueId}.{$extension}";
                    })
                    ->toMediaCollection('sellers', $disk);
                $mediaIds[] = $profile_image->id;
            }
            if ($this->address_proof) {
                $addressProofFile = $this->address_proof;
                $address_proof = $media->addMedia($addressProofFile)
                    ->sanitizingFileName(function ($fileName) use ($media) {
                        $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        $uniqueId = time() . '_' . mt_rand(1000, 9999);
                        $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                        $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
                        return "{$baseName}-{$uniqueId}.{$extension}";
                    })
                    ->toMediaCollection('sellers', $disk);
                $mediaIds[] = $address_proof->id;
            }
            if ($this->store_logo) {
                $storeLogoFile = $this->store_logo;
                $store_logo = $media->addMedia($storeLogoFile)
                    ->sanitizingFileName(function ($fileName) use ($media) {
                        $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        $uniqueId = time() . '_' . mt_rand(1000, 9999);
                        $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                        $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
                        return "{$baseName}-{$uniqueId}.{$extension}";
                    })
                    ->toMediaCollection('sellers', $disk);
                $mediaIds[] = $store_logo->id;
            }

            if ($this->store_thumbnail) {
                $storeThumbnailFile = $this->store_thumbnail;
                $store_thumbnail = $media->addMedia($storeThumbnailFile)
                    ->sanitizingFileName(function ($fileName) use ($media) {
                        $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        $uniqueId = time() . '_' . mt_rand(1000, 9999);
                        $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                        $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
                        return "{$baseName}-{$uniqueId}.{$extension}";
                    })
                    ->toMediaCollection('sellers', $disk);
                $mediaIds[] = $store_thumbnail->id;
            }
            if ($this->authorized_signature) {
                $authorizedSignatureFile = $this->authorized_signature;
                $authorized_signature = $media->addMedia($authorizedSignatureFile)
                    ->sanitizingFileName(function ($fileName) use ($media) {
                        $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        $uniqueId = time() . '_' . mt_rand(1000, 9999);
                        $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                        $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
                        return "{$baseName}-{$uniqueId}.{$extension}";
                    })
                    ->toMediaCollection('sellers', $disk);
                $mediaIds[] = $authorized_signature->id;
            }
            if ($this->national_identity_card) {
                $nationalIdentityCardFile = $this->national_identity_card;
                $national_identity_card = $media->addMedia($nationalIdentityCardFile)
                    ->sanitizingFileName(function ($fileName) use ($media) {
                        $sanitizedFileName = strtolower(str_replace(['#', '/', '\\', ' '], '-', $fileName));
                        $uniqueId = time() . '_' . mt_rand(1000, 9999);
                        $extension = pathinfo($sanitizedFileName, PATHINFO_EXTENSION);
                        $baseName = pathinfo($sanitizedFileName, PATHINFO_FILENAME);
                        return "{$baseName}-{$uniqueId}.{$extension}";
                    })
                    ->toMediaCollection('sellers', $disk);
                $mediaIds[] = $national_identity_card->id;
            }
            // //code for storing s3 object url for media
            if ($disk == 's3') {
                $media_list = $media->getMedia('sellers');
                for ($i = 0; $i < count($mediaIds); $i++) {
                    $media_url = $media_list[($media_list->count()) - (count($mediaIds) - $i)]->getUrl();

                    switch ($i) {
                        case 0:
                            $address_proof_url = $media_url;
                            break;
                        case 1:
                            $logo_url = $media_url;
                            break;
                        case 2:
                            $store_thumbnail_url = $media_url;
                            break;
                        case 3:
                            $authorized_signature_url = $media_url;
                            break;
                        case 4:
                            $national_identity_card_url = $media_url;
                            break;
                        case 5:
                            $profile_image_url = $media_url;
                            break;
                        case 6:
                            $other_documents_url = $media_url;
                            break;
                            // Add more cases as needed
                    }
                    Media::destroy($mediaIds[$i]);
                }
            }
        } catch (Exception $e) {
            $this->dispatch('show-error', message: $e->getMessage());
        }

        $user_data = [
            'role_id' => 4,
            'active' => 1,
            'password' => bcrypt($this->password),
            'address' => $this->address,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile' => $this->phone_full,
            'country_code' => $this->country_code,
            'email' => $this->email,
            'image' => $disk == 's3' ? (isset($profile_image_url) ? $profile_image_url : '') : (isset($profile_image->file_name) ? '/' . $profile_image->file_name : ''),
            'telegram_id' => $this->telegram_id,
            'telegram_username' => $this->telegram_username,
            'referral_code' => $this->generateUniqueReferralCode(),
            'friends_code' => $this->friends_code
        ];


        $seller_store_data['address_proof'] = $disk == 's3' ? (isset($address_proof_url) ? $address_proof_url : '') : (isset($address_proof->file_name) ? '/' . $address_proof->file_name : '');

        $seller_store_data['logo'] = $disk == 's3' ? (isset($logo_url) ? $logo_url : '') : (isset($store_logo->file_name) ? '/' . $store_logo->file_name : '');

        $seller_store_data['other_documents'] = $disk == 's3' ? (isset($other_documents_url) ? ($other_documents_url) : '') : (isset($other_documents->file_name) ? json_encode($other_document_file_names) : '');

        $seller_store_data['store_thumbnail'] = $disk == 's3' ? (isset($store_thumbnail_url) ? $store_thumbnail_url : '') : (isset($store_thumbnail->file_name) ? '/' . $store_thumbnail->file_name : '');

        $seller_data['authorized_signature'] = $disk == 's3' ? (isset($authorized_signature_url) ? $authorized_signature_url : '') : (isset($authorized_signature->file_name) ? '/' . $authorized_signature->file_name : '');

        $seller_data['national_identity_card'] = $disk == 's3' ? (isset($national_identity_card_url) ? $national_identity_card_url : '') : (isset($national_identity_card->file_name) ? '/' . $national_identity_card->file_name : '');

        $permmissions = array();
        $permmissions['require_products_approval'] = 1;
        $permmissions['customer_privacy'] = 1;
        $permmissions['view_order_otp'] = 1;

        //create user
        $user = User::create($user_data);
        $seller_data = array_merge($seller_data, [
            'user_id' => $user->id,
            'status' => 2, // not approved
            'pan_number' => $this->pan_number,
            'disk' => isset($authorized_signature->disk) && !empty($authorized_signature->disk) ? $authorized_signature->disk : 'public',
        ]);
        //create seller
        $seller = Seller::create($seller_data);

        $seller_store_data = array_merge($seller_store_data, [
            'user_id' => $user->id,
            'seller_id' => $seller->id,
            'store_name' => $this->store_name,
            'store_url' => $this->store_url,
            'store_description' => $this->description,
            'commission' => 0,
            'account_number' => $this->account_number,
            'account_name' => $this->account_name,
            'bank_name' => $this->bank_name,
            'bank_code' => $this->bank_code,
            'status' => 2, // not approved
            'tax_name' => $this->tax_name,
            'tax_number' => $this->tax_number,
            'category_ids' => '',
            'permissions' => (isset($permmissions) && $permmissions != "") ? json_encode($permmissions) : null,
            'slug' => generateSlug($this->store_name, 'seller_store'),
            'store_id' => $store_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'city' => $this->city,
            'zipcode' => $this->zipcode,
            'disk' => isset($address_proof->disk) && !empty($address_proof->disk) ? $address_proof->disk : 'public',
        ]);
        // create store
        $seller_store = DB::table('seller_store')->insert($seller_store_data);

        $this->invite->update(['status' => SellerInvite::STATUS_USED]);

        session()->put('seller_registration_success', [
            'username' => $this->username,
        ]);
        // Auth::login($user);

        return redirect()->route('seller.register.success');
    }


    public function render()
    {
        $zipcodes = Zipcode::orderBy('id', 'desc')->get();
        $cities = City::orderBy('id', 'desc')->get();
        return view('livewire.elegant.sellers.seller-register', compact('zipcodes', 'cities'));
    }
}
