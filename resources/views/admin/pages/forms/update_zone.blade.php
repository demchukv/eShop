@extends('admin/layout')
@section('title')
    {{ labels('admin_labels.zones', 'Zones') }}
@endsection
@section('content')
    <div class="d-flex row align-items-center">
        <div class="col-md-6 col-xl-6 page-info-title">
            <h3>{{ labels('admin_labels.zones', 'Zones') }}</h3>
            <p class="sub_title">
                {{ labels('admin_labels.enhance_visual_appeal_with_effortless_zone_integration', 'Enhance Visual Appeal with Effortless Zone Integration') }}
            </p>
        </div>
        <div class="col-md-6 col-xl-6 d-flex justify-content-end">
            <nav aria-label="breadcrumb" class="float-end">
                <ol class="breadcrumb">
                    <i class='bx bx-home-smile'></i>
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">{{ labels('admin_labels.home', 'Home') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ labels('admin_labels.zones', 'Zones') }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form class="form-horizontal form-submit-event submit_form" action="{{ url('/admin/zones/update/' . $zone->id) }}"
                    method="POST" id="" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="card-body">
                        <h5 class="mb-4">
                            {{ labels('admin_labels.add_zone', 'Manage Zones') }}
                        </h5>

                        <div class="form-group">
                            <label for="name" class="form-label">{{ labels('admin_labels.name', 'Name') }}<span
                                    class="text-asterisks text-sm">*</span></label>
                            <input type="text" class="form-control" name="name" value={{ $zone->name ?? '' }}>
                        </div>
                        <!-- Zipcodes Repeater -->
                        <label for="name" class="form-label">
                            {{ labels('admin_labels.serviceable_zipcodes', 'Serviceable Zipcodes') }}<span
                                class="text-asterisks text-sm">*</span>
                        </label>
                        <div class="repeater">
                            <div data-repeater-list="zipcode_group">
                                @foreach ($zipcodes as $zipcode)
                                    <div data-repeater-item>
                                        <div class="row">
                                            <div class="col-md-5 mt-2">
                                                <select class="form-select zipcode_list" name="serviceable_zipcode_id">
                                                    <option value="">
                                                        {{ labels('admin_labels.select_zipcode', 'Select Zipcode') }}
                                                    </option>
                                                    @foreach ($all_zipcodes as $z)

                                                        <option value="{{ $z->id }}"
                                                            {{ $zipcode->id == $z->id ? 'selected' : '' }}>
                                                            {{ $z->zipcode }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5 mt-2">
                                                <input type="text" name="zipcode_delivery_charge" class="form-control"
                                                    placeholder="Delivery Charge"
                                                    value="{{ $zipcode->delivery_charges }}" />
                                            </div>
                                            <div class="col-md-2">
                                                <input data-repeater-delete type="button" class="btn btn-secondary mt-2"
                                                    value="Delete" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input data-repeater-create type="button" class="btn btn-primary mt-2" value="Add" />
                        </div>

                        <!-- Cities Repeater -->
                        <label for="name" class="form-label mt-4">
                            {{ labels('admin_labels.serviceable_cities', 'Serviceable Cities') }}<span
                                class="text-asterisks text-sm">*</span>
                        </label>
                        <div class="repeater">
                            <div data-repeater-list="city_group">
                                @foreach ($cities as $city)
                                    <div data-repeater-item>
                                        <div class="row city_list_parent">
                                            <div class="col-md-5 mt-2">
                                                <select class="form-select city_list" name="serviceable_city_id">
                                                    <option value="">
                                                        {{ labels('admin_labels.select_city', 'Select City') }}</option>
                                                    @foreach ($all_cities as $c)

                                                        <option value="{{ $c->id }}"
                                                            {{ $city->id == $c->id ? 'selected' : '' }}>
                                                            {{ $c->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-5 mt-2">
                                                <input type="text" name="city_delivery_charge" class="form-control"
                                                    placeholder="Delivery Charge" value="{{ $city->delivery_charges }}" />
                                            </div>
                                            <div class="col-md-2">
                                                <input data-repeater-delete type="button" class="btn btn-secondary mt-2"
                                                    value="Delete" />
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <input data-repeater-create type="button" class="btn btn-primary mt-2" value="Add" />
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit"
                                class="btn btn-primary submit_button">{{ labels('admin_labels.update_zone', 'Update Zone') }}</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
