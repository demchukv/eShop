<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\OrderItems;
use App\Models\Parcelitem;
use App\Models\ProductRating;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ComboProductRating;

class Review extends Component
{
    use WithFileUploads;

    protected $listeners = ['updateRating'];

    public $itemId;
    public $orderItems = [];
    public $rating = '';
    public $comment = '';
    public $images = [];
    public $review = null;

    public function mount($itemId)
    {
        $this->itemId = $itemId;

        $initialOrderItem = OrderItems::where('id', $this->itemId)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$initialOrderItem) {
            abort(404, 'Order item not found or you do not have permission to review it.');
        }

        $parcelItem = Parcelitem::where('order_item_id', $this->itemId)->first();

        if (!$parcelItem) {
            $this->orderItems = OrderItems::where('id', $this->itemId)
                ->with(['productVariant.product'])
                ->get();
        } else {
            $parcelOrderItemIds = Parcelitem::where('parcel_id', $parcelItem->parcel_id)
                ->pluck('order_item_id')
                ->toArray();

            $this->orderItems = OrderItems::whereIn('id', $parcelOrderItemIds)
                ->whereHas('order', function ($query) {
                    $query->where('user_id', Auth::id());
                })
                ->with(['productVariant.product'])
                ->get();
        }
    }

    public function save_review($orderItemId)
    {
        $orderItem = OrderItems::with(['productVariant', 'product'])
            ->find($orderItemId);
        $product = $orderItem->product;

        if (!$orderItem || $orderItem->is_completed != 1 || $orderItem->is_write_review != 0) {
            $this->addError('general', 'Cannot submit review for this item.');
            return;
        }

        // Валідація з CustomerRatings.php
        $validator = Validator::make(
            [
                'rating' => $this->rating,
                'comment' => $this->comment,
                'images.*' => $this->images,
            ],
            [
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'required|string|max:500',
                'images.*' => 'image|max:2048'
            ],
            [
                'comment' => 'Please Write a Review'
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors();
            $this->dispatch('validationErrorshow', ['data' => $errors]);
            return;
        }

        // Перевіряємо, чи є вже відгук для цього продукту від користувача
        $existingReview = ProductRating::where('user_id', Auth::id())
            ->where('product_id', $orderItem->product_id)
            ->first();

        if ($existingReview) {
            $this->addError('general', 'You have already submitted a review for this product.');
            return;
        }

        // Збереження зображень
        $images = [];
        foreach ($this->images as $key => $image) {
            $imageName = 'image_' . time() . '_' . $key . '.' . $image->getClientOriginalExtension();
            $review_image = $image->storeAs('review_image', $imageName, 'public');
            array_push($images, $review_image);
        }

        // Дані для збереження
        $validated = [
            'product_id' => $product->id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'user_id' => Auth::id(),
            'images' => json_encode($images),
        ];

        if ($product->type == "combo-product") {
            ComboProductRating::create($validated);
        } else {
            ProductRating::create($validated);
        }

        $orderItem->update(['is_write_review' => 1]);

        if ($product->type == "combo-product") {
            $averageRating = ComboProductRating::where(["product_id" => $product->id])->avg('rating');
        } else {
            $averageRating = ProductRating::where(["product_id" => $product->id])->avg('rating');
        }
        $ratingUpdate = [
            'rating' => $averageRating
        ];
        $ratingUpdate['no_of_ratings'] = DB::raw('no_of_ratings + 1');
        if ($product->type == "combo-product") {
            updateDetails($ratingUpdate, ['id' => $validated['product_id']], 'combo_products');
        } else {
            updateDetails($ratingUpdate, ['id' => $validated['product_id']], 'products');
        }

        // Скидаємо поля форми
        $this->review = $validated;
        $this->reset(['rating', 'comment', 'images']);
        $this->orderItems = $this->orderItems->fresh();
        $this->dispatch('showSuccess', 'The review has been successfully added.');
        return;
    }

    public function updateRating($update_rating)
    {
        $this->rating = $update_rating;
    }

    public function render()
    {
        return view('livewire.orders.review')
            ->title('Write a Review |');
    }
}
