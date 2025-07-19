<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Categories') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success message --}}
            @if(session('success'))
                <div class="mb-4 text-green-600 font-semibold">{{ session('success') }}</div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="mb-4 text-red-600">
                    <ul>@foreach ($errors->all() as $error)<li>- {{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            {{-- Add New Category --}}
            <div class="bg-white shadow-sm sm:rounded-lg mb-6 p-6">
                <h3 class="text-lg font-semibold mb-4">Add New Category</h3>

                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf

                    <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                    <input type="text" name="name" id="name" required
                           class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" />

                    <label for="description" class="block font-medium text-sm text-gray-700">Description</label>
                    <textarea name="description" id="description" required
                              class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" rows="10"></textarea>

                    <label for="image" class="block font-medium text-sm text-gray-700 mb-1">Upload & Crop Image</label>
                    <input type="file" accept="image/*" id="imageInput" class="mb-4" />

                    <div style="max-width: 400px; max-height: 400px; margin-bottom: 10px;">
                        <img id="imagePreview" style="max-width: 100%; display: none;" />
                    </div>

                    <input type="hidden" name="cropped_image" id="croppedImageInput" />

                    <button type="button" id="cropButton" class="mb-4 bg-gray-600 text-white px-4 py-2 rounded" style="display:none;">Crop Image</button>

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Category
                    </button>
                </form>
            </div>

            {{-- Categories Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Categories List</h3>

                @if ($categories->isEmpty())
                    <p>No categories found.</p>
                @else
                    <table class="min-w-full border border-gray-300">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Image</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td class="border px-4 py-2">{{ $category->id }}</td>
                                <td class="border px-4 py-2">{{ $category->name }}</td>
                                <td class="border px-4 py-2">
                                    <img src="{{ $category->image }}" alt="{{ $category->name }}" class="h-12 w-12 object-cover rounded" />
                                </td>
                                <td class="border px-4 py-2">
                                    {{-- Edit form toggle --}}
                                    <button onclick="toggleEditForm({{ $category->id }})" class="text-blue-600 hover:underline mr-2">Edit</button>

                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this category?')" class="text-red-600 hover:underline">Delete</button>
                                    </form>

                                    {{-- Edit form hidden by default --}}
                                    <form id="edit-form-{{ $category->id }}" action="{{ route('categories.update', $category->id) }}" method="POST" class="hidden mt-4 border p-4 rounded bg-gray-50" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <label for="edit_name_{{ $category->id }}" class="block font-medium text-sm text-gray-700">Name</label>
                                        <input type="text" name="name" id="edit_name_{{ $category->id }}" value="{{ $category->name }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-2" />

                                        <label for="edit_description_{{ $category->id }}" class="block font-medium text-sm text-gray-700">Description</label>
                                        <textarea  rows="3" name="description" id="edit_description_{{ $category->id }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-2" >{{ $category->description }}</textarea>

                                        <label for="edit_image_{{ $category->id }}" class="block font-medium text-sm text-gray-700 mb-1">Upload & Crop Image</label>
                                        <input type="file" accept="image/*" id="editImageInput{{ $category->id }}" class="mb-4 edit-image-input" data-id="{{ $category->id }}" />

                                        <div style="max-width: 400px; max-height: 400px; margin-bottom: 10px;">
                                            <img id="editImagePreview{{ $category->id }}" src="{{ $category->image }}" style="max-width: 100%;" />
                                        </div>

                                        <input type="hidden" name="cropped_image" id="editCroppedImageInput{{ $category->id }}" />

                                        <button type="button" id="editCropButton{{ $category->id }}" class="mb-4 bg-gray-600 text-white px-4 py-2 rounded edit-crop-button" data-id="{{ $category->id }}" style="display:none;">Crop Image</button>

                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Update</button>
                                        <button type="button" onclick="toggleEditForm({{ $category->id }})" class="ml-2 px-4 py-2 border rounded">Cancel</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>
    </div>

    {{-- Cropper.js CSS & JS CDN --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        // Variables for add form cropper
        let cropper;
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const cropButton = document.getElementById('cropButton');
        const croppedImageInput = document.getElementById('croppedImageInput');

        imageInput.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const url = URL.createObjectURL(file);

                imagePreview.src = url;
                imagePreview.style.display = 'block';

                if (cropper) cropper.destroy();

                cropper = new Cropper(imagePreview, {
                    aspectRatio: 280 / 270,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                });

                cropButton.style.display = 'inline-block';
            }
        });

        cropButton.addEventListener('click', function () {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 280,
                height: 270,
                imageSmoothingQuality: 'high'
            });

            canvas.toBlob(function (blob) {
                const reader = new FileReader();
                reader.readAsDataURL(blob);
                reader.onloadend = function () {
                    croppedImageInput.value = reader.result;
                    imagePreview.src = reader.result;
                    cropper.destroy();
                    cropper = null;
                    cropButton.style.display = 'none';
                };
            }, 'image/jpeg', 0.7);
        });

        // Edit form croppers management
        const editCroppers = {}; // store cropper instances keyed by category ID

        function toggleEditForm(id) {
            const form = document.getElementById('edit-form-' + id);
            if (!form) return;

            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

        // Handle edit image inputs dynamically
        document.querySelectorAll('.edit-image-input').forEach(input => {
            input.addEventListener('change', function(e) {
                const id = e.target.dataset.id;
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    const url = URL.createObjectURL(file);

                    const imgPreview = document.getElementById('editImagePreview' + id);
                    imgPreview.src = url;
                    imgPreview.style.display = 'block';

                    if (editCroppers[id]) {
                        editCroppers[id].destroy();
                    }

                    editCroppers[id] = new Cropper(imgPreview, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                    });

                    const cropBtn = document.getElementById('editCropButton' + id);
                    cropBtn.style.display = 'inline-block';
                }
            });
        });

        // Handle edit crop buttons
        document.querySelectorAll('.edit-crop-button').forEach(button => {
            button.addEventListener('click', function (e) {
                const id = e.target.dataset.id;
                const cropper = editCroppers[id];
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: 'high' });

                canvas.toBlob(function (blob) {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function () {
                        const hiddenInput = document.getElementById('editCroppedImageInput' + id);
                        hiddenInput.value = reader.result;

                        const imgPreview = document.getElementById('editImagePreview' + id);
                        imgPreview.src = reader.result;

                        cropper.destroy();
                        editCroppers[id] = null;

                        e.target.style.display = 'none';
                    };
                }, 'image/jpeg', 0.7);
            });
        });
    </script>
</x-app-layout>
