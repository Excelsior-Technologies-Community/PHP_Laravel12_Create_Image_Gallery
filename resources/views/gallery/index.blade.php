<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Image Gallery</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            height: 200px;
            object-fit: cover;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
        }

        .btn-sm {
            font-size: 12px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <h2 class="text-center mb-4">📸 Image Gallery</h2>

        <!-- SEARCH + BUTTONS -->
        <div class="top-bar mb-3">
            <form method="GET" class="d-flex w-50">
                <input type="text" name="search" class="form-control me-2" placeholder="Search image...">
                <button class="btn btn-primary">Search</button>
            </form>

            <div>
                <a href="{{ route('gallery.create') }}" class="btn btn-success">+ Add</a>
                <a href="{{ route('gallery.trash') }}" class="btn btn-dark">Trash</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- CARDS -->
        <div class="row justify-content-center">

            @foreach($images as $img)
                <div class="col-md-3 mb-4">

                    <div class="card">

                        <img src="{{ asset('images/' . $img->filename) }}" class="w-100">

                        <div class="card-body text-center">

                            <h6 class="card-title">{{ $img->title }}</h6>

                            <a href="{{ route('gallery.download', $img->id) }}" class="btn btn-info btn-sm">⬇️</a>

                            <form action="{{ route('gallery.destroy', $img->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure you want to delete this image?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑️</button>
                            </form>

                        </div>

                    </div>

                </div>
            @endforeach

        </div>

        <!-- PAGINATION -->
        <div class="d-flex justify-content-center mt-4">
            {{ $images->links() }}
        </div>

    </div>

</body>

</html>