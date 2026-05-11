<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImageController extends Controller
{
    public function index(Request $request)
    {
        $query = Image::query();

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $images = $query->latest()->paginate(8);

        return view('gallery.index', compact('images'));
    }

    public function create()
    {
        return view('gallery.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'file' => 'required',
                'file.*' => 'image|mimes:jpg,jpeg,png,gif,webp|max:20480',
            ]);

            if ($request->hasFile('file')) {
                $path = public_path('images');
                if (!File::isDirectory($path)) {
                    File::makeDirectory($path, 0777, true, true);
                }

                foreach ($request->file('file') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    
                    $file->move($path, $filename);

                    Image::create([
                        'title' => $request->title,
                        'filename' => $filename,
                    ]);
                }
                return response()->json(['success' => 'Done']);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        if ($request->has('ids')) {
            Image::whereIn('id', $request->ids)->delete();
            return back()->with('success', 'Selected images moved to trash');
        }

        Image::findOrFail($id)->delete();
        return back()->with('success', 'Image moved to trash');
    }

    public function download($id)
    {
        $image = Image::findOrFail($id);
        $path = public_path('images/' . $image->filename);

        if (file_exists($path)) {
            return response()->download($path);
        }

        return back()->with('error', 'File not found');
    }

    public function trash()
    {
        $images = Image::onlyTrashed()->get();
        return view('gallery.trash', compact('images'));
    }

    public function restore($id)
    {
        Image::withTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Image restored successfully');
    }

    public function bulkRestore(Request $request)
    {
        if ($request->ids) {
            Image::withTrashed()->whereIn('id', $request->ids)->restore();
            return back()->with('success', 'Selected images restored');
        }
        return back();
    }

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

    public function bulkForceDelete(Request $request)
    {
        if ($request->ids) {
            $images = Image::withTrashed()->whereIn('id', $request->ids)->get();
            foreach ($images as $img) {
                $path = public_path('images/' . $img->filename);
                if (file_exists($path)) { 
                    unlink($path); 
                }
                $img->forceDelete();
            }
            return back()->with('success', 'Selected images deleted permanently');
        }
        return back();
    }
}