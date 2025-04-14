<?php

namespace App\Livewire\Orders;

use Livewire\Component;
use App\Models\OrderItems;
use App\Models\Parcelitem;
use App\Models\ProductRating;
use App\Models\SellerRating;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ComboProductRating;
use App\Models\Seller;

class Review extends Component
{
    use WithFileUploads;

    protected $listeners = ['updateRating', 'updateSellerRating'];

    public $itemId;
    public $orderItems = [];
    public $ratings = []; // Масив для рейтингів товарів
    public $comments = []; // Масив для коментарів до товарів
    public $images = []; // Масив для зображень товарів
    public $advantages = []; // Масив для переваг товарів
    public $disadvantages = []; // Масив для недоліків товарів
    public $sellerQualityOfService; // Оцінка якості обслуговування продавця
    public $sellerOnTimeDelivery; // Оцінка своєчасної доставки продавця
    public $sellerPriceAvailability; // Оцінка відповідності ціни та наявності
    public $sellerComment; // Коментар про продавця
    public $review = null;
    public $initialOrderItem; // Зберігаємо initialOrderItem для доступу до store_id, seller_id, order_id
    public $sellerReview; // Відгук про продавця
    public $productReviews = []; // Відгуки про товари

    public function mount($itemId)
    {
        $this->itemId = $itemId;

        // Завантажуємо initialOrderItem
        $this->initialOrderItem = OrderItems::where('id', $this->itemId)
            ->whereHas('order', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->first();

        if (!$this->initialOrderItem) {
            abort(404, 'Order item not found or you do not have permission to review it.');
        }

        \Log::debug('initialOrderItem seller_id: ' . $this->initialOrderItem->seller_id);

        // Перевіряємо наявність відгуку продавця
        $this->sellerReview = SellerRating::where('user_id', Auth::id())
            ->where('order_id', $this->initialOrderItem->order_id)
            ->where('store_id', $this->initialOrderItem->store_id)
            ->first();

        // Завантажуємо orderItems
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

        // Завантажуємо відгуки про товари
        // Завантажуємо відгуки про товари
        foreach ($this->orderItems as $item) {
            if (!$item->productVariant || !$item->productVariant->product) {
                \Log::warning('Missing productVariant or product for item_id: ' . $item->id);
                $this->productReviews[$item->id] = null;
                continue;
            }

            $productId = $item->productVariant->product_id;
            $productType = $item->productVariant->product->type;

            \Log::debug('Checking review for item_id: ' . $item->id . ', product_id: ' . $productId . ', user_id: ' . Auth::id() . ', product_type: ' . $productType);

            $review = ProductRating::where('user_id', Auth::id())
                ->where('product_id', $productId)
                ->first();

            if (!$review && $productType == 'combo-product') {
                $review = ComboProductRating::where('user_id', Auth::id())
                    ->where('product_id', $productId)
                    ->first();
            }

            $this->productReviews[$item->id] = $review;

            \Log::debug('Review for item_id: ' . $item->id . ': ' . json_encode($review));
            \Log::debug('is_write_review: ' . $item->is_write_review . ', product_type: ' . $productType);
        }

        \Log::debug('sellerReview: ' . json_encode($this->sellerReview));
        \Log::debug('productReviews: ' . json_encode($this->productReviews));
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

    public function updateSellerRating($field, $value)
    {
        if (in_array($field, ['quality_of_service', 'on_time_delivery', 'relevance_price_availability'])) {
            $property = match ($field) {
                'quality_of_service' => 'sellerQualityOfService',
                'on_time_delivery' => 'sellerOnTimeDelivery',
                'relevance_price_availability' => 'sellerPriceAvailability',
            };
            $this->$property = $value;
        }
    }

    public function save_review()
    {
        $errors = [];

        // Валідація оцінки продавця, якщо форма активна
        if (!$this->sellerReview) {
            $sellerValidator = Validator::make(
                [
                    'quality_of_service' => $this->sellerQualityOfService,
                    'on_time_delivery' => $this->sellerOnTimeDelivery,
                    'relevance_price_availability' => $this->sellerPriceAvailability,
                    'comment' => $this->sellerComment,
                ],
                [
                    'quality_of_service' => 'required|integer|min:1|max:5',
                    'on_time_delivery' => 'required|integer|min:1|max:5',
                    'relevance_price_availability' => 'required|integer|min:1|max:5',
                    'comment' => 'required|string|max:1000',
                ],
                [
                    'quality_of_service.required' => 'Please select a rating for Quality of Service.',
                    'on_time_delivery.required' => 'Please select a rating for On-time Delivery.',
                    'relevance_price_availability.required' => 'Please select a rating for Relevance of Price and Availability.',
                    'comment.required' => 'Please write a comment about the seller.',
                ]
            );

            if ($sellerValidator->fails()) {
                $errors['seller'] = $sellerValidator->errors()->all();
            }
        }

        // Валідація відгуків про товари
        foreach ($this->orderItems as $item) {
            $orderItemId = $item->id;

            // Пропускаємо, якщо відгук уже є
            if (isset($this->productReviews[$orderItemId])) {
                continue;
            }

            $orderItem = OrderItems::with(['productVariant', 'product'])
                ->find($orderItemId);

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
        }

        \Log::debug('relevance_price_availability = ' . $this->sellerPriceAvailability);
        \Log::debug('Validation errors', $errors);

        // Якщо є помилки, повертаємо їх
        if (!empty($errors)) {
            $this->dispatch('validationErrorshow', ['data' => $errors]);
            return;
        }

        // Збереження даних, якщо немає помилок
        // Збереження відгуку про продавця, якщо його ще немає
        if (!$this->sellerReview) {
            SellerRating::create([
                'seller_id' => $this->initialOrderItem->seller_id,
                'store_id' => $this->initialOrderItem->store_id,
                'order_id' => $this->initialOrderItem->order_id,
                'user_id' => Auth::id(),
                'comment' => $this->sellerComment,
                'quality_of_service' => $this->sellerQualityOfService,
                'on_time_delivery' => $this->sellerOnTimeDelivery,
                'relevance_price_availability' => $this->sellerPriceAvailability,
            ]);

            // Оновлення рейтингу продавця
            $seller = Seller::where('seller_id', $this->initialOrderItem->seller_id)
                ->where('store_id', $this->initialOrderItem->store_id)
                ->first();
            \Log::debug('Seller find result: ' . json_encode($seller));
            if ($seller) {
                $seller->updateRating();
            } else {
                \Log::error('Seller not found for seller_id: ' . $this->initialOrderItem->seller_id . ', store_id: ' . $this->initialOrderItem->store_id);
            }
        }

        // Збереження відгуків про товари
        foreach ($this->orderItems as $item) {
            $orderItemId = $item->id;

            // Пропускаємо, якщо відгук уже є
            if (isset($this->productReviews[$orderItemId])) {
                continue;
            }

            $orderItem = OrderItems::with(['productVariant', 'product'])
                ->find($orderItemId);
            $product = $orderItem->product;

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

        // Скидаємо поля форми
        $this->reset([
            'ratings',
            'comments',
            'advantages',
            'disadvantages',
            'images',
            'sellerQualityOfService',
            'sellerOnTimeDelivery',
            'sellerPriceAvailability',
            'sellerComment',
        ]);
        $this->orderItems = $this->orderItems->fresh();
        $this->dispatch('showSuccess', 'The reviews have been successfully added.');
        // Показати повідомлення
        session()->flash('message', 'Your review has been submitted successfully.');

        // Перенаправлення на ту ж сторінку
        return redirect()->route('front_end.orders.review', ['itemId' => $this->itemId]);
    }

    public function render()
    {
        return view('livewire.orders.review')
            ->title('Write a Review |');
    }
}
