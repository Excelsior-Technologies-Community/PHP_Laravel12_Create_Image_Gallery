# PHP_Laravel12_Create_Image_Gallery


A beginner-friendly **Laravel 12 Image Gallery CRUD project** with image upload, display, and delete functionality.


## Project Overview

The **Image Gallery project** allows users to upload images with a title, view all uploaded images in a gallery, and delete images when needed.

### How it works:
1. User uploads an image using a form
2. Laravel validates the input
3. The image is saved in the `public/images` folder
4. Image title and filename are stored in the database
5. Images are displayed on the gallery page
6. Users can delete images with confirmation

---

##  Features
- Image Upload with Validation
- Display Image Gallery
- Delete Image with Confirmation
- Uses Bootstrap 5 UI
- Beginner Friendly Code Structure

---

##  Requirements
- PHP >= 8.2
- Composer
- XAMPP / Apache
- MySQL
- Laravel 12

---

###  Installation Steps

## Step 1: Create Laravel Project

```bash
composer create-project laravel/laravel PHP_Laravel12_Create_Image_Gallery "12.*"
cd PHP_Laravel12_Create_Image_Gallery
```

## Step 2: Configure `.env`

Set database details:

```env
DB_DATABASE=create_image_gallery
DB_USERNAME=root
DB_PASSWORD=
```

Create database using this command:

```bash
php artisan migrate
```

---

## Step 3: Create Migration & Model

Generate Migration & Model:

```bash
php artisan make:model Image -m
```

This creates:

app/Models/Image.php – Model

Migration file inside database/migrations

### Migartion table

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('filename');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
```

Run migration:
```bash
php artisan migrate
```


### Model

File: app/Models/Image.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'title',
        'filename',
    ];
}
```

---

## Step 4: Controller

```bash
php artisan make:controller ImageController
```

```php
<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display all images (INDEX)
     */
    public function index()
    {
        // Soft deleted images are automatically excluded
        $images = Image::latest()->get();

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
        // Step 1: Validate input
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Step 2: Get uploaded file
        $file = $request->file('image');

        // Step 3: Create unique image name
        $filename = time() . '_' . $file->getClientOriginalName();

        // Step 4: Move image to public/images
        $file->move(public_path('images'), $filename);

        // Step 5: Save image info to database
        Image::create([
            'title'    => $request->title,
            'filename' => $filename,
        ]);

        // Step 6: Redirect back with success message
        return redirect()->route('gallery.index')
            ->with('success', 'Image uploaded successfully');
    }

    /**
     * Soft delete image
     */
    public function destroy($id)
    {
        // Step 1: Find image
        $image = Image::findOrFail($id);

        // Step 2: Delete image file from folder
        $imagePath = public_path('images/' . $image->filename);

        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Step 3: Soft delete database record
        $image->delete();

        // Step 4: Redirect back with success message
        return back()->with('success', 'Image deleted successfully');
    }
}
```

---

## Step 5: Routes

File: `routes/web.php`

```php
<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;

/*
|--------------------------------------------------------------------------
| Image Gallery Routes
|--------------------------------------------------------------------------
*/

// Gallery routes
Route::get('/gallery', [ImageController::class, 'index'])->name('gallery.index');
Route::get('/gallery/create', [ImageController::class, 'create'])->name('gallery.create');
Route::post('/gallery', [ImageController::class, 'store'])->name('gallery.store');
Route::delete('/gallery/{id}', [ImageController::class, 'destroy'])->name('gallery.destroy');
```

---

## Step 6: Blade Views

### `resources/views/gallery/create.blade.php`

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2 class="text-center mb-4">Upload New Image</h2>

    <a href="{{ route('gallery.index') }}" class="btn btn-secondary mb-3">
        Back to Gallery
    </a>

    <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Image Title</label>
            <input type="text" name="title" class="form-control">
            @error('title')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label>Upload Image</label>
            <input type="file" name="image" class="form-control">
            @error('image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button class="btn btn-primary">
            Upload Image
        </button>
    </form>

</div>

</body>
</html>
```

### `resources/views/gallery/index.blade.php`
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <h2 class="text-center mb-4">Laravel 12 Image Gallery</h2>

    <a href="{{ route('gallery.create') }}" class="btn btn-success mb-3">
        + Add New Image
    </a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        @foreach($images as $img)
            <div class="col-md-3 mb-4 text-center">
                <img src="{{ asset('images/'.$img->filename) }}"
                     class="img-fluid rounded mb-2"
                     style="height:180px; object-fit:cover;">

                <p class="fw-bold">{{ $img->title }}</p>

                <form action="{{ route('gallery.destroy', $img->id) }}" method="POST"    onsubmit="return confirm('Are you sure you want to delete this image?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">
                        Delete
                    </button>
                </form>
            </div>
        @endforeach
    </div>

</div>

</body>
</html>
@endforeach
```

---

## Step 7: Run Project

```bash
php artisan serve
```

Open:
```
http://127.0.0.1:8000/gallery
```

---


## Project Structure

```
PHP_Laravel12_Create_Image_Gallery/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── ImageController.php
│   └── Models/
│       └── Image.php
├── database/
│   └── migrations/
│       └── 2026_01_07_000000_create_images_table.php
├── public/
│   └── images/   
├── resources/
│   └── views/
│       └── gallery/
│           ├── index.blade.php
│           └── create.blade.php
├── routes/
│   └── web.php
│
├── .env
└── composer.json
```

---

## Output

**Upload New Image**

<img width="1919" height="1031" alt="Screenshot 2026-01-07 171424" src="https://github.com/user-attachments/assets/46262b9b-9a0b-4e22-a065-d8951c3aee62" />

**Image Index**

<img width="1919" height="1026" alt="Screenshot 2026-01-07 171435" src="https://github.com/user-attachments/assets/a9f73c80-2589-4fa9-9f2c-9f240d2853f0" />

<<<<<<< HEAD
 **Delete Image**

 <img width="1917" height="1038" alt="Screenshot 2026-01-07 171921" src="https://github.com/user-attachments/assets/cc9cb044-3d60-4ce1-b342-89ce4522c154" />

 <img width="1919" height="1030" alt="Screenshot 2026-01-07 171931" src="https://github.com/user-attachments/assets/40ed0a57-b505-41ca-96d6-a6ebe0254e63" />
=======
**Delete Image**

<img width="1917" height="1038" alt="Screenshot 2026-01-07 171921" src="https://github.com/user-attachments/assets/cc9cb044-3d60-4ce1-b342-89ce4522c154" />

<img width="1919" height="1030" alt="Screenshot 2026-01-07 171931" src="https://github.com/user-attachments/assets/40ed0a57-b505-41ca-96d6-a6ebe0254e63" />
>>>>>>> development


---

Your PHP_Laravel12_Create_Image_Gallery Project is Now Ready!
<<<<<<< HEAD

=======
>>>>>>> development
