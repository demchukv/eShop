@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.update_offer_slider', 'Update Offer Slider') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.update_offer_slider', 'Update Offer Slider') }}
            </h3>
            <p class="sub_title">
                {{ labels('admin_labels.captivate_audiences_with_eye_catching_deal_showcases', 'Captivate Audiences with Eye-Catching Deal Showcases') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item second_breadcrumb_item">
                        {{ labels('admin_labels.offers', 'Offers') }}</li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.update_offer_slider', 'Update Offer Slider') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="col-md-12">
        <form id="offer-slider-form" action="{{ url('admin/offer_sliders/update/' . $data->id) }}" class="submit_form"
            enctype="multipart/form-data" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label"
                                    for="basic-default-fullname">{{ labels('admin_labels.title', 'Title') }}<span
                                        class='text-asterisks text-sm'>*</span></label>
                                <input type="text" class="form-control offer_slider_title" id="basic-default-fullname"
                                    placeholder="Popular Categories" name="title" value="{{ $data->title ?? '' }}">

                            </div>
                            <div class="mb-3">
                                <label class="form-label"
                                    for="basic-default-fullname">{{ labels('admin_labels.select_offer', 'Select Offer') }}</label>
                                <select name="offer_ids[]" required id="offer_sliders_offer"
                                    class="offer_sliders_offer form-select w-100" multiple
                                    data-placeholder="Type to search and select categories">
                                    @foreach ($offers as $offer)
                                        <option value="{{ $offer->id }}"
                                            {{ in_array($offer->id, explode(',', $data->offer_ids ?? '')) ? 'selected' : '' }}>
                                            {{ $offer->type }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">

                            <div class="form-group col-md-12">
                                <label for="" class="form-label">{{ labels('admin_labels.banner_image', 'Banner Image') }}<span
                                        class="text-asterisks text-sm">*</span></label>
                                <div class="col-md-12">
                                    <div class="row form-group">
                                        <div class="col-md-6 file_upload_box border file_upload_border mt-2">
                                            <div class="mt-2">
                                                <div class="col-md-12  text-center">
                                                    <div>
                                                        <a class="media_link" data-input="banner_image" data-isremovable="0"
                                                            data-is-multiple-uploads-allowed="0" data-bs-toggle="modal"
                                                            data-bs-target="#media-upload-modal" value="Upload Photo">
                                                            <h4><i class='bx bx-upload'></i> Upload
                                                        </a></h4>
                                                        <p class="image_recommendation">Recommended Size: 131 x 131
                                                            pixels</p>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        @if ($data->banner_image && !empty($data->banner_image))
                                            <div class="row col-md-6">
                                                <label for="" class="text-danger">*Only Choose When Update is
                                                    necessary</label>
                                                <div class="container-fluid row image-upload-section">
                                                    <div
                                                        class="col-md-6 col-sm-12 shadow p-3 mb-5 bg-white rounded m-4 text-center grow image">
                                                        <div class='image-upload-div'>
                                                            <img class="img-fluid mb-2" alt=""
                                                                src={{ asset('/storage/' . $data->banner_image) }}
                                                                 />
                                                        </div>
                                                        <input type="hidden" name="banner_image"
                                                            value='<?= $data->banner_image ?>'>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="reset"
                                    class="btn mx-2 reset_button offer_slider_reset_button">{{ labels('admin_labels.reset', 'Reset') }}</button>
                                <button type="submit"
                                    class="btn btn-primary submit_button">{{ labels('admin_labels.update_offer_slider', 'Update Offer Slider') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
