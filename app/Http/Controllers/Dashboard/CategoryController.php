<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use function PHPUnit\Framework\isNan;
use function PHPUnit\Framework\isNull;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if (!Gate::allows('categories.view')) {
        if (Gate::denies('categories.view')) {
            // here possible using any way in error status.
            abort(403);
        }

        // $categories = Category::leftJoin('categories as parents', 'parents.id', '=', 'categories.parent_id')
        //     ->select([
        //         'categories.*',
        //         'parents.name as parent_name'
        //     ])
        $categories = Category::with('parent')
            // ->latest()
            // this according to latest added.
            // ->latest('name')
            // orderBy() arranging according to name just.
            // ->orderBy('categories.name'

            // This's to return the count of products for each category
            ->select('categories.*')
            // ->selectRaw("(SELECT COUNT(*) FROM products WHERE status = 'active' AND category_id = categories.id) as products_count")
            // other way mean the other using for select
            // ->addSelect(DB::raw('(SELECT COUNT(*) FROM products WHERE category_id = categories.id) as products_count'))
            // other way to return the count of products for each category
            // ->withCount('products as products_count') // the automatically be the name of column products_count , products this be the name of relation
            ->withCount([
                'products as products_count' => function ($query) {
                    $query->where('status', '=', 'active');
                }
            ])
            ->filter(request()->query())
            ->paginate();
        return view('dashboard.categories.index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // this automatic if was not allow will return 403 page (forbidden).
        Gate::authorize('categories.create');

        $parents = Category::all();
        $category = new Category();
        return view('dashboard.categories.create', compact('category', 'parents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        // $request->validate(Category::rules());

        // Request Merge
        $request->merge([
            'slug' => Str::slug($request->post('name')),
        ]);

        $data = $request->except('image');

        $data['image'] = $this->uploadImage($request);

        // Mass assignment
        $category = Category::create($data);

        //(PRG) Post Redirect Get
        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Category added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('dashboard.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parents = Category::where('id', '<>', $id)
            ->where(function ($query) use ($id) {
                $query->whereNull('parent_id')
                    ->orWhere('parent_id', '<>', $id);
            })->get();


        return view('dashboard.categories.edit', [
            'category' => $category,
            'parents' => $parents,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, $id)
    {
        // $request->validate(Category::rules());

        $category = Category::findOrFail($id);

        $old_image = $request->image;

        $data = $request->except('image');

        $new_image = $this->uploadImage($request);

        if ($new_image) {
            $data['image'] = $new_image;
        }

        $category->update($data);

        // other way to check the image in $data
        // if ($old_image && $data['image']) {
        // if ($old_image && !isNull($data['image'])) {
        if ($old_image && $new_image) {
            Storage::disk('public')->delete($old_image);
        }

        // other way
        // $category->fill($request->all())->save();


        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        //this way even delete the category will return the information of category to delete the image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
        // another ways to delete the category
        // here be for each model the own primary key.
        // here destroy don;t return the information of category
        // Category::destroy($category->id);
        // Category::where('id', '=', $category->id)->delete();

        return redirect()->route('dashboard.categories.index')
            ->with('success', 'Category deleted successfully');
    }

    protected function uploadImage(Request $request)
    {

        if (!$request->hasFile('image')) {
            return;
        }

        $file = $request->file('image'); // return uploadedFile object
        // here will create the uploads folder in public in storage folder
        // I use this way if I need name each file
        // $path = $file->storeAs('uploads', nameFile, [
        $path = $file->store('uploads', [
            'disk' => 'public',
        ]);

        return $path;
    }

    public function trash()
    {
        $categories_trashed = Category::onlyTrashed()->paginate();
        return view('dashboard.categories.trash', [
            'categories' => $categories_trashed,
        ]);
    }

    public function restore(Request $request, $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return redirect()->route('dashboard.categories.trash')
            ->with('success', 'Category restored successfully');
    }

    public function forceDelete($id)
    {
        $category = Category::findOrFail($id);
        $category->forceDelete();

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        return redirect()->route('dashboard.categories.trash')
            ->with('success', 'Category delete for ever successfully');
    }
}
