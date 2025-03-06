<?php

namespace App\Livewire\MyAccount;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\ChangeUserStatus;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserStatus extends Component
{
    use WithFileUploads;

    #[Validate]
    public $message = "";
    #[Validate('required', message: 'First name is required')]
    public $first_name = "";
    #[Validate('required', message: 'Last name is required')]
    public $last_name = "";
    #[Validate('required', message: 'Birthdate is required')]
    public $manager_birthdate = "";
    #[Validate('required', message: 'Passport is required')]
    public $passport = "";
    #[Validate('required', message: 'Tax ID is required')]
    public $tax_id = "";

    public $photos = [];
    public $newPhotos = null;
    public $photoUrls = [];

    public $user_info;
    public $user_status;
    public $archived_requests;
    public $has_pending_request = false;
    public $dealer_birthdate = "";

    protected $listeners = ['refreshComponent'];

    public function mount()
    {
        $this->user_info = Auth::user();
        if ($this->user_info) {
            $this->first_name = $this->user_info->first_name ?? '';
            $this->last_name = $this->user_info->last_name ?? '';
            $this->manager_birthdate = $this->user_info->birthdate ?? '';
            $this->dealer_birthdate = $this->user_info->birthdate ?? '';
            $this->passport = $this->user_info->passport ?? '';
            $this->tax_id = $this->user_info->tax_id ?? '';

            // Отримуємо активну заявку (pending)
            $this->user_status = ChangeUserStatus::where('user_id', $this->user_info->id)
                ->where('status', 'pending')
                ->first();

            // Перевіряємо наявність pending заявки
            $this->has_pending_request = !is_null($this->user_status);

            // Отримуємо архівні заявки (approved або rejected)
            $this->archived_requests = ChangeUserStatus::where('user_id', $this->user_info->id)
                ->whereIn('status', ['approved', 'rejected'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Обробляємо фотографії для архівних заявок
            foreach ($this->archived_requests as $request) {
                if (!empty($request->photos)) {
                    $photos = is_array($request->photos)
                        ? $request->photos
                        : json_decode($request->photos, true);

                    if (is_array($photos)) {
                        $request->photos = array_map(function ($photo) {
                            return asset($photo);
                        }, $photos);
                    }
                }
            }
        }
    }

    public function updatedNewPhotos()
    {
        if (!is_array($this->newPhotos)) {
            $this->newPhotos = [$this->newPhotos];
        }

        $this->validate([
            'newPhotos.*' => 'image|max:2048',
        ]);

        foreach ($this->newPhotos as $photo) {
            $this->photos[] = $photo;
            $this->photoUrls[] = [
                'temporaryUrl' => $photo->temporaryUrl(),
                'name' => $photo->getClientOriginalName()
            ];
        }

        $this->newPhotos = null;
        $this->dispatch('photos-updated', ['photos' => $this->photoUrls]);
    }

    public function removePhoto($index)
    {
        unset($this->photos[$index]);
        unset($this->photoUrls[$index]);

        $this->photos = array_values($this->photos);
        $this->photoUrls = array_values($this->photoUrls);

        $this->dispatch('photos-updated', ['photos' => $this->photoUrls]);
    }

    public function render()
    {
        if (!$this->user_info) {
            return redirect()->route('login');
        }

        if ($this->user_status && !empty($this->user_status->photos)) {
            // Перевіряємо, чи photos вже є масивом
            $photos = is_array($this->user_status->photos)
                ? $this->user_status->photos
                : json_decode($this->user_status->photos, true);

            if (is_array($photos)) {
                $this->user_status->photos = array_map(function ($photo) {
                    return asset($photo);
                }, $photos);
            }
        }

        return view('livewire.' . config('constants.theme') . '.my-account.user-status', [
            'user_info' => $this->user_info,
            'user_status' => $this->user_status,
            'archived_requests' => $this->archived_requests,
            'has_pending_request' => $this->has_pending_request
        ]);
    }

    public function send_dealer_request(Request $request)
    {
        $user_id = Auth::user()->id ?? "";

        // Перевіряємо чи немає активної заявки
        $pending_request = ChangeUserStatus::where('user_id', $user_id)
            ->where('status', 'pending')
            ->first();

        if ($pending_request) {
            return response()->json([
                'error' => true,
                'message' => 'You already have a pending request.',
            ]);
        }

        // Створюємо директорію для користувача, якщо вона не існує
        $userDirectory = "dealer-documents/{$user_id}";
        if (!Storage::disk('public')->exists($userDirectory)) {
            Storage::disk('public')->makeDirectory($userDirectory);
        }

        // Зберігаємо фотографії
        $uploadedPhotos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store($userDirectory, 'public');
                $uploadedPhotos[] = Storage::url($path);
            }
        }

        $user_status = ChangeUserStatus::create([
            'user_id' => $user_id,
            'type' => 'dealer',
            'status' => 'pending',
            'message' => $request->message,
            'photos' => $uploadedPhotos,
        ]);

        User::where('id', $user_id)->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthdate' => $request->dealer_birthdate,
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Dealer request sent successfully!',
        ]);
    }

    public function send_manager_request()
    {
        $this->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'manager_birthdate' => 'required|date',
            'photos' => 'required|array|min:1',
            'photos.*' => 'image|max:2048',
            'message' => 'nullable|string',
            'passport' => 'required|string',
            'tax_id' => 'required|string'
        ]);

        $user_id = Auth::user()->id ?? "";

        // Перевіряємо чи немає активної заявки
        $pending_request = ChangeUserStatus::where('user_id', $user_id)
            ->where('status', 'pending')
            ->first();

        if ($pending_request) {
            session()->flash('error', 'You already have a pending request.');
            return;
        }

        // Створюємо директорію для користувача, якщо вона не існує
        $userDirectory = "manager-documents/{$user_id}";
        if (!Storage::disk('public')->exists($userDirectory)) {
            Storage::disk('public')->makeDirectory($userDirectory);
        }

        $uploadedPhotos = [];
        foreach ($this->photos as $photo) {
            // Зберігаємо фото в папку користувача
            $path = $photo->store($userDirectory, 'public');
            $uploadedPhotos[] = Storage::url($path);
        }

        $data = [
            'user_id' => $user_id,
            'type' => 'manager',
            'status' => 'pending',
            'message' => $this->message,
            'photos' => $uploadedPhotos,
            'passport' => $this->passport,
            'tax_id' => $this->tax_id
        ];

        $user_status = ChangeUserStatus::create($data);
        User::where('id', $user_id)->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'birthdate' => $this->manager_birthdate
        ]);

        $this->reset(['first_name', 'last_name', 'manager_birthdate', 'photos', 'photoUrls', 'message', 'passport', 'tax_id']);
        session()->flash('success', 'Request sent successfully!');

        $this->dispatch('closeModal');
        $this->dispatch('refresh-page');
    }

    public function refreshComponent()
    {
        $this->dispatch('$refresh');
    }
}
