<?php

namespace App\Http\Controllers;

use App\Models\ThumbnailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThumbnailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $thumbnails = ThumbnailTemplate::latest()
        ->paginate(4)
        ->withQueryString();
        return view('article.admin.thumbnail', compact('thumbnails'));
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
        $data = $request->validate([
            'image'           => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:2048'], // KB
            'text_color'      => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_position'   => ['required', 'in:top,center,bottom'],
            'overlay_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'overlay_opacity' => ['nullable', 'integer', 'between:0,100'],
            'is_active'       => ['required', 'boolean'],
        ]);

        $data['image_path'] = $request->file('image')->store('thumbnail-templates', 'public');
        unset($data['image']);

        ThumbnailTemplate::create($data);

        return back()->with('success', 'Thumbnail template saved.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $request->validate(['edit_id' => ['required', 'exists:thumbnail_templates,id']]);

        $template = ThumbnailTemplate::findOrFail($request->edit_id);

        $data = $request->validate([
            'image'           => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
            'text_color'      => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'text_position'   => ['required', 'in:top,center,bottom'],
            'overlay_color'   => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'overlay_opacity' => ['nullable', 'integer', 'between:0,100'],
            'is_active'       => ['required', 'boolean'],
        ]);

        // overlay switched off: the fields are not sent, so clear the color explicitly
        $data['overlay_color'] = $data['overlay_color'] ?? null;
        $data['overlay_opacity'] = $data['overlay_opacity'] ?? $template->overlay_opacity;

        if ($request->hasFile('image')) {
            $newPath = $request->file('image')->store('thumbnail-templates', 'public');
            $oldPath = $template->image_path;

            $data['image_path'] = $newPath;
            $template->update(collect($data)->except('image')->all());

            Storage::disk('public')->delete($oldPath); // delete the old file only after saving
        } else {
            $template->update(collect($data)->except('image')->all());
        }

        return back()->with('success', 'Thumbnail template updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
