<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trash Images</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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
            position: relative;
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
        .img-checkbox {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 20px;
            height: 20px;
            z-index: 10;
            cursor: pointer;
        }
        .bulk-actions-bar {
            display: none;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="container py-5">

        <h3 class="text-center mb-4">
            <i class="fa fa-trash text-danger"></i> Trash Images
        </h3>

        <div class="text-center mb-4">
            <a href="{{ route('gallery.index') }}" class="btn btn-secondary me-2">
                <i class="fa fa-arrow-left"></i> Back to Gallery
            </a>
            @if($images->count() > 0)
                <div class="form-check d-inline-block ms-3">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">Select All</label>
                </div>
            @endif
        </div>

        <div class="bulk-actions-bar text-center" id="bulkBar">
            <span class="me-3 fw-bold"><span id="selectedCount">0</span> Images Selected</span>
            <button onclick="bulkRestore()" class="btn btn-success btn-sm btn-custom px-4 me-2">
                <i class="fa fa-rotate-left"></i> Restore Selected
            </button>
            <button onclick="bulkDelete()" class="btn btn-danger btn-sm btn-custom px-4">
                <i class="fa fa-trash"></i> Delete Selected Permanently
            </button>
        </div>

        <form id="bulkForm" method="POST" class="d-none">
            @csrf
            <div id="bulkIdsContainer"></div>
        </form>

        <div class="row justify-content-center">
            @forelse($images as $img)
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card p-3 text-center">
                        <input type="checkbox" class="img-checkbox image-select" value="{{ $img->id }}">
                        
                        <img src="{{ asset('images/' . $img->filename) }}" class="img-fluid rounded mb-3"
                            style="height:150px; object-fit:cover; width:100%;">

                        <div class="title-text mb-3">
                            {{ $img->title }}
                        </div>

                        <form action="{{ route('gallery.restore', $img->id) }}" method="POST" class="mb-2">
                            @csrf
                            <button class="btn btn-success btn-sm btn-custom w-100">
                                <i class="fa fa-rotate-left"></i> Restore
                            </button>
                        </form>

                        <form action="{{ route('gallery.forceDelete', $img->id) }}" method="POST"
                            onsubmit="return confirmDelete(event, this)">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm btn-custom w-100">
                                <i class="fa fa-trash"></i> Delete Permanently
                            </button>
                        </form>
                    </div>
                </div>
            @empty
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

    <script>
        $(document).ready(function() {
            $('#selectAll').on('click', function() {
                $('.image-select').prop('checked', this.checked);
                toggleBulkBar();
            });

            $('.image-select').on('change', function() {
                if ($('.image-select:checked').length == $('.image-select').length) {
                    $('#selectAll').prop('checked', true);
                } else {
                    $('#selectAll').prop('checked', false);
                }
                toggleBulkBar();
            });
        });

        function toggleBulkBar() {
            let count = $('.image-select:checked').length;
            if (count > 0) {
                $('#bulkBar').fadeIn();
                $('#selectedCount').text(count);
            } else {
                $('#bulkBar').fadeOut();
            }
        }

        function confirmDelete(e, form) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This image will be gone forever!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        function bulkRestore() {
            let ids = [];
            $('.image-select:checked').each(function() {
                ids.push($(this).val());
            });

            let form = $('#bulkForm');
            form.attr('action', "{{ route('gallery.bulkRestore') }}");
            
            $('#bulkIdsContainer').empty();
            ids.forEach(id => {
                $('#bulkIdsContainer').append(`<input type="hidden" name="ids[]" value="${id}">`);
            });
            
            form.submit();
        }

        function bulkDelete() {
            Swal.fire({
                title: 'Delete Selected?',
                text: "All selected images will be permanently removed!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let ids = [];
                    $('.image-select:checked').each(function() {
                        ids.push($(this).val());
                    });

                    let form = $('#bulkForm');
                    form.attr('action', "{{ route('gallery.bulkForceDelete') }}");
                    $('#bulkIdsContainer').empty();
                    $('#bulkIdsContainer').append('<input type="hidden" name="_method" value="DELETE">');
                    
                    ids.forEach(id => {
                        $('#bulkIdsContainer').append(`<input type="hidden" name="ids[]" value="${id}">`);
                    });
                    
                    form.submit();
                }
            });
        }

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