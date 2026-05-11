<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload New Image</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .upload-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 600px;
            margin: 50px auto;
        }
        .dropzone {
            border: 2px dashed #0d6efd !important;
            border-radius: 15px;
            background: #f8f9fa !important;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }
        .dropzone:hover {
            background: #eef2f7 !important;
        }
        .dz-message {
            font-weight: 500;
            color: #6c757d;
        }
        .dz-preview .dz-image img {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .btn-upload {
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="upload-card">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4><i class="fa fa-cloud-upload-alt text-primary"></i> New Gallery Item</h4>
                <a href="{{ route('gallery.index') }}" class="btn btn-light btn-sm text-muted">
                    <i class="fa fa-times"></i>
                </a>
            </div>

            <form action="{{ route('gallery.store') }}" method="POST" enctype="multipart/form-data" id="imageUploadForm">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-bold">Gallery Title</label>
                    <input type="text" name="title" id="imageTitle" class="form-control form-control-lg" placeholder="Enter image title..." required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Upload Images</label>
                    <div id="myDropzone" class="dropzone">
                        <div class="dz-message">
                            <i class="fa fa-images fa-3x mb-3 text-primary"></i><br>
                            Drag & Drop your images here or click to upload
                        </div>
                    </div>
                </div>

                <button type="button" id="submitAll" class="btn btn-primary w-100 btn-upload">
                    Start Uploading
                </button>
            </form>

        </div>
    </div>

    <script>
        Dropzone.autoDiscover = false;

        $(document).ready(function() {
            const myDropzone = new Dropzone("#myDropzone", {
                url: "{{ route('gallery.store') }}",
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 10,
                maxFiles: 10,
                paramName: "file",
                acceptedFiles: 'image/*',
                addRemoveLinks: true,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function() {
                    let dz = this;

                    $("#submitAll").click(function(e) {
                        e.preventDefault();
                        const title = $("#imageTitle").val();

                        if (!title) {
                            Swal.fire('Error', 'Please enter a title', 'error');
                            return;
                        }

                        if (dz.getQueuedFiles().length === 0) {
                            Swal.fire('Error', 'Please select at least one image', 'error');
                            return;
                        }

                        dz.processQueue();
                    });

                    this.on("sendingmultiple", function(data, xhr, formData) {
                        formData.append("title", $("#imageTitle").val());
                    });

                    this.on("successmultiple", function(files, response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Uploaded!',
                            text: 'Images added successfully.',
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.href = "{{ route('gallery.index') }}";
                        });
                    });

                    this.on("errormultiple", function(files, response) {
                        Swal.fire('Upload Failed', 'Check file size or format', 'error');
                    });
                }
            });
        });
    </script>

</body>

</html>