<?php

namespace App\Livewire\MyAccount;

use App\Models\SellerInvite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class SellerInvites extends Component
{
    use WithPagination;

    public $successMessage = '';
    public $errorMessage = '';

    // Додаємо властивість для відстеження сторінки
    protected $paginationTheme = 'bootstrap'; // Опціонально: для Bootstrap-стилів пагінації

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
    }

    public function createInvite()
    {
        if (!Auth::check()) {
            $this->dispatch('show-error-invite', message: 'Login please');
            return;
        }

        do {
            $link = Str::random(25);
        } while (SellerInvite::where('link', $link)->exists());

        SellerInvite::create([
            'link' => $link,
            'user_id' => Auth::id(),
            'status' => SellerInvite::STATUS_ACTIVE,
        ]);

        $this->dispatch('show-success-invite', message: "Invitation created!");
        $this->resetPage(); // Скидаємо сторінку до першої після створення
    }

    public function deleteInvite($inviteId)
    {
        if (!Auth::check()) {
            $this->dispatch('show-error-invite', message: 'Login please');
            return;
        }

        $invite = SellerInvite::find($inviteId);

        if (!$invite) {
            $this->dispatch('show-error-invite', message: 'Invitation not found');
            return;
        }

        if ($invite->user_id !== Auth::id()) {
            $this->dispatch('show-error-invite', message: 'You cannot delete this link.');
            return;
        }

        $invite->delete();
        $this->dispatch('show-success-invite', message: 'Link removed');
        $remainingInvites = SellerInvite::where('user_id', Auth::id())->count();
        if ($remainingInvites === 0) {
            $this->resetPage(); // Скидаємо до першої сторінки, якщо записів немає
        } else {
            // Оновлюємо пагінацію, якщо видалений елемент був останнім на сторінці
            $currentPage = $this->getPage();
            $totalPages = ceil($remainingInvites / SellerInvite::ITEM_PER_PAGE);
            if ($currentPage > $totalPages) {
                $this->setPage($totalPages); // Переходимо на останню сторінку
            }
        }
    }



    public function render()
    {
        if (!Auth::check()) {
            return view('livewire.elegant.my-account.seller-invites', [
                'invites' => collect(),
            ]);
        }

        // Отримуємо всі активні запрошення користувача
        $invites = SellerInvite::where('user_id', Auth::id())
            ->where('status', SellerInvite::STATUS_ACTIVE)
            ->get();

        // Перевіряємо та оновлюємо статус прострочених запрошень
        foreach ($invites as $invite) {
            if ($invite->isExpired() && $invite->status !== SellerInvite::STATUS_EXPIRED) {
                $invite->update(['status' => SellerInvite::STATUS_EXPIRED]);
            }
        }

        // Отримуємо оновлений список із пагінацією
        $invites = SellerInvite::where('user_id', Auth::id())->paginate(SellerInvite::ITEM_PER_PAGE);

        return view('livewire.elegant.my-account.seller-invites', [
            'invites' => $invites,
        ]);
    }
}
