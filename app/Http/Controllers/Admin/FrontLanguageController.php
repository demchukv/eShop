<?php

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\directoryExists;

class FrontLanguageController extends Controller
{
    public function index()
    {
        $languages = Language::all();

        $current_language_code = session()->get('locale');

        $current_language = fetchdetails('languages', ['code' => $current_language_code], 'language');
        return view('admin.pages.forms.web_language', compact('languages', 'current_language_code', 'current_language'));
    }
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'language' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($request->ajax()) {
                return response()->json(['errors' => $errors->all()], 422);
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        // Create or retrieve the language
        $language = Language::firstOrCreate([
            'language' => strtolower($request->language),
            'code' => strtolower($request->code),
            'is_rtl' => isset($request->is_rtl) && $request->is_rtl == "on" ? 1 : 0,
        ]);

        // Return the response
        if ($request->ajax()) {
            return response()->json([
                'message' => labels('admin_labels.language_added_successfully', 'Language Added Successfully')
            ]);
        }
    }


    public function change(Request $request)
    {

        $request->validate([
            'lang' => 'required|string|max:255',
        ]);


        $is_rtl = fetchdetails('languages', ['code' => $request->lang], 'is_rtl');
        $is_rtl = isset($is_rtl) && !empty($is_rtl) ? $is_rtl[0]->is_rtl : '';

        app()->setLocale($request->lang);

        session()->put('locale', $request->lang);
        session()->put('is_rtl', $is_rtl);

        return redirect()->back();
    }


    public function savelabel(Request $request, Language $lang)
    {
        $data = $request->except(["_token", "_method"]);

        $langstr = '';

        foreach ($data as $key => $value) {
            $label_data = strip_tags($value);
            $label_key = $key;
            $langstr .= "'" . $label_key . "' => '$label_data'," . "\n";
        }

        $langstr_final = "<?php return [" . "\n\n\n" . $langstr . "];";

        $root = base_path("/resources/lang");
        $dir = $root . '/' . $request->langcode;

        if (!File::isDirectory($dir)) {
            // Create the directory if it doesn't exist
            File::makeDirectory($dir, 0755, true);
        }

        $filename = $dir . '/front_messages.php';

        // Save the file
        file_put_contents($filename, $langstr_final);

        return response()->json([
            'error' => false, 'message' => labels('admin_labels.language_labels_added_successfully', 'Language labels added successfully')
        ]);
    }

    public function setLanguage($locale)
    {
        config(['app.locale' => $locale]);
        session()->put('locale', $locale);

        return redirect()->back();
    }



    public function delete($id)
    {
        $l =  Language::findOrFail($id);
        $code = $l->code;
        $folderPath = app()->basePath("lang/$code");

        if (File::isDirectory($folderPath)) {
            // The folder exists in the lang folder
            File::deleteDirectory($folderPath);
        }

        $language =  language::findOrFail($id)->delete();

        if ($language) {
            return response()->json([
                'error' => false, 'message' => labels('admin_labels.language_deleted_successfully', 'Language Deleted Successfully')
            ]);
        } else {
            return response()->json([
                'error' => false, 'message' => labels('admin_labels.error_occurred_while_deleting_language', 'Error occurred while deleting language')
            ]);
        }
    }

    function list()
    {
        DB::enableQueryLog();
        $search = trim(request('search'));
        $sort = (request('sort')) ? request('sort') : "id";
        $order = (request('order')) ? request('order') : "DESC";
        $limit = (request('limit')) ? request('limit') : 5;
        $pageNumber = request('offset') / $limit + 1;


        $languages = language::query()->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('id', 'like', '%' . $search . '%')
                ->orWhere('code', 'like', '%' . $search . '%');
        })
            ->orderBy($sort, $order)
            ->paginate($limit, ['*'], 'page', $pageNumber);

        $languages->transform(function ($item) {
            $item['delete'] = $item['code'] == 'en' ?
                "" :
                '<button type="button" class="btn btn-danger btn-sm delete-button" data-id="' . $item['id'] . '">
                    <i class="fa fa-trash"> </i>
                </button>';
            return $item;
        });


        return response()->json([
            "rows" => $languages->items(),
            'total' => $languages->total()
        ]);
    }
}
