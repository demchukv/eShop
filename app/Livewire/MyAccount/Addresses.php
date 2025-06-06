<?php

namespace App\Livewire\MyAccount;

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AddressAutocompleteController;
use App\Models\Address;
use App\Models\Country;
use App\Models\Region;
use App\Models\City;
use App\Models\Zipcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class Addresses extends Component
{
    protected $listeners = ['refreshComponent', 'deleteAddress'];

    public function render(AddressController $addressController)
    {
        $user = Auth::user();
        $res = $this->get_Address($addressController);
        return view('livewire.' . config('constants.theme') . '.my-account.addresses', [
            'user_info' => $user,
            'addresses' => $res
        ])->title("Addresses |");
    }

    public function get_Address($addressController)
    {
        $user = Auth::user();
        $res = $addressController->getAddress($user->id);
        return $res;
    }

    public function add_address(Request $request)
    {
        $user_id = Auth::user()->id ?? "";
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'type' => 'required|in:home,office',
                'mobile' => 'required|digits_between:1,16|numeric',
                'alternate_mobile' => 'nullable|digits_between:1,16|numeric',
                'address' => 'required|string',
                'landmark' => 'required|string',
                'country_id' => 'required|exists:countries,id',
                'region_id' => 'required|exists:regions,id',
                'city_id' => 'required|exists:cities,id',
                'zipcode_id' => 'required|exists:zipcodes,id',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]
        );

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response['error'] = true;
            $response['message'] = $errors;
            return $response;
        }

        // Fetch details
        $country = Country::findOrFail($request->country_id);
        $region = Region::findOrFail($request->region_id);
        $city = City::findOrFail($request->city_id);
        $zipcode = Zipcode::findOrFail($request->zipcode_id);

        // Prepare address data
        $address_data = [
            'user_id' => $user_id,
            'name' => $request->name,
            'type' => $request->type,
            'mobile' => $request->mobile,
            'alternate_mobile' => $request->alternate_mobile,
            'address' => $request->address,
            'landmark' => $request->landmark,
            'country_id' => $request->country_id,
            'country' => $country->name,
            'country_code' => $country->phonecode,
            'region_id' => $request->region_id,
            'state' => $region->name, // Використовуємо state для сумісності з фронтендом
            'city_id' => $request->city_id,
            'city' => $city->name,
            'zipcode_id' => $request->zipcode_id,
            'pincode' => $zipcode->zipcode,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        if (isset($request->address_id)) {
            $address_id = $request->address_id;
            $res = updateDetails($address_data, ['id' => $address_id], 'addresses');
            if (!$res) {
                $response = [
                    'error' => true,
                    'message' => 'Failed to update address. Please try again.'
                ];
                return $response;
            }
            $response = [
                'error' => false,
                'message' => 'Address updated successfully!'
            ];
            return $response;
        } else {
            $address_id = Address::insertGetId($address_data);
            if (!$address_id) {
                $response = [
                    'error' => true,
                    'message' => 'Failed to add address. Please try again.'
                ];
                return $response;
            }
            $response = [
                'error' => false,
                'message' => 'Address added successfully!'
            ];
            return $response;
        }
    }

    public function edit_address(Request $request)
    {
        $addressId = $request->input('address_id');
        $address = Address::findOrFail($addressId);
        return [
            'name' => $address->name,
            'type' => $address->type,
            'mobile' => $address->mobile,
            'alternate_mobile' => $address->alternate_mobile,
            'address' => $address->address,
            'landmark' => $address->landmark,
            'country_id' => $address->country_id,
            'country_name' => $address->country,
            'region_id' => $address->region_id,
            'region_name' => $address->state,
            'city_id' => $address->city_id,
            'city_name' => $address->city,
            'zipcode_id' => $address->zipcode_id,
            'zipcode' => $address->pincode,
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
        ];
    }

    public function deleteAddress($address_id)
    {
        $user = Auth::user();
        $data = [
            'user_id' => $user->id,
            'id' => $address_id,
        ];
        deleteDetails($data, 'addresses');
    }

    public function setDefault($address_id)
    {
        $user = Auth::user();
        $address = Address::where('id', $address_id)->where('user_id', $user->id)->first();
        if ($address) {
            Address::where('user_id', $user->id)->update(['is_default' => 0]);
            updateDetails(['is_default' => '1'], ['id' => $address_id], 'addresses');
        }
    }

    public function refreshComponent()
    {
        $this->dispatch('$refresh');
    }
}
