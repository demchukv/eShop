<div class="tabs-listing section pb-0">
    <ul class="product-tabs list-unstyled d-flex-wrap border-bottom d-none d-md-flex">
        <li rel="reviews"><a class="tablink" rel="reviews">{{ labels('front_messages.reviews', 'Reviews') }}</a></li>
        <li rel="writeReview"><a class="tablink"
                rel="writeReview">{{ labels('front_messages.write_reviews', 'Write Review') }}</a></li>
    </ul>

    <div class="tab-container">
        <!--Review-->
        <h3 class="tabs-ac-style d-md-none" rel="reviews">
            {{ labels('front_messages.reviews', 'Reviews') }}
        </h3>
        <div id="reviews" class="tab-content">
            @if ($customer_reviews != [])
                <div class="spr-reviews">
                    <x-utility.others.ratingCard :$customer_reviews />
                    @if ($product_details->no_of_ratings >= 4)
                        <a href="/products/{{ $product_details->slug }}/reviews" wire:navigate
                            class="d-flex justify-content-center align-content-center pt-3 fw-500 fs-6 ">{{ labels('front_messages.view_all', 'View All') }}
                            {{ $product_details->no_of_ratings }}
                            {{ labels('front_messages.reviews', 'Reviews...') }}</a>
                    @endif
                </div>
            @endif
        </div>
        <!--End Review-->
        <!--Write Review-->
        <h3 class="tabs-ac-style d-md-none" rel="writeReview">
            {{ labels('front_messages.write_reviews', 'Write Review') }}
        </h3>
        <div id="reviews" class="tab-content">
            @auth
                @if ($product_details->is_purchased == true)
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 mb-4" wire:ignore>
                        <form wire:submit="save_review" class="product-review-form new-review-form"
                            enctype="multipart/form-data">
                            @if ($review_id != '')
                                <h3 class="spr-form-title">{{ labels('front_messages.edit_review', 'Edit Review') }}</h3>
                            @else
                                <h3 class="spr-form-title">{{ labels('front_messages.write_review', 'Write a Review') }}
                                </h3>
                            @endif
                            <fieldset class="row spr-form-contact">
                                <div class="col-sm-6 spr-form-review-rating form-group">
                                    <input type="hidden" name="id" value="">
                                    <label class="spr-form-label">{{ labels('front_messages.rating', 'Rating') }}</label>
                                    <div class="product-review pt-1">
                                        <div class="review-rating">
                                            <input id="input-3-ltr-star-md" name="input-3-ltr-star-md"
                                                class="kv-ltr-theme-svg-star star-rating rating-loading review_rating"
                                                value="" wire:model="rating" dir="ltr" data-size="s"
                                                data-show-clear="false" data-show-caption="false" data-step="1">
                                        </div>
                                        @error('rating')
                                            <p class="fw-400 text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 spr-form-review-body form-group">
                                    <label class="spr-form-label"
                                        for="add_image">{{ labels('front_messages.add_image_or_video', 'Add Image or Video') }}</label>
                                    <input wire:model="images" id="review_image" type="file" name="image[]" multiple
                                        accept="image/gif, image/jpeg, image/png">
                                </div>
                                @error('images')
                                    <p class="fw-400 text-danger mt-1"></p>
                                @enderror
                                <div class="col-12 spr-form-review-body form-group">
                                    <label class="spr-form-label"
                                        for="message">{{ labels('front_messages.description', 'Description') }}</label>
                                    <div class="spr-form-input">
                                        <textarea wire:model="comment" class="spr-form-input spr-form-input-textarea" id="message" name="message"
                                            rows="3"></textarea>
                                        @error('comment')
                                            <p class="fw-400 text-danger mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </fieldset>
                            <div class="spr-form-actions clearfix">
                                <input type="submit" class="btn btn-primary spr-button spr-button-primary"
                                    value="Submit Review" />
                            </div>
                        </form>
                    </div>
                @endauth
            @endif
        </div>
        <!--End Write Review-->
    </div>
</div>
