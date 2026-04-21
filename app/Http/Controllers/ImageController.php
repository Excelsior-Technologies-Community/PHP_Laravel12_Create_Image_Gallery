<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display all images (INDEX + SEARCH + PAGINATION)
     */
    public function index(Request $request)
    {
        $query = Image::query();

        // SEARCH
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // PAGINATION
        $images = $query->latest()->paginate(8);

        return view('gallery.index', compact('images'));
    }

    /**
     * Show create image form
     */
    public function create()
    {
        return view('gallery.create');
    }

    /**
     * Store uploaded image
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();

        $file->move(public_path('images'), $filename);

        Image::create([
            'title' => $request->title,
            'filename' => $filename,
        ]);

        return redirect()->route('gallery.index')
            ->with('success', 'Image uploaded successfully');
    }

    /**
     * Soft delete image
     */
    public function destroy($id)
    {
        $image = Image::findOrFail($id);

        $image->delete();

        return back()->with('success', 'Image moved to trash');
    }

    /**
     * DOWNLOAD IMAGE
     */
    public function download($id)
    {
        $image = Image::findOrFail($id);

        $path = public_path('images/' . $image->filename);

        return response()->download($path);
    }

    /**
     * SHOW TRASH
     */
    public function trash()
    {
        $images = Image::onlyTrashed()->get();

        return view('gallery.trash', compact('images'));
    }

    /**
     * RESTORE IMAGE
     */
    public function restore($id)
    {
        Image::withTrashed()->findOrFail($id)->restore();

        return back()->with('success', 'Image restored successfully');
    }

    /**
     * PERMANENT DELETE
     */
    public function forceDelete($id)
    {
        $image = Image::withTrashed()->findOrFail($id);

        $path = public_path('images/' . $image->filename);

        if (file_exists($path)) {
            unlink($path);
        }

        $image->forceDelete();

        return back()->with('success', 'Image permanently deleted');
    }
}