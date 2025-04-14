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
    public $ratings = []; // Масив для рейтингів
    public $comments = []; // Масив для коментарів
    public $images = []; // Масив для зображень
    public $advantages = []; // Масив для переваг
    public $disadvantages = []; // Масив для недоліків
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

        dd($initialOrderItem);

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

    public function addImage($path, $orderItemId)
    {
        if (!isset($this->images[$orderItemId])) {
            $this->images[$orderItemId] = [];
        }
        $this->images[$orderItemId][] = $path;
    }

    public function removeImage($path, $orderItemId)
    {
        if (isset($this->images[$orderItemId])) {
            $this->images[$orderItemId] = array_filter($this->images[$orderItemId], fn($image) => $image !== $path);
        }
    }

    public function updateRating($update_rating, $orderItemId)
    {
        $this->ratings[$orderItemId] = $update_rating;
    }

    public function save_review()
    {
        $errors = [];

        foreach ($this->orderItems as $item) {
            $orderItemId = $item->id;

            $orderItem = OrderItems::with(['productVariant', 'product'])
                ->find($orderItemId);
            $product = $orderItem->product;

            if (!$orderItem || $orderItem->is_completed != 1 || $orderItem->is_write_review != 0) {
                $errors[$orderItemId] = 'Cannot submit review for this item.';
                continue;
            }

            // Валідація для кожного товару
            $validator = Validator::make(
                [
                    'rating' => $this->ratings[$orderItemId] ?? null,
                    'comment' => $this->comments[$orderItemId] ?? null,
                    'advantages' => $this->advantages[$orderItemId] ?? null,
                    'disadvantages' => $this->disadvantages[$orderItemId] ?? null,
                    'images' => $this->images[$orderItemId] ?? [],
                ],
                [
                    'rating' => 'required|integer|min:1|max:5',
                    'comment' => 'required|string|max:500',
                    'advantages' => 'nullable|string|max:500',
                    'disadvantages' => 'nullable|string|max:500',
                    'images' => 'nullable|array',
                    'images.*' => 'nullable|string',
                ],
                [
                    'comment' => 'Please Write a Review',
                    'rating.required' => 'Please select a rating.',
                    'advantages.max' => 'Advantages must not exceed 500 characters.',
                    'disadvantages.max' => 'Disadvantages must not exceed 500 characters.',
                ]
            );

            if ($validator->fails()) {
                $errors[$orderItemId] = $validator->errors()->all();
                continue;
            }

            // Перевіряємо, чи є вже відгук
            $existingReview = ProductRating::where('user_id', Auth::id())
                ->where('product_id', $orderItem->product_id)
                ->first();

            if ($existingReview) {
                $errors[$orderItemId] = 'You have already submitted a review for this product.';
                continue;
            }

            // Збереження зображень
            $savedImages = [];
            if (!empty($this->images[$orderItemId])) {
                foreach ($this->images[$orderItemId] as $key => $path) {
                    $imageName = 'image_' . time() . '_' . $key . '.' . pathinfo($path, PATHINFO_EXTENSION);
                    Storage::disk('public')->move($path, 'review_image/' . $imageName);
                    $savedImages[] = 'review_image/' . $imageName;
                }
            }

            // Дані для збереження
            $validated = [
                'product_id' => $product->id,
                'rating' => $this->ratings[$orderItemId],
                'comment' => $this->comments[$orderItemId],
                'advantages' => $this->advantages[$orderItemId] ?? null,
                'disadvantages' => $this->disadvantages[$orderItemId] ?? null,
                'user_id' => Auth::id(),
                'images' => json_encode($savedImages),
            ];

            // Збереження відгуку
            if ($product->type == "combo-product") {
                ComboProductRating::create($validated);
            } else {
                ProductRating::create($validated);
            }

            $orderItem->update(['is_write_review' => 1]);

            // Оновлення середнього рейтингу
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
        }

        // Перевірка помилок
        if (!empty($errors)) {
            $this->dispatch('validationErrorshow', ['data' => $errors]);
            return;
        }

        // Скидаємо поля форми
        $this->reset(['ratings', 'comments', 'advantages', 'disadvantages', 'images']);
        $this->orderItems = $this->orderItems->fresh();
        $this->dispatch('showSuccess', 'The reviews have been successfully added.');
        // Показати повідомлення
        session()->flash('message', 'Your review has been submitted successfully.');

        // Перенаправлення на ту ж сторінку
        return redirect()->route('orders.review');
    }

    public function render()
    {
        return view('livewire.orders.review')
            ->title('Write a Review |');
    }
}
