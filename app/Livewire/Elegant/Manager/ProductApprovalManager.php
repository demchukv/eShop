<?php

namespace App\Livewire\Elegant\Manager;

use App\Models\Product;
use App\Models\ProductApproval;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProductApprovalManager extends Component
{
    use WithPagination;

    public $perPage = 10;
    public $search = '';

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
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.elegant.manager.product-approval', [
            'user_info' => $user,
            'products' => $products,
        ]);
    }
}
