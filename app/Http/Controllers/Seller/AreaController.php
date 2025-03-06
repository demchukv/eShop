<?php

namespace App\Http\Controllers\Seller;

use App\Models\Area;
use App\Models\City;
use App\Models\Zone;
use App\Models\Setting;
use App\Models\Zipcode;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AreaController extends Controller
{

    // zipcode

    public function zipcodes()
    {
        return view('seller.pages.tables.zipcodes');
    }


    public function zipcode_list()
    {
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : "0";
        $limit = (request('limit')) ? request('limit') : "10";
        $multipleWhere = [
            'zipcodes.id' => $search,
            'zipcodes.zipcode' => $search,
            'zipcodes.minimum_free_delivery_order_amount' => $search,
            'zipcodes.delivery_charges' => $search,
            'cities.name' => $search,
            'cities.id' => $search,

        ];


        $query = Zipcode::query();

        $query->select('zipcodes.*', 'cities.name as city_name', 'cities.id as city_id')
            ->leftJoin('cities', 'zipcodes.city_id', '=', 'cities.id');

        $query->where(function ($query) use ($multipleWhere) {
            foreach ($multipleWhere as $column => $value) {
                $query->orWhere($column, 'like', '%' . $value . '%');
            }
        });

        if (isset($where) && !empty($where)) {
            $query->where($where);
        }
        $total = $query->count();


        $search_query = DB::table('zipcodes');

        if (!Schema::hasColumn('zipcodes', 'city_id')) {
            $search_query->select('*');
        } else {
            $search_query->select('zipcodes.*', 'cities.name as city_name', 'cities.id as city_id')
                ->leftJoin('cities', 'zipcodes.city_id', '=', 'cities.id');
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $search_query->where(function ($search_query) use ($multipleWhere) {
                foreach ($multipleWhere as $column => $value) {
                    $search_query->orWhere($column, 'like', '%' . $value . '%');
                }
            });
        }

        if (isset($where) && !empty($where)) {
            $search_query->where($where);
        }

        $result = $search_query->orderBy($sort, 'asc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->toArray();

        if (!empty($result)) {

            foreach ($result as $row) {

                $tempRow['id'] = $row->id;
                $tempRow['zipcode'] = $row->zipcode;
                if (!Schema::hasColumn('zipcodes', 'city_id')) {
                    $tempRow['city_name'] = '';
                    $tempRow['city_id'] = '';
                    $tempRow['minimum_free_delivery_order_amount'] = 0;
                    $tempRow['delivery_charges'] = 0;
                } else {
                    $tempRow['city_name'] = $row->city_name ?? '';
                    $tempRow['city_id'] = $row->city_id ?? '';
                    $tempRow['minimum_free_delivery_order_amount'] = $row->minimum_free_delivery_order_amount ?? 0;
                    $tempRow['delivery_charges'] = $row->delivery_charges ?? 0;
                }

                $rows[] = $tempRow;
            }
            return response()->json([
                "rows" => $rows,
                "total" => $total,
            ]);
        }
    }


    // city

    public function city()
    {
        return view('seller.pages.tables.city');
    }




    public function city_list(Request $request)
    {
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : "0";
        $limit = (request('limit')) ? request('limit') : "10";

        $city_data = City::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });

        $total = $city_data->count();

        // Use Paginator to handle the server-side pagination
        $cities = $city_data->orderBy($sort, $order)->offset($offset)
            ->limit($limit)
            ->get();

        // Prepare the data for the "Actions" field
        $data = $cities->map(function ($c) {
            return [
                'id' => $c->id ?? '',
                'name' => $c->name ?? '',
                'minimum_free_delivery_order_amount' => $c->minimum_free_delivery_order_amount ?? '',
                'delivery_charges' => $c->delivery_charges ?? '',
            ];
        });

        return response()->json([
            "rows" => $data, // Return the formatted data for the "Actions" field
            "total" => $total,
        ]);
    }

    // area

    public function area()
    {
        return view('seller.pages.tables.areas');
    }

    // area

    public function area_list(Request $request)
    {
        $search = trim($request->input('search'));
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'DESC');
        // $offset = $request->input('offset', 0);
        $offset = $search || (request('pagination_offset')) ? (request('pagination_offset')) : 0;
        $limit = $request->input('limit', 10);

        $area_data = Area::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%');
        });

        $total = $area_data->count();

        // Use Paginator to handle the server-side pagination
        $areas = $area_data->with(['city', 'zipcode'])
            ->orderBy($sort, $order)
            ->offset($offset)
            ->limit($limit)
            ->get();

        // Prepare the data for the "Actions" field
        $data = $areas->map(function ($a) {

            return [
                'id' => $a->id,
                'name' => $a->name,
                'minimum_free_delivery_order_amount' => $a->minimum_free_delivery_order_amount,
                'delivery_charges' => $a->delivery_charges,
                'city_name' => $a->city->name, // Get the city name using the relationship
                'zipcode' => $a->zipcode->zipcode, // Get the zipcode using the relationship
            ];
        });

        return response()->json([
            "rows" => $data, // Return the formatted data for the "Actions" field
            "total" => $total,
        ]);
    }



    public function get_cities(Request $request)
    {
        $search = trim($request->search) ?? "";
        $cities = City::where('name', 'like', '%' . $search . '%')->get();

        $data = array();
        foreach ($cities as $city) {
            $data[] = array("id" => $city->id, "text" => $city->name);
        }
        return response()->json($data);
    }

    public function get_zipcodes(Request $request)
    {
        $search = trim($request->search) ?? "";
        $zipcodes = Zipcode::where('zipcode', 'like', '%' . $search . '%')->get();

        $data = array();
        foreach ($zipcodes as $zipcode) {
            $data[] = array("id" => $zipcode->id, "text" => $zipcode->zipcode);
        }
        return response()->json($data);
    }

    public function getCities(Request $request)
    {
        $search = trim($request->search) ?? "";
        $cities = City::where('name', 'like', '%' . $search . '%')->get();

        $data = array();
        foreach ($cities as $city) {
            $data[] = array("id" => $city->id, "text" => $city->name);
        }
        return response()->json($data);
    }

    public function zone_data(Request $request)
    {

        $search = trim($request->input('search'));

        $limit = (int) $request->input('limit', 50);

        $query = Zone::where('status', 1)
            ->where(function ($query) use ($search) {
                $query->where('id', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });


        $zones = $query->limit($limit)->get(['id', 'name', 'serviceable_city_ids', 'serviceable_zipcode_ids']);
        $total = $query->count();

        $cities = [];
        $zipcodes = [];

        foreach ($zones as $zone) {
            $city_ids = explode(',', $zone->serviceable_city_ids);
            $zipcode_ids = explode(',', $zone->serviceable_zipcode_ids);

            $cities = array_unique(array_merge($cities, $city_ids));
            $zipcodes = array_unique(array_merge($zipcodes, $zipcode_ids));
        }

        $city_names = City::whereIn('id', $cities)->pluck('name', 'id')->toArray();

        $zipcode_names = Zipcode::whereIn('id', $zipcodes)->pluck('zipcode', 'id')->toArray();

        $response = [
            'total' => $total,
            'results' => $zones->map(function ($zone) use ($city_names, $zipcode_names) {
                $city_ids = explode(',', $zone->serviceable_city_ids);
                $zipcode_ids = explode(',', $zone->serviceable_zipcode_ids);

                return [
                    'id' => $zone->id,
                    'text' => $zone->name,
                    'serviceable_cities' => implode(', ', array_map(function ($city_id) use ($city_names) {
                        return $city_names[$city_id] ?? null;
                    }, $city_ids)),
                    'serviceable_zipcodes' => implode(', ', array_map(function ($zipcode_id) use ($zipcode_names) {
                        return $zipcode_names[$zipcode_id] ?? null;
                    }, $zipcode_ids)),
                ];
            }),
        ];

        return response()->json($response);
    }
}
