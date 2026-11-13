<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class ArticleCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoryStats = Category::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
            ')
            ->first();
        $categories = Category::where('is_active', true)
        ->when($request->filled('search'), function ($query) use ($request) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            });
        })
        ->latest()
        ->paginate(12)
        ->withQueryString();
        return view('article.admin.category', compact('categoryStats', 'categories'));
    }

    public function show(Request $request)
    {
        $categoryStats = Category::query()
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive
            ')
            ->first();
        $categories = Category::where('is_active', false)
        ->when($request->filled('search'), function ($query) use ($request) {
            $keyword = $request->search;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('slug', 'like', "%{$keyword}%");
            });
        })
        ->latest()
        ->paginate(12)
        ->withQueryString();
        return view('article.admin.category', compact('categoryStats', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
            ],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $name = $validated['name'];
            $description = $validated['description'] ?? null;

            $category = Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'meta_title' => $name,
                'meta_description' => $description
                    ?: "Explore our {$name} category for the latest articles and updates.",
            ]);

            return redirect()
                ->route('admin.article.category')
                ->with('alert', [
                    'type' => 'success',
                    'title' => 'Category Created!',
                    'message' => "The category '{$name}' has been created successfully.",
                ]);

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with('alert', [
                    'type' => 'error',
                    'title' => 'Something Went Wrong!',
                    'message' => 'Unable to create the category. Please try again.',
                ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function archive(Request $request)
    {
        $validated = $request->validate([
            'archive_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
        ]);

        try {

            $category = Category::findOrFail($validated['archive_id']);

            if (!$category->is_active) {
                return back()->with('alert', [
                    'type' => 'info',
                    'title' => 'Already Archived!',
                    'message' => "The category '{$category->name}' is already archived.",
                ]);
            }

            $category->update([
                'is_active' => false,
            ]);

            return back()->with('alert', [
                'type' => 'warning',
                'title' => 'Category Archived!',
                'message' => "The category '{$category->name}' has been successfully archived.",
            ]);

        } catch (\Throwable $e) {

            report($e);

            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Archive Failed!',
                'message' => 'Unable to archive the category. Please try again.',
            ]);
        }
    }

    public function activate(Request $request)
    {
        $validated = $request->validate([
            'activate_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
        ]);

        try {

            $category = Category::findOrFail($validated['activate_id']);

            if ($category->is_active) {
                return back()->with('alert', [
                    'type' => 'info',
                    'title' => 'Already Activated!',
                    'message' => "The category '{$category->name}' is already activated.",
                ]);
            }

            $category->update([
                'is_active' => true,
            ]);

            return back()->with('alert', [
                'type' => 'warning',
                'title' => 'Category Restored!',
                'message' => "The category '{$category->name}' has been successfully restored.",
            ]);

        } catch (\Throwable $e) {

            report($e);

            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Activation Failed!',
                'message' => 'Unable to activate the category. Please try again.',
            ]);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'edit_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
            'edit_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')
                    ->ignore($request->edit_id),
            ],
            'edit_description' => [
                'nullable',
                'string',
            ],
        ]);

        try {

            $category = Category::findOrFail($validated['edit_id']);

            $name = $validated['edit_name'];
            $description = $validated['edit_description'] ?? null;

            $category->update([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $description,
                'meta_title' => $name,
                'meta_description' => $description
                    ?: "Explore our {$name} category for the latest articles and updates.",
            ]);

            return redirect()
                ->route('admin.article.category')
                ->with('alert', [
                    'type' => 'success',
                    'title' => 'Category Updated!',
                    'message' => "The category '{$name}' has been updated successfully.",
                ]);

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->with('alert', [
                    'type' => 'error',
                    'title' => 'Update Failed!',
                    'message' => 'Unable to update the category. Please try again.',
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validated = $request->validate([
            'deletion_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],
        ]);

        try {

            $category = Category::findOrFail($validated['deletion_id']);

            $name = $category->name;

            $category->delete();

            return back()->with('alert', [
                'type' => 'success',
                'title' => 'Category Deleted!',
                'message' => "The category '{$name}' has been successfully deleted.",
            ]);

        } catch (\Throwable $e) {

            report($e);

            return back()->with('alert', [
                'type' => 'error',
                'title' => 'Deletion Failed!',
                'message' => 'Unable to delete the category. Please try again.',
            ]);
        }
    }
}
