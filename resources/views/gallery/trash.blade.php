<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trash Images</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(to right, #eef2f3, #ffffff);
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .title-text {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            min-height: 40px;
        }

        .btn-custom {
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            padding: 6px 10px;
        }

        .btn-success {
            background: linear-gradient(45deg, #28a745, #43d17a);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(45deg, #dc3545, #ff6b6b);
            border: none;
        }

        .btn-secondary {
            border-radius: 25px;
            padding: 8px 20px;
        }

        .empty-box {
            text-align: center;
            padding: 50px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        img {
            transition: 0.3s;
        }

        img:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <!-- Title -->
        <h3 class="text-center mb-4">
            <i class="fa fa-trash text-danger"></i> Trash Images
        </h3>

        <!-- Back Button -->
        <div class="text-center mb-4">
            <a href="{{ route('gallery.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Back to Gallery
            </a>
        </div>

        <div class="row justify-content-center">

            @forelse($images as $img)
                <div class="col-md-3 col-sm-6 mb-4">

                    <div class="card p-3 text-center">

                        <!-- IMAGE -->
                        <img src="{{ asset('images/' . $img->filename) }}" class="img-fluid rounded mb-3"
                            style="height:150px; object-fit:cover; width:100%;">

                        <!-- TITLE -->
                        <div class="title-text mb-3">
                            {{ $img->title }}
                        </div>

                        <!-- RESTORE -->
                        <form action="{{ route('gallery.restore', $img->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button class="btn btn-success btn-sm btn-custom w-100">
                                <i class="fa fa-rotate-left"></i> Restore
                            </button>
                        </form>

                        <!-- DELETE -->
                        <form action="{{ route('gallery.forceDelete', $img->id) }}" method="POST"
                            onsubmit="return confirm('Permanently delete this image?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-custom w-100">
                                <i class="fa fa-trash"></i> Delete Permanently
                            </button>
                        </form>

                    </div>

                </div>
            @empty

                <!-- EMPTY STATE -->
                <div class="col-md-6">
                    <div class="empty-box">
                        <i class="fa fa-image fa-3x text-muted mb-3"></i>
                        <h5>No Images in Trash</h5>
                        <p class="text-muted">Deleted images will appear here</p>
                    </div>
                </div>

            @endforelse

        </div>

    </div>

    <!-- 🔥 SUCCESS POPUP -->
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif
    </script>

</body>

</html>