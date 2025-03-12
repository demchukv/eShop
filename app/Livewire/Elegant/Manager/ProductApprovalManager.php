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
            ]);

            $approvalCount = ProductApproval::where('product_id', $productId)->count();
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
        $this->validate([
            'comment' => 'required|string|max:500',
        ]);

        ProductApprovalComment::create([
            'product_id' => $this->selectedProductId,
            'manager_id' => Auth::id(),
            'comment' => $this->comment,
        ]);

        // Оновлюємо список коментарів після збереження
        $this->comments = ProductApprovalComment::where('product_id', $this->selectedProductId)
            ->with('manager')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
        $this->dispatch('show-success', message: 'Comment saved successfully!');
        $this->comment = ''; // Очищаємо поле після збереження
    }
    public function render()
    {
        $user = Auth::user();
        $products = Product::where('status', 2)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('product_identity', 'like', '%' . $this->search . '%');
            })
            ->withCount('approvals')
            ->with('seller')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
        // dd($products);
        return view('livewire.elegant.manager.product-approval', [
            'user_info' => $user,
            'products' => $products,
        ]);
    }
}
