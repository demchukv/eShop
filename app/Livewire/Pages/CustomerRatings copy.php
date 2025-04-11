<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Services\ReviewService;
use App\Models\ProductRating;
use Livewire\WithFileUploads;
use App\Models\ComboProductRating;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CustomerRatings extends Component
{
    use WithFileUploads;

    protected $listeners = ['updateRating'];

    public $user_id;
    public $product_details;
    public $product_id = "";
    public $product_type = "";
    public $review_id;
    public $comment = "";
    public $images = [];
    public $rating;
    public $is_disabled = false;

    public function __construct()
    {
        $this->user_id = Auth::user() ? Auth::user()->id : null;
    }

    public function render()
    {
        $product_details = $this->product_details;
        $this->product_type = $product_details->type;
        if ($product_details->type == "combo-product") {
            $user_review = fetchDetails('combo_product_ratings', ['user_id' => $this->user_id, "product_id" => $product_details->id]);
        } else {
            $user_review = fetchDetails('product_ratings', ['user_id' => $this->user_id, "product_id" => $product_details->id]);
        }
        $this->review_id = $user_review[0]->id ?? "";
        $this->product_id = $product_details->id ?? "";

        if (isset($this->review_id) && !empty($this->review_id)) {
            $this->rating = $user_review[0]->rating ?? "";
            $this->comment = $user_review[0]->comment ?? "";
        }

        $product_ratings = $this->getProductRating($this->product_id, $product_details->type);
        foreach ($product_ratings as $key => $ratings) {
            $user_profile = fetchDetails('users', ['id' => $ratings->user_id], ['image', 'username']);
            $product_ratings[$key]->user_profile = $user_profile[$key]->image ?? "";
            $product_ratings[$key]->user_name = $user_profile[$key]->username ?? "";
        }

        $sortedReviews = $this->sortReviews($product_ratings, $this->user_id);
        $sortedReviews = array_slice($sortedReviews, 0, 3);
        return view('components.utility.others.customer-ratings', [
            'customer_reviews' => $sortedReviews,
            'product_details' => $this->product_details
        ]);
    }

    public function sortReviews($reviews, $UserId)
    {
        $sortedReviews = [];
        foreach ($reviews as $review) {
            if ($review->user_id == $UserId) {
                array_unshift($sortedReviews, $review);
            } else {
                $sortedReviews[] = $review;
            }
        }
        return $sortedReviews;
    }

    public function getProductRating($product_id, $type)
    {
        if ($type == 'combo-product') {
            return fetchDetails('combo_product_ratings', ['product_id' => $product_id]);
        }
        return fetchDetails('product_ratings', ['product_id' => $product_id]);
    }

    public function updateRating($update_rating)
    {
        $this->rating = $update_rating;
    }

    public function save_review()
    {
        if ($this->is_disabled) {
            return;
        }

        $reviewService = new ReviewService();
        $result = $reviewService->saveReview([
            'product_id' => $this->product_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'images' => $this->images,
        ], $this->product_type == 'combo-product');

        if ($result['success']) {
            $this->dispatch('showSuccess', $result['message']);
            $this->is_disabled = true;
        } else {
            if (isset($result['errors'])) {
                $this->dispatch('validationErrorshow', ['data' => $result['errors']]);
            }
        }
    }

    public function delete_rating()
    {
        if ($this->review_id) {
            if ($this->product_type == "combo-product") {
                $existingReview = ComboProductRating::findOrFail($this->review_id);
            } else {
                $existingReview = ProductRating::findOrFail($this->review_id);
            }

            $delete = $existingReview->delete();
            $this->dispatch('showSuccess', 'The review has been successfully removed.');
            $this->is_disabled = false;
            if ($this->product_type == "combo-product") {
                $averageRating = ComboProductRating::where(["product_id" => $this->product_id])->avg('rating');
            } else {
                $averageRating = ProductRating::where(["product_id" => $this->product_id])->avg('rating');
            }
            $ratingUpdate = [
                'no_of_ratings' => DB::raw('no_of_ratings - 1'),
                'rating' => $averageRating
            ];
            if ($this->product_type == "combo-product") {
                $update = updateDetails($ratingUpdate, ['id' => $this->product_id], 'combo_products');
            } else {
                $update = updateDetails($ratingUpdate, ['id' => $this->product_id], 'products');
            }
        }
    }
}
