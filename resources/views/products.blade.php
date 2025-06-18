<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Products') }}</h2>
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

            {{-- Add New Product Button --}}
            <div class="mb-6">
                <button id="open-add-dialog" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Add New Product
                </button>
            </div>

            {{-- Add New Product Dialog --}}
            <dialog id="add-product-dialog" class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
                <h3 class="text-lg font-semibold mb-4">Add New Product</h3>
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Tabs -->
                    <div class="mb-4">
                        <div class="border-b border-gray-200">
                            <nav class="flex space-x-4" aria-label="Tabs">
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent active-tab" data-tab="basic-info">Basic Info</button>
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="images">Images</button>
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="data">Data</button>
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="tags">Tags</button>
                            </nav>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div id="basic-info" class="tab-content">
                        <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required
                               class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" />

                        <label for="description" class="block font-medium text-sm text-gray-700">Description</label>
                        <input type="text" name="description" id="description" required
                               class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" />

                        <label for="category_id" class="block font-medium text-sm text-gray-700">Category</label>
                        <select name="category_id" id="category_id" required
                                class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <label for="best_seller" class="inline-flex items-center mb-4">
                            <input type="checkbox" name="best_seller" id="best_seller" value="1"
                                   class="border-gray-300 rounded shadow-sm">
                            <span class="ml-2 text-sm text-gray-700">Best Seller</span>
                        </label>
                    </div>

                    <div id="images" class="tab-content hidden">
                        <h4 class="font-medium text-sm text-gray-700 mb-2">Images (Max 5, Select Main Image)</h4>
                        <div id="image-container" class="space-y-4">
                            <div class="image-entry">
                                <input type="file" accept="image/*" class="image-input mb-2" />
                                <div style="max-width: 400px; max-height: 400px; margin-bottom: 10px;">
                                    <img class="image-preview" style="max-width: 100%; display: none;" />
                                </div>
                                <input type="hidden" name="cropped_images[]" class="cropped-image-input" />
                                <label class="inline-flex items-center mb-2">
                                    <input type="radio" name="main_image" value="0" class="main-image-radio">
                                    <span class="ml-2 text-sm text-gray-700">Main Image</span>
                                </label>
                                <button type="button" class="crop-button bg-gray-600 text-white px-4 py-2 rounded mb-2" style="display:none;">Crop Image</button>
                                <button type="button" class="remove-image bg-red-600 text-white px-2 py-1 rounded" style="display:none;">Remove</button>
                            </div>
                        </div>
                        <button type="button" id="add-image" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded" disabled>Add Another Image</button>
                        <p id="image-limit-message" class="text-red-600 text-sm mt-2 hidden">Maximum 5 images allowed.</p>
                    </div>

                    <div id="data" class="tab-content hidden">
                        <h4 class="font-medium text-sm text-gray-700 mb-2">Data</h4>
                        <div id="data-container" class="space-y-4">
                            <div class="data-entry flex space-x-4 items-start">
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Name</label>
                                    <input type="text" name="data[0][name]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                                </div>
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Description</label>
                                    <input type="text" name="data[0][description]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                                </div>
                                <button type="button" class="remove-data bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
                            </div>
                        </div>
                        <button type="button" id="add-data" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Add Another Data</button>
                    </div>

                    <div id="tags" class="tab-content hidden">
                        <h4 class="font-medium text-sm text-gray-700 mb-2">Tags</h4>
                        <div id="tag-container" class="space-y-4">
                            <div class="tag-entry flex space-x-4 items-start">
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Name</label>
                                    <input type="text" name="tags[0][name]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                                </div>
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Description</label>
                                    <textarea name="tags[0][description]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" rows="4"></textarea>
                                </div>
                                <button type="button" class="remove-tag bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
                            </div>
                        </div>
                        <button type="button" id="add-tag" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Add Another Tag</button>
                    </div>

                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" id="close-add-dialog" class="px-4 py-2 border rounded">Cancel</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add Product</button>
                    </div>
                </form>
            </dialog>

            {{-- Products Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Products List</h3>

                @if ($products->isEmpty())
                    <p>No products found.</p>
                @else
                    <table class="min-w-full border border-gray-300">
                        <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Description</th>
                            <th class="border px-4 py-2">Category</th>
                            <th class="border px-4 py-2">Best Seller</th>
                            <th class="border px-4 py-2">Main Image</th>
                            <th class="border px-4 py-2">Other Images</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="border px-4 py-2">{{ $product->id }}</td>
                                <td class="border px-4 py-2">{{ $product->name }}</td>
                                <td class="border px-4 py-2">{{ $product->description }}</td>
                                <td class="border px-4 py-2">{{ $product->hasOneCategory->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $product->best_seller ? 'Yes' : 'No' }}</td>
                                <td class="border px-4 py-2">
                                    @php
                                        $mainImage = $product->hasManyImages->where('is_main', true)->first();
                                    @endphp
                                    @if($mainImage)
                                        <img src="{{ Storage::url($mainImage->image) }}" alt="Main Image" class="h-12 w-12 object-cover rounded" />
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="border px-4 py-2">
                                    @foreach($product->hasManyImages->where('is_main', false) as $image)
                                        <img src="{{ Storage::url($image->image) }}" alt="Product Image" class="h-12 w-12 object-cover rounded inline-block mr-2" />
                                    @endforeach
                                </td>
                                <td class="border px-4 py-2">
                                    <button onclick="openEditDialog({{ $product->id }})" class="text-blue-600 hover:underline mr-2">Edit</button>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this product?')" class="text-red-600 hover:underline">Delete</button>
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

    {{-- Edit Product Dialogs --}}
    @foreach ($products as $product)
        <dialog id="edit-product-dialog-{{ $product->id }}" class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl">
            <h3 class="text-lg font-semibold mb-4">Edit Product: {{ $product->name }}</h3>
            <form id="edit-form-{{ $product->id }}" action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Tabs -->
                <div class="mb-4">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-4" aria-label="Tabs">
                            <button type="button" class="edit-tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent active-tab" data-tab="edit-basic-info-{{ $product->id }}" data-product-id="{{ $product->id }}">Basic Info</button>
                            <button type="button" class="edit-tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="edit-images-{{ $product->id }}" data-product-id="{{ $product->id }}">Images</button>
                            <button type="button" class="edit-tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="edit-data-{{ $product->id }}" data-product-id="{{ $product->id }}">Data</button>
                            <button type="button" class="edit-tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="edit-tags-{{ $product->id }}" data-product-id="{{ $product->id }}">Tags</button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div id="edit-basic-info-{{ $product->id }}" class="edit-tab-content">
                    <label for="edit_name_{{ $product->id }}" class="block font-medium text-sm text-gray-700">Name</label>
                    <input type="text" name="name" id="edit_name_{{ $product->id }}" value="{{ $product->name }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-2" />

                    <label for="edit_description_{{ $product->id }}" class="block font-medium text-sm text-gray-700">Description</label>
                    <input type="text" name="description" id="edit_description_{{ $product->id }}" value="{{ $product->description }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-2" />

                    <label for="edit_category_id_{{ $product->id }}" class="block font-medium text-sm text-gray-700">Category</label>
                    <select name="category_id" id="edit_category_id_{{ $product->id }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <label for="edit_best_seller_{{ $product->id }}" class="inline-flex items-center mb-4">
                        <input type="checkbox" name="best_seller" id="edit_best_seller_{{ $product->id }}" value="1" {{ $product->best_seller ? 'checked' : '' }} class="border-gray-300 rounded shadow-sm">
                        <span class="ml-2 text-sm text-gray-700">Best Seller</span>
                    </label>
                </div>

                <div id="edit-images-{{ $product->id }}" class="edit-tab-content hidden">
                    <h4 class="font-medium text-sm text-gray-700 mb-2">Images (Max 5, Select Main Image)</h4>
                    <div id="edit-image-container-{{ $product->id }}" class="space-y-4">
                        @foreach($product->hasManyImages as $index => $image)
                            <div class="image-entry">
                                <img src="{{ Storage::url($image->image) }}" class="image-preview" style="max-width: 100px; margin-bottom: 10px;" />
                                <input type="file" accept="image/*" class="image-input mb-2" />
                                <div style="max-width: 400px; max-height: 400px; margin-bottom: 10px;">
                                    <img class="image-preview" style="max-width: 100%; display: none;" />
                                </div>
                                <input type="hidden" name="cropped_images[]" class="cropped-image-input" />
                                <label class="inline-flex items-center mb-2">
                                    <input type="radio" name="main_image" value="{{ $index }}" class="main-image-radio" {{ $image->is_main ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Main Image</span>
                                </label>
                                <button type="button" class="crop-button bg-gray-600 text-white px-4 py-2 rounded mb-2" style="display:none;">Crop Image</button>
                                <button type="button" class="remove-image bg-red-600 text-white px-2 py-1 rounded">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-image mt-2 bg-blue-500 text-white px-4 py-2 rounded" data-product-id="{{ $product->id }}" {{ $product->hasManyImages->count() >= 5 ? 'disabled' : '' }}>Add Another Image</button>
                    <p id="edit-image-limit-message-{{ $product->id }}" class="text-red-600 text-sm mt-2 {{ $product->hasManyImages->count() >= 5 ? '' : 'hidden' }}">Maximum 5 images allowed.</p>
                </div>

                <div id="edit-data-{{ $product->id }}" class="edit-tab-content hidden">
                    <h4 class="font-medium text-sm text-gray-700 mb-2">Data</h4>
                    <div id="edit-data-container-{{ $product->id }}" class="space-y-4">
                        @foreach($product->hasManyData as $index => $data)
                            <div class="data-entry flex space-x-4 items-start">
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Name</label>
                                    <input type="text" name="data[{{ $index }}][name]" value="{{ $data->name }}" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                                </div>
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Description</label>
                                    <input type="text" name="data[{{ $index }}][description]" value="{{ $data->description }}" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                                </div>
                                <button type="button" class="remove-data bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-data mt-2 bg-blue-500 text-white px-4 py-2 rounded" data-product-id="{{ $product->id }}">Add Another Data</button>
                </div>

                <div id="edit-tags-{{ $product->id }}" class="edit-tab-content hidden">
                    <h4 class="font-medium text-sm text-gray-700 mb-2">Tags</h4>
                    <div id="edit-tag-container-{{ $product->id }}" class="space-y-4">
                        @foreach($product->hasManyTags as $index => $tag)
                            <div class="tag-entry flex space-x-4 items-start">
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Name</label>
                                    <input type="text" name="tags[{{ $index }}][name]" value="{{ $tag->name }}" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                                </div>
                                <div class="flex-1">
                                    <label class="block font-medium text-sm text-gray-700">Description</label>
                                    <textarea name="tags[{{ $index }}][description]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" rows="4">{{ $tag->description }}</textarea>
                                </div>
                                <button type="button" class="remove-tag bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-tag mt-2 bg-blue-500 text-white px-4 py-2 rounded" data-product-id="{{ $product->id }}">Add Another Tag</button>
                </div>

                <div class="mt-6 flex justify-end space-x-2">
                    <button type="button" class="close-edit-dialog px-4 py-2 border rounded" data-product-id="{{ $product->id }}">Cancel</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Update</button>
                </div>
            </form>
        </dialog>
    @endforeach

    {{-- Cropper.js CSS & JS CDN --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <script>
        // Store croppers for all images
        let croppers = {};

        // Toggle add dialog
        document.getElementById('open-add-dialog').addEventListener('click', function () {
            document.getElementById('add-product-dialog').showModal();
        });

        document.getElementById('close-add-dialog').addEventListener('click', function () {
            document.getElementById('add-product-dialog').close();
        });

        // Toggle edit dialog
        function openEditDialog(id) {
            const dialog = document.getElementById('edit-product-dialog-' + id);
            if (dialog) {
                dialog.showModal();
            }
        }

        document.querySelectorAll('.close-edit-dialog').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.dataset.productId;
                document.getElementById('edit-product-dialog-' + productId).close();
            });
        });

        // Tab functionality for add form
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                const tabId = this.dataset.tab;
                document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
                document.getElementById(tabId).classList.remove('hidden');
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active-tab');
                    btn.classList.add('text-gray-500', 'border-transparent');
                });
                this.classList.add('active-tab', 'text-gray-700', 'border-blue-500');
                this.classList.remove('text-gray-500', 'border-transparent');
            });
        });

        // Tab functionality for edit forms
        document.querySelectorAll('.edit-tab-button').forEach(button => {
            button.addEventListener('click', function () {
                const tabId = this.dataset.tab;
                const productId = this.dataset.productId;
                document.querySelectorAll(`#edit-product-dialog-${productId} .edit-tab-content`).forEach(content => content.classList.add('hidden'));
                document.getElementById(tabId).classList.remove('hidden');
                document.querySelectorAll(`#edit-product-dialog-${productId} .edit-tab-button`).forEach(btn => {
                    btn.classList.remove('active-tab');
                    btn.classList.add('text-gray-500', 'border-transparent');
                });
                this.classList.add('active-tab', 'text-gray-700', 'border-blue-500');
                this.classList.remove('text-gray-500', 'border-transparent');
            });
        });

        // Initialize image cropper
        function initializeCropper(input, preview, cropButton, hiddenInput, container) {
            input.addEventListener('change', function (e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    const url = URL.createObjectURL(file);

                    preview.src = url;
                    preview.style.display = 'block';

                    const cropperId = input.id || Math.random().toString(36).substr(2, 9);
                    if (croppers[cropperId]) {
                        croppers[cropperId].destroy();
                    }

                    croppers[cropperId] = new Cropper(preview, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                    });

                    cropButton.style.display = 'inline-block';
                    if (container.querySelector('.remove-image')) {
                        container.querySelector('.remove-image').style.display = 'inline-block';
                    }
                }
            });

            cropButton.addEventListener('click', function () {
                const cropperId = input.id || cropButton.previousElementSibling.id;
                const cropper = croppers[cropperId];
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({ width: 400, height: 400, imageSmoothingQuality: 'high' });

                canvas.toBlob(function (blob) {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function () {
                        hiddenInput.value = reader.result;
                        preview.src = reader.result;
                        cropper.destroy();
                        croppers[cropperId] = null;
                        cropButton.style.display = 'none';
                    };
                }, 'image/jpeg', 0.7);
            });
        }

        // Initialize existing image inputs
        document.querySelectorAll('.image-entry').forEach(entry => {
            const input = entry.querySelector('.image-input');
            const preview = entry.querySelector('.image-preview:not([src])');
            const cropButton = entry.querySelector('.crop-button');
            const hiddenInput = entry.querySelector('.cropped-image-input');
            if (input && preview && cropButton && hiddenInput) {
                input.id = 'image-input-' + Math.random().toString(36).substr(2, 9);
                initializeCropper(input, preview, cropButton, hiddenInput, entry);
            }
        });

        // Update image count and toggle add button
        function updateImageCount(container, addButton, limitMessage, maxImages = 5) {
            const imageCount = container.querySelectorAll('.image-entry').length;
            if (imageCount >= maxImages) {
                addButton.disabled = true;
                limitMessage.classList.remove('hidden');
            } else {
                addButton.disabled = false;
                limitMessage.classList.add('hidden');
            }
        }

        // Add new image field
        function addImageField(container, prefix = '', productId = null) {
            const index = container.querySelectorAll('.image-entry').length;
            const entry = document.createElement('div');
            entry.className = 'image-entry';
            entry.innerHTML = `
                <input type="file" accept="image/*" class="image-input mb-2" id="image-input-${prefix}${index}" />
                <div style="max-width: 400px; max-height: 400px; margin-bottom: 10px;">
                    <img class="image-preview" style="max-width: 100%; display: none;" />
                </div>
                <input type="hidden" name="${prefix}cropped_images[]" class="cropped-image-input" />
                <label class="inline-flex items-center mb-2">
                    <input type="radio" name="${prefix}main_image" value="${index}" class="main-image-radio">
                    <span class="ml-2 text-sm text-gray-700">Main Image</span>
                </label>
                <button type="button" class="crop-button bg-gray-600 text-white px-4 py-2 rounded mb-2" style="display:none;">Crop Image</button>
                <button type="button" class="remove-image bg-red-600 text-white px-2 py-1 rounded">Remove</button>
            `;
            container.appendChild(entry);

            const input = entry.querySelector('.image-input');
            const preview = entry.querySelector('.image-preview');
            const cropButton = entry.querySelector('.crop-button');
            const hiddenInput = entry.querySelector('.cropped-image-input');
            initializeCropper(input, preview, cropButton, hiddenInput, entry);

            const addButton = productId ? document.querySelector(`.add-image[data-product-id="${productId}"]`) : document.getElementById('add-image');
            const limitMessage = productId ? document.getElementById(`edit-image-limit-message-${productId}`) : document.getElementById('image-limit-message');
            updateImageCount(container, addButton, limitMessage);

            entry.querySelector('.remove-image').addEventListener('click', function () {
                entry.remove();
                updateImageCount(container, addButton, limitMessage);
            });
        }

        // Add new data field
        function addDataField(container, prefix = '', indexOffset = 0) {
            const index = container.querySelectorAll('.data-entry').length + indexOffset;
            const entry = document.createElement('div');
            entry.className = 'data-entry flex space-x-4 items-start';
            entry.innerHTML = `
                <div class="flex-1">
                    <label class="block font-medium text-sm text-gray-700">Name</label>
                    <input type="text" name="${prefix}data[${index}][name]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                </div>
                <div class="flex-1">
                    <label class="block font-medium text-sm text-gray-700">Description</label>
                    <input type="text" name="${prefix}data[${index}][description]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                </div>
                <button type="button" class="remove-data bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
            `;
            container.appendChild(entry);

            entry.querySelector('.remove-data').addEventListener('click', function () {
                entry.remove();
            });
        }

        // Add new tag field
        function addTagField(container, prefix = '', indexOffset = 0) {
            const index = container.querySelectorAll('.tag-entry').length + indexOffset;
            const entry = document.createElement('div');
            entry.className = 'tag-entry flex space-x-4 items-start';
            entry.innerHTML = `
                <div class="flex-1">
                    <label class="block font-medium text-sm text-gray-700">Name</label>
                    <input type="text" name="${prefix}tags[${index}][name]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
                </div>
                <div class="flex-1">
                    <label class="block font-medium text-sm text-gray-700">Description</label>
                    <textarea name="${prefix}tags[${index}][description]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" rows="4"></textarea>
                </div>
                <button type="button" class="remove-tag bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
            `;
            container.appendChild(entry);

            entry.querySelector('.remove-tag').addEventListener('click', function () {
                entry.remove();
            });
        }

        // Add image button for create form
        document.getElementById('add-image').addEventListener('click', function () {
            addImageField(document.getElementById('image-container'));
        });

        // Add data button for create form
        document.getElementById('add-data').addEventListener('click', function () {
            addDataField(document.getElementById('data-container'));
        });

        // Add tag button for create form
        document.getElementById('add-tag').addEventListener('click', function () {
            addTagField(document.getElementById('tag-container'));
        });

        // Add image buttons for edit forms
        document.querySelectorAll('.add-image').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.dataset.productId;
                addImageField(document.getElementById('edit-image-container-' + productId), '', productId);
            });
        });

        // Add data buttons for edit forms
        document.querySelectorAll('.add-data').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.dataset.productId;
                const container = document.getElementById('edit-data-container-' + productId);
                addDataField(container, '', container.querySelectorAll('.data-entry').length);
            });
        });

        // Add tag buttons for edit forms
        document.querySelectorAll('.add-tag').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.dataset.productId;
                const container = document.getElementById('edit-tag-container-' + productId);
                addTagField(container, '', container.querySelectorAll('.tag-entry').length);
            });
        });

        // Remove buttons for create and edit forms
        document.querySelectorAll('.remove-image, .remove-data, .remove-tag').forEach(button => {
            button.addEventListener('click', function () {
                const container = this.closest('.space-y-4');
                const addButton = container.nextElementSibling;
                const limitMessage = addButton.nextElementSibling;
                this.parentElement.remove();
                if (container.id.includes('image-container')) {
                    updateImageCount(container, addButton, limitMessage);
                }
            });
        });

        // Initialize image counts for edit forms
        document.querySelectorAll('[id^="edit-image-container-"]').forEach(container => {
            const productId = container.id.replace('edit-image-container-', '');
            const addButton = document.querySelector(`.add-image[data-product-id="${productId}"]`);
            const limitMessage = document.getElementById(`edit-image-limit-message-${productId}`);
            updateImageCount(container, addButton, limitMessage);
        });
    </script>

    <style>
        dialog {
            max-height: 80vh;
            overflow-y: auto;
        }
        .active-tab {
            border-bottom: 2px solid #3b82f6 !important;
            color: #1f2937 !important;
        }
    </style>
</x-app-layout>
