<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Image Gallery</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card img {
            height: 200px;
            object-fit: cover;
            cursor: pointer;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .img-checkbox {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 20px;
            height: 20px;
            z-index: 5;
            cursor: pointer;
        }

        .bulk-btn-area {
            display: none;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <h2 class="text-center mb-4">📸 Image Gallery</h2>

        <div class="top-bar mb-4">
            <form method="GET" class="d-flex w-50">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search by title...">
                <button class="btn btn-primary px-4">Search</button>
            </form>

            <div class="d-flex align-items-center gap-2">
                <div class="bulk-btn-area" id="bulkActions">
                    <button onclick="confirmBulkDelete()" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Delete (<span id="selectedCount">0</span>)
                    </button>
                </div>
                <div class="form-check me-2">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label fw-bold" for="selectAll">Select All</label>
                </div>
                <a href="{{ route('gallery.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Add</a>
                <a href="{{ route('gallery.trash') }}" class="btn btn-dark"><i class="fa fa-trash-can"></i> Trash</a>
            </div>
        </div>

        <form id="bulkDeleteForm" action="{{ route('gallery.destroy', 0) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
            <div id="bulkIdsContainer"></div>
        </form>

        <div class="row">
            @forelse($images as $img)
                <div class="col-md-3 mb-4">
                    <div class="card">
                        <input type="checkbox" class="img-checkbox image-select" value="{{ $img->id }}">
                        
                        <a href="{{ asset('images/' . $img->filename) }}" class="glightbox" data-gallery="gallery1" data-title="{{ $img->title }}">
                            <img src="{{ asset('images/' . $img->filename) }}" class="w-100" alt="{{ $img->title }}">
                        </a>

                        <div class="card-body text-center">
                            <h6 class="card-title mb-3">{{ $img->title }}</h6>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('gallery.download', $img->id) }}" class="btn btn-outline-info btn-sm">
                                    <i class="fa fa-download"></i>
                                </a>

                                <form action="{{ route('gallery.destroy', $img->id) }}" method="POST" class="d-inline single-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmSingleDelete(this)" class="btn btn-outline-danger btn-sm">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fa fa-images fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No images found in your gallery.</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $images->appends(request()->input())->links() }}
        </div>

    </div>

    <script>
        const lightbox = GLightbox({
            selector: '.glightbox',
            touchNavigation: true,
            loop: true
        });

        $(document).ready(function() {
            $('#selectAll').on('click', function() {
                $('.image-select').prop('checked', this.checked);
                updateBulkUI();
            });

            $('.image-select').on('change', function() {
                if ($('.image-select:checked').length == $('.image-select').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
                updateBulkUI();
            });
        });

        function updateBulkUI() {
            let count = $('.image-select:checked').length;
            if (count > 0) {
                $('#bulkActions').fadeIn();
                $('#selectedCount').text(count);
            } else {
                $('#bulkActions').fadeOut();
            }
        }

        function confirmSingleDelete(btn) {
            Swal.fire({
                title: 'Move to Trash?',
                text: "You can restore this image later from the trash.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(btn).closest('form').submit();
                }
            });
        }

        function confirmBulkDelete() {
            Swal.fire({
                title: 'Delete Selected?',
                text: "Selected images will be moved to trash.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let container = $('#bulkIdsContainer');
                    container.empty();
                    
                    $('.image-select:checked').each(function() {
                        container.append(`<input type="hidden" name="ids[]" value="${$(this).val()}">`);
                    });

                    let form = $('#bulkDeleteForm');
                    form.attr('action', "{{ route('gallery.destroy', 0) }}");
                    form.submit();
                }
            });
        }

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>

</body>

</html>