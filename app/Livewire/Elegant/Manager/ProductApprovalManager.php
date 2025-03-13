<?php

namespace App\Livewire\Elegant\Manager;

use App\Models\Product;
use App\Models\ProductApproval;
use App\Models\ProductApprovalComment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductApprovalManager extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';
    public $comment = ''; // Для зберігання тексту коментаря
    public $selectedProductId = null; // Для ідентифікатора продукту в модальному вікні
    public $comments = []; //
    public $reasons = [];

    // Список можливих причин відхилення
    protected $availableReasons = [
        'missing image',
        'missing description',
    ];

    public function approveProduct($productId)
    {
        $managerId = Auth::id();

        $existingApproval = ProductApproval::where('product_id', $productId)
            ->where('manager_id', $managerId)
            ->exists();

        if (!$existingApproval) {
            ProductApproval::create([
                'product_id' => $productId,
                'manager_id' => $managerId,
                'approved_at' => now(),
                'status' => ProductApproval::STATUS_APPROVED,
            ]);

            $approvalCount = ProductApproval::where('product_id', $productId)
                ->where('status', ProductApproval::STATUS_APPROVED)
                ->count();
            if ($approvalCount >= 10) {
                Product::where('id', $productId)->update(['status' => 1]);
                $this->dispatch('show-success', message: 'Product approved successfully!');
            } else {
                $this->dispatch('show-success', message: 'Product approval recorded. ' . (10 - $approvalCount) . ' approvals remaining.');
            }
        } else {
            $this->dispatch('show-error', message: 'You have already approved this product.');
        }
    }
    public function openCommentModal($productId)
    {
        $this->selectedProductId = $productId;
        $this->comment = ''; // Очищаємо поле коментаря
        $this->reasons = [];
        // Завантажуємо попередні коментарі для цього продукту
        $this->comments = ProductApprovalComment::where('product_id', $productId)
            ->with('manager') // Завантажуємо дані менеджера
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $this->dispatch('open-comment-modal');
    }

    public function saveComment()
    {
        $managerId = Auth::id();

        $this->validate([
            'comment' => 'required|string|max:500',
            'reasons' => 'array',
        ]);

        // Перевіряємо, чи менеджер уже відгукнувся про цей товар
        $existingApproval = ProductApproval::where('product_id', $this->selectedProductId)
            ->where('manager_id', $managerId)
            ->exists();

        if ($existingApproval) {
            $this->dispatch('show-error', message: 'You have already reviewed this product.');
            return;
        }

        // Створюємо запис про відхилення
        ProductApproval::create([
            'product_id' => $this->selectedProductId,
            'manager_id' => $managerId,
            'approved_at' => now(),
            'status' => ProductApproval::STATUS_DISAPPROVED, // Зберігаємо 'disapproved'
        ]);

        // Зберігаємо коментар із причинами
        ProductApprovalComment::create([
            'product_id' => $this->selectedProductId,
            'manager_id' => $managerId,
            'comment' => $this->comment,
            'reason' => json_encode($this->reasons), // Зберігаємо як JSON
        ]);

        // Оновлюємо список коментарів після збереження
        $this->comments = ProductApprovalComment::where('product_id', $this->selectedProductId)
            ->with('manager')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $this->dispatch('show-success', message: 'Product disapproved successfully!');
        $this->comment = '';
        $this->reasons = [];
    }
    public function render()
    {
        $user = Auth::user();
        $managerId = Auth::id();

        $products = Product::where('status', 2)
            ->whereDoesntHave('approvals', function ($query) use ($managerId) {
                $query->where('manager_id', $managerId); // Виключаємо товари, які менеджер уже розглянув
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('product_identity', 'like', '%' . $this->search . '%');
            })
            ->withCount(['approvals' => function ($query) {
                $query->where('status', ProductApproval::STATUS_APPROVED); // Лише 'approved'
            }])
            ->with('seller')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.elegant.manager.product-approval', [
            'user_info' => $user,
            'products' => $products,
        ]);
    }
}
