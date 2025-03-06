@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.section_order', 'Section Order') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.section_order', 'Section Order') }}</h3>
            <p class="sub_title">
                {{ labels('admin_labels.optimize_and_manage_featured_section_order', 'Optimize and Manage Featured Section Order') }}
            </p>
        </div>
        <div class="col-md-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a
                            href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.section_order', 'Section Order') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="overview-data">
        <div class="card content-area p-4 ">
            <div class="row">
                <div class="col-md-12 main-content">

                    <div class="col-md-6 heading mb-5">
                        <h4>{{ labels('admin_labels.manage_featured_section_order', 'Manage Featured Section Order') }}
                        </h4>
                    </div>
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-8">
                            <div class="row font-weight-bold">
                                <div class="col-3">Row Order</div>
                                <div class="col-3">Image</div>
                                <div class="col-2">Name</div>
                            </div>

                            <div class="section-order-container">
                                @php
                                    $i = 0;
                                @endphp

                                @if (!empty($sections))
                                    @foreach ($sections as $row)
                                        <div class="section-list-item" id="{{ $row['id'] }}">
                                            <div
                                                class="section-item-content d-flex justify-content-between align-items-center">
                                                <span class="section-order col-md-4">{{ $i }}</span>
                                                <div class="col-md-4">
                                                    <img alt=""
                                                        src="{{ route('admin.dynamic_image', [
                                                            'url' => getMediaImageUrl($row['banner_image']),
                                                            'width' => 60,
                                                            'quality' => 90,
                                                        ]) }}">
                                                </div>
                                                <span class="col-md-4">{{ $row['title'] }}</span>
                                            </div>
                                        </div>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                @endif

                            </div>
                            <div class="d-flex justify-content-end"> <button type="button" class="btn btn-dark mt-4"
                                    id="save_section_order">{{ labels('admin_labels.save', 'Save') }}</button>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </section>
@endsection
