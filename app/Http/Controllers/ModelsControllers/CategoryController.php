<?php

namespace App\Http\Controllers\ModelsControllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'categories' => $categories, ]);
        $categories = Category::orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return response()->view('dashboard.categories.index', ['categories' => $categories]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->view('dashboard.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {
        $roles = [
            'image_path' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
            'status' => ['nullable', 'numeric'],
            'order' => ['nullable', 'numeric'],
        ];

        foreach (Config::get('languages') as $lang => $language) :
            $roles[$lang . '_name'] = ['required', 'string', 'unique:categories,' . $lang . '_name'];
            $roles[$lang . '_description'] = ['nullable', 'string', 'unique:categories,' . $lang . '_description'];
        endforeach;

        $category_en_name = $request->post('en_name');
        $category_en_description = $request->post('en_description');
        $category_ar_name = $request->post('ar_name');
        $category_ar_description = $request->post('ar_description');

        if ($request->post('order')) :
            $category_order = (int)$request->post('order');
        else :
            $category_order = 0;
        endif;

        if ($request->post('status')) :
            $category_status = (int)$request->input('status');
        else :
            $category_status = 0;
        endif;

        $image_path = '';
        $new_category_name = str_replace(' ', '_', $category_en_name);

        if ($request->hasFile('image_path')) :
            $file_name = $request->file('image_path')->getClientOriginalName(); // file name with extension
            $request_file_path = $new_category_name . '/' . $file_name;

            if (
                Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($new_category_name))
            ) :
                $image_path = $request_file_path;
            else :

                $image_path = $request->file('image_path')->storeAs(
                    $new_category_name,
                    $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        endif;

        /* Create Category Model */
        $category = Category::create([
            'en_name' => $category_en_name,
            'ar_name' => $category_ar_name,
            'slug' => $category_en_name,
            'en_description' => $category_en_description,
            'ar_description' => $category_ar_description,
            'status' => $category_status,
            'order' => $category_order,
        ]);

        /* Attach the Image to the Current Category */
        $category->image()->create(
            [
                'en_title' => $category_en_name,
                'file_path' => $image_path,
            ]
        );

        $alert_status = 'alert-success';
        // Msg
        $msg = 'New Category Added Successfully.';
        // Pref
        $pref = "You Add $category_en_name As New Category To The System!<br>Her ID : $category->id ,Her Description : $category_en_description ,Her Order Queue : $category_order . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->back()->with('status', $status);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): Response
    {
        $category = Category::findOrfail($id);

        $posts = $category->products->get();
        $count_posts = count($posts);

        if (empty($posts) || $count_posts === 0) :
            $posts = 'no service type to this service';
        endif;
        return response()->view('dashboard.categories.show', [
            'category' => $category,
            'posts' => $posts,
            'count_posts' => $count_posts,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id): Response
    {
        $category = Category::findOrfail($id);
        return response()->view('dashboard.categories.edit', [
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCategoryRequest  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrfail($id);

        $roles = [
            'image_path' => ['nullable', 'image', 'mimes:jpg,bmp,png'],
            'status' => ['nullable'],
            'order' => ['nullable', 'numeric'],
        ];

        foreach (Config::get('languages') as $lang => $language) :
            $roles[$lang . '_name'] = [
                'required', 'string',
                Rule::unique('categories')->ignore($category->id)
            ];
            $roles[$lang . '_description'] = [
                'nullable', 'string',
                Rule::unique('categories')->ignore($category->id)
            ];
        endforeach;

        $request->validate($roles);

        $category_en_name_model = $category->en_name;
        //        $category_en_description_model = $category->en_description;
        $category_image_path_model = $category->image_path;
        $category_order_model = $category->order;

        $request_en_name = $request->post('en_name');
        $request_en_description = $request->post('en_description');
        $request_ar_name = $request->post('ar_name');
        $request_ar_description = $request->post('ar_description');

        if ($request->post('status')) :
            $request_status = 1;
        elseif ($request->post('status') === 'on') :
            $request_status = 1;
        else :
            $request_status = 0;
        endif;

        if ($request->post('order') && $request->post('order') !== $category_order_model) :
            $request_order = (int)$request->post('order');
        else :
            $request_order = $category_order_model;
        endif;

        $new_request_name = str_replace(' ', '_', $request_en_name);

        if ($request->hasFile('image_path')) :
            $file_name = $request->file('image_path')->getClientOriginalName(); // file name with extension
            $request_file_path = $new_request_name . '/' . $file_name;
            if ($request_file_path !== $category_image_path_model) :
                $request->validate([
                    'image_path' => [
                        'nullable',
                        'image',
                        'mimes:jpg,bmp,png',
                        //                        Rule::unique('categories')->ignore($category->id)

                    ],
                ]);
            endif;

            if (
                Storage::disk('uploads')->exists($request_file_path) ||
                in_array($request_file_path, Storage::disk('uploads')->allFiles($new_request_name), false)
            ) :
                $request_image_path = $request_file_path;
            else :
                $parent_file_path = substr($category_image_path_model, 0, strrpos($category_image_path_model, '/'));
                if (empty($category_image_path_model) === false) :
                    Storage::disk('uploads')->deleteDirectory($parent_file_path . '/');
                    Storage::disk('uploads')->delete($category_image_path_model . '/');
                endif;
                $request_image_path = $request->file('image_path')->storeAs(
                    $new_request_name,
                    $file_name,
                    [
                        'disk' => 'uploads',
                    ],
                );
            endif;
        else :
            $request_image_path = $category_image_path_model;
        endif;

        /* Update Category Model */
        $category->update([
            'en_name' => $request_en_name,
            'slug' => $request_en_name,
            'ar_name' => $request_ar_name,
            'ar_description' => $request_ar_description,
            'en_description' => $request_en_description,
            'status' => $request_status,
            'order' => $request_order,
        ]);

        /* Attach the Image to the Current Category Model */
        if (is_null($category->image_path)) :
            $category->image()->create(
                [
                    'en_title' => $request_en_name,
                    'file_path' => $request_image_path,
                ]
            );
        endif;

        $category->image()->update(
            [
                'en_title' => $request_en_name,
                'file_path' => $request_image_path,
            ]
        );

        $alert_status = 'alert-success';
        // Msg
        $msg = "Edit Category $category_en_name_model Successfully.";
        // Pref
        $pref = "You Edit $category_en_name_model to $request_en_name Category in The System!<br>Her ID : $id , Her Description : $request_en_description, Her Status : $request_status ,Her Order Queue : $request_order . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.categories.index')->with('status', $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrfail($id);

        $category_en_name = $category->en_name;
        $category_en_description = $category->en_description;
        $category_status = $category->status;
        $category_order = $category->order;

        $category_products = $category->products;

        $category_new_name = str_replace(' ', '_', $category_en_name);
        $category_image_path = $category->image_path;

        //        dd(
        //            $category_products,
        //            $category_products->count(),
        //        );

        $category_image_path_array = explode('/', $category_image_path);
        $base_client_image_path = $category_image_path_array[0] . '/';

        if ($category_products->count() > 1) :
            $deleted_files = $category_products->get()->implode('en_title', ' , ');
            $error_msg = "You can't delete this category because it related to {$category_products->count()} posts.\n Are $deleted_files .";
            abort(403, $error_msg);

        else :
            if (!is_null($category_image_path)) :
                if (
                    Storage::disk('uploads')->exists($category_image_path) ||
                    in_array($category_image_path, Storage::disk('uploads')->allFiles($category_new_name), true)
                ) :
                    Storage::disk('uploads')->deleteDirectory($base_client_image_path);
                    Storage::disk('uploads')->delete($category_image_path);
                endif;

                $category->image->Delete();

            endif;
        endif;

        $category->forceDelete();

        // Status for Deleting This Category from The System!
        $alert_status = 'alert-warning';
        // Msg
        $msg = "$category_en_name Category deleted with all Category types successfully.";
        // Pref
        $pref = "You Delete $category_en_name Category from The System!<br>Her ID : $id , <br>, Her Description : $category_en_description, His Status : $category_status ,Her Order Queue : $category_order . ";
        $status = ['alert_status' => $alert_status, 'msg' => $msg, 'pref' => $pref];

        return redirect()->route('dashboard.categories.index')->with('status', $status);
    }
}
