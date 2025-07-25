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
                <form id="add-product-form" method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Tabs -->
                    <div class="mb-4">
                        <div class="border-b border-gray-200">
                            <nav class="flex space-x-4" aria-label="Tabs">
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent active-tab" data-tab="basic-info">Basic Info</button>
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="images">Images</button>
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="data">Data</button>
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="tags">Tags</button>
                                <button type="button" class="tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="months">Months</button>
                            </nav>
                        </div>
                    </div>

                    <!-- Tab Content -->
                    <div id="basic-info" class="tab-content">
                        <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                        <input type="text" name="name" id="name" required
                               class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" />

                        <label for="subdescription" class="block font-medium text-sm text-gray-700">Sub Description</label>
                        <input type="text" name="subdescription" id="subdescription" required
                               class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" />

                        <label for="description" class="block font-medium text-sm text-gray-700">Description</label>
                        <textarea name="description" id="description" required
                                  class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" rows="10"></textarea>

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

                        <div id="price-container" class="hidden">
                            <label for="price" class="block font-medium text-sm text-gray-700">Price</label>
                            <input type="number" name="price" id="price" step="0.01" min="0"
                                   class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" />
                        </div>
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
                        <button type="button" id="add-image" class="mt-2 bg-blue-500 text-white px-4 py-2 rounded">Add Another Image</button>
                        <p id="image-limit-message" class="text-red-600 text-sm mt-2 hidden">Maximum 5 images allowed.</p>
                    </div>

                    <div id="data" class="tab-content hidden">
                        <h4 class="font-medium text-sm text-gray-700 mb-2">Data (Max 8)</h4>
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
                        <p id="data-limit-message" class="text-red-600 text-sm mt-2 hidden">Maximum 8 data entries allowed.</p>
                    </div>

                    <div id="tags" class="tab-content hidden">
                        <h4 class="font-medium text-sm text-gray-700 mb-2">Tags (Max 6)</h4>
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
                        <p id="tag-limit-message" class="text-red-600 text-sm mt-2 hidden">Maximum 6 tags allowed.</p>
                    </div>

                    <div id="months" class="tab-content hidden">
                        <h4 class="font-medium text-sm text-gray-700 mb-2">Availability Months</h4>
                        <div id="month-container" class="grid grid-cols-3 gap-4">
                            @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="months[]" value="{{ $index + 1 }}" class="border-gray-300 rounded shadow-sm">
                                    <span class="ml-2 text-sm text-gray-700">{{ $month }}</span>
                                </label>
                            @endforeach
                        </div>
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
                            <th class="border px-4 py-2">Category</th>
                            <th class="border px-4 py-2">Best Seller</th>
                            <th class="border px-4 py-2">Price</th>
                            <th class="border px-4 py-2">Months</th>
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
                                <td class="border px-4 py-2">{{ $product->hasOneCategory->name ?? 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $product->best_seller ? 'Yes' : 'No' }}</td>
                                <td class="border px-4 py-2">{{ $product->price ? '$' . number_format($product->price, 2) : 'N/A' }}</td>
                                <td class="border px-4 py-2">{{ $product->months ? implode(', ', array_map(fn($m) => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][$m-1], json_decode($product->months))) : 'N/A' }}</td>
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
                            <button type="button" class="edit-tab-button text-gray-500 hover:text-gray-700 px-3 py-2 font-medium text-sm border-b-2 border-transparent" data-tab="edit-months-{{ $product->id }}" data-product-id="{{ $product->id }}">Months</button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div id="edit-basic-info-{{ $product->id }}" class="edit-tab-content">
                    <label for="edit_name_{{ $product->id }}" class="block font-medium text-sm text-gray-700">Name</label>
                    <input type="text" name="name" id="edit_name_{{ $product->id }}" value="{{ $product->name }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-2" />

                    <label for="edit_subdescription_{{ $product->id }}" class="block font-medium text-sm text-gray-700">Sub Description</label>
                    <input type="text" name="subdescription" id="edit_subdescription_{{ $product->id }}" value="{{ $product->subdescription }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-2" />

                    <label for="edit_description_{{ $product->id }}" class="block font-medium text-sm text-gray-700">Description</label>
                    <textarea rows="10" name="description" id="edit_description_{{ $product->id }}" required class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-2" >{{ $product->description }}</textarea>

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

                    <div id="edit-price-container-{{ $product->id }}" class="{{ $product->best_seller ? '' : 'hidden' }}">
                        <label for="edit_price_{{ $product->id }}" class="block font-medium text-sm text-gray-700">Price</label>
                        <input type="number" name="price" id="edit_price_{{ $product->id }}" value="{{ $product->price }}" step="0.01" min="0" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full mb-4" />
                    </div>
                </div>

                <div id="edit-images-{{ $product->id }}" class="edit-tab-content hidden">
                    <h4 class="font-medium text-sm text-gray-700 mb-2">Images (Max 5, Select Main Image)</h4>
                    <div id="edit-image-container-{{ $product->id }}" class="space-y-4">
                        @foreach($product->hasManyImages as $index => $image)
                            <div class="image-entry" data-image-id="{{ $image->id }}">
                                <img src="{{ Storage::url($image->image) }}" class="existing-image-preview" style="max-width: 100px; margin-bottom: 10px;" />
                                <input type="file" accept="image/*" class="image-input mb-2" style="display: none;" />
                                <div style="max-width: 400px; max-height: 400px; margin-bottom: 10px;">
                                    <img class="image-preview" style="max-width: 100%; display: none;" />
                                </div>
                                <input type="hidden" name="cropped_images[]" class="cropped-image-input" />
                                <label class="inline-flex items-center mb-2">
                                    <input type="radio" name="main_image" value="{{ $index }}" class="main-image-radio" {{ $image->is_main ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">Main Image</span>
                                </label>
                                <button type="button" class="crop-button bg-gray-600 text-white px-4 py-2 rounded mb-2" style="display: none;">Crop Image</button>
                                <button type="button" class="remove-image bg-red-600 text-white px-2 py-1 rounded">Remove</button>
                                <button type="button" class="replace-image bg-blue-500 text-white px-2 py-1 rounded">Replace</button>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-image mt-2 bg-blue-500 text-white px-4 py-2 rounded" data-product-id="{{ $product->id }}" {{ $product->hasManyImages->count() >= 5 ? 'disabled' : '' }}>Add Another Image</button>
                    <p id="edit-image-limit-message-{{ $product->id }}" class="text-red-600 text-sm mt-2 {{ $product->hasManyImages->count() >= 5 ? '' : 'hidden' }}">Maximum 5 images allowed.</p>
                    <div id="deleted-images-container-{{ $product->id }}" class="hidden">
                        <!-- Hidden inputs for deleted images will be added here -->
                    </div>
                </div>

                <div id="edit-data-{{ $product->id }}" class="edit-tab-content hidden">
                    <h4 class="font-medium text-sm text-gray-700 mb-2">Data (Max 8)</h4>
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
                    <button type="button" class="add-data mt-2 bg-blue-500 text-white px-4 py-2 rounded" data-product-id="{{ $product->id }}" {{ $product->hasManyData->count() >= 8 ? 'disabled' : '' }}>Add Another Data</button>
                    <p id="edit-data-limit-message-{{ $product->id }}" class="text-red-600 text-sm mt-2 {{ $product->hasManyData->count() >= 8 ? '' : 'hidden' }}">Maximum 8 data entries allowed.</p>
                </div>

                <div id="edit-tags-{{ $product->id }}" class="edit-tab-content hidden">
                    <h4 class="font-medium text-sm text-gray-700 mb-2">Tags (Max 6)</h4>
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
                    <button type="button" class="add-tag mt-2 bg-blue-500 text-white px-4 py-2 rounded" data-product-id="{{ $product->id }}" {{ $product->hasManyTags->count() >= 6 ? 'disabled' : '' }}>Add Another Tag</button>
                    <p id="edit-tag-limit-message-{{ $product->id }}" class="text-red-600 text-sm mt-2 {{ $product->hasManyTags->count() >= 6 ? '' : 'hidden' }}">Maximum 6 tags allowed.</p>
                </div>

                <div id="edit-months-{{ $product->id }}" class="edit-tab-content hidden">
                    <h4 class="font-medium text-sm text-gray-700 mb-2">Availability Months</h4>
                    <div id="edit-month-container-{{ $product->id }}" class="grid grid-cols-3 gap-4">
                        @php
                            $selectedMonths = $product->months ? json_decode($product->months, true) : [];
                        @endphp
                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $index => $month)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="months[]" value="{{ $index + 1 }}" class="border-gray-300 rounded shadow-sm" {{ in_array($index + 1, $selectedMonths) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">{{ $month }}</span>
                            </label>
                        @endforeach
                    </div>
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

        // Initialize all functionality when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle add dialog
            document.getElementById('open-add-dialog').addEventListener('click', function() {
                document.getElementById('add-product-dialog').showModal();
            });

            document.getElementById('close-add-dialog').addEventListener('click', function() {
                document.getElementById('add-product-dialog').close();
            });

            // Toggle price field based on best seller checkbox
            const bestSellerCheckbox = document.getElementById('best_seller');
            const priceContainer = document.getElementById('price-container');
            bestSellerCheckbox.addEventListener('change', function() {
                priceContainer.classList.toggle('hidden', !this.checked);
                const priceInput = priceContainer.querySelector('#price');
                priceInput.required = this.checked;
            });

            // Tab functionality for add form
            document.querySelectorAll('.tab-button').forEach(button => {
                button.addEventListener('click', function() {
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

            // Initialize the first image input in add form
            const firstImageInput = document.querySelector('#image-container .image-input');
            if (firstImageInput) {
                const entry = firstImageInput.closest('.image-entry');
                const preview = entry.querySelector('.image-preview');
                const cropButton = entry.querySelector('.crop-button');
                const hiddenInput = entry.querySelector('.cropped-image-input');
                initializeCropper(firstImageInput, preview, cropButton, hiddenInput, entry);
            }

            // Enable "Add Another Image" button for add form
            updateImageCount(document.getElementById('image-container'), document.getElementById('add-image'), document.getElementById('image-limit-message'));

            // Enable "Add Another Data" button for add form
            updateDataCount(document.getElementById('data-container'), document.getElementById('add-data'), document.getElementById('data-limit-message'));

            // Enable "Add Another Tag" button for add form
            updateTagCount(document.getElementById('tag-container'), document.getElementById('add-tag'), document.getElementById('tag-limit-message'));
        });

        // Toggle edit dialog
        function openEditDialog(id) {
            const dialog = document.getElementById(`edit-product-dialog-${id}`);
            if (dialog) {
                dialog.showModal();
                initializeEditForm(id);
            }
        }

        // Initialize edit form
        function initializeEditForm(productId) {
            const container = document.getElementById(`edit-image-container-${productId}`);
            const dataContainer = document.getElementById(`edit-data-container-${productId}`);
            const tagContainer = document.getElementById(`edit-tag-container-${productId}`);
            const bestSellerCheckbox = document.getElementById(`edit_best_seller_${productId}`);
            const priceContainer = document.getElementById(`edit-price-container-${productId}`);

            if (container) {
                container.querySelectorAll('.image-input').forEach(input => {
                    const entry = input.closest('.image-entry');
                    const preview = entry.querySelector('.image-preview');
                    const cropButton = entry.querySelector('.crop-button');
                    const hiddenInput = entry.querySelector('.cropped-image-input');
                    input.id = `image-input-${productId}-${Math.random().toString(36).substr(2, 9)}`;
                    initializeCropper(input, preview, cropButton, hiddenInput, entry);
                });

                // Initialize replace buttons
                container.querySelectorAll('.replace-image').forEach(button => {
                    button.addEventListener('click', function() {
                        const entry = button.closest('.image-entry');
                        const input = entry.querySelector('.image-input');
                        input.style.display = 'block';
                        input.click();
                    });
                });

                updateImageCount(container, document.querySelector(`.add-image[data-product-id="${productId}"]`), document.getElementById(`edit-image-limit-message-${productId}`));
            }

            if (dataContainer) {
                updateDataCount(dataContainer, document.querySelector(`.add-data[data-product-id="${productId}"]`), document.getElementById(`edit-data-limit-message-${productId}`));
            }

            if (tagContainer) {
                updateTagCount(tagContainer, document.querySelector(`.add-tag[data-product-id="${productId}"]`), document.getElementById(`edit-tag-limit-message-${productId}`));
            }

            // Toggle price field in edit form
            if (bestSellerCheckbox && priceContainer) {
                // Set initial visibility based on checkbox state
                priceContainer.classList.toggle('hidden', !bestSellerCheckbox.checked);
                const priceInput = priceContainer.querySelector(`#edit_price_${productId}`);
                priceInput.required = bestSellerCheckbox.checked;

                // Add event listener for checkbox changes
                bestSellerCheckbox.addEventListener('change', function() {
                    priceContainer.classList.toggle('hidden', !this.checked);
                    priceInput.required = this.checked;
                });
            }
        }

        // Close edit dialog
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('close-edit-dialog')) {
                const productId = e.target.dataset.productId;
                document.getElementById(`edit-product-dialog-${productId}`).close();
            }
        });

        // Tab functionality for edit forms
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('edit-tab-button')) {
                const tabId = e.target.dataset.tab;
                const productId = e.target.dataset.productId;
                const dialog = document.getElementById(`edit-product-dialog-${productId}`);

                dialog.querySelectorAll('.edit-tab-content').forEach(content => content.classList.add('hidden'));
                dialog.querySelector(`#${tabId}`).classList.remove('hidden');
                dialog.querySelectorAll('.edit-tab-button').forEach(btn => {
                    btn.classList.remove('active-tab');
                    btn.classList.add('text-gray-500', 'border-transparent');
                });
                e.target.classList.add('active-tab', 'text-gray-700', 'border-blue-500');
                e.target.classList.remove('text-gray-500', 'border-transparent');
            }
        });

        // Initialize image cropper
        function initializeCropper(input, preview, cropButton, hiddenInput, container) {
            input.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    const url = URL.createObjectURL(file);

                    preview.src = url;
                    preview.style.display = 'block';
                    container.querySelector('.existing-image-preview')?.classList.add('hidden');

                    const cropperId = input.id;
                    if (croppers[cropperId]) {
                        croppers[cropperId].destroy();
                    }

                    croppers[cropperId] = new Cropper(preview, {
                        aspectRatio: 300 / 310,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                    });

                    cropButton.style.display = 'inline-block';
                    const removeBtn = container.querySelector('.remove-image');
                    if (removeBtn) {
                        removeBtn.style.display = 'inline-block';
                    }
                }
            });

            cropButton.addEventListener('click', function() {
                const cropperId = input.id;
                const cropper = croppers[cropperId];
                if (!cropper) return;

                const canvas = cropper.getCroppedCanvas({
                    width: 300,
                    height: 310,
                    imageSmoothingQuality: 'high'
                });

                canvas.toBlob(function(blob) {
                    const reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        hiddenInput.value = reader.result;
                        preview.src = reader.result;
                        cropper.destroy();
                        croppers[cropperId] = null;
                        cropButton.style.display = 'none';
                    };
                }, 'image/jpeg', 0.7);
            });
        }

        // Update image count and toggle add button
        function updateImageCount(container, addButton, limitMessage, maxImages = 5) {
            const imageCount = container.querySelectorAll('.image-entry').length;
            if (imageCount >= maxImages) {
                addButton.disabled = true;
                limitMessage?.classList.remove('hidden');
            } else {
                addButton.disabled = false;
                limitMessage?.classList.add('hidden');
            }
            updateMainImageIndexes(container);
        }

        // Update data count and toggle add button
        function updateDataCount(container, addButton, limitMessage, maxData = 8) {
            const dataCount = container.querySelectorAll('.data-entry').length;
            if (dataCount >= maxData) {
                addButton.disabled = true;
                limitMessage?.classList.remove('hidden');
            } else {
                addButton.disabled = false;
                limitMessage?.classList.add('hidden');
            }
        }

        // Update tag count and toggle add button
        function updateTagCount(container, addButton, limitMessage, maxTags = 6) {
            const tagCount = container.querySelectorAll('.tag-entry').length;
            if (tagCount >= maxTags) {
                addButton.disabled = true;
                limitMessage?.classList.remove('hidden');
            } else {
                addButton.disabled = false;
                limitMessage?.classList.add('hidden');
            }
        }

        // Update main image radio button values
        function updateMainImageIndexes(container) {
            const radios = container.querySelectorAll('.main-image-radio');
            radios.forEach((radio, index) => {
                radio.value = index;
            });
        }

        // Add new image field
        function addImageField(container, productId = null) {
            const index = container.querySelectorAll('.image-entry').length;
            if (index >= 5) return;
            const entry = document.createElement('div');
            entry.className = 'image-entry';
            entry.innerHTML = `
        <input type="file" accept="image/*" class="image-input mb-2" id="image-input-${productId || 'add'}-${index}" />
        <div style="max-width: 400px; max-height: 400px; margin-bottom: 10px;">
            <img class="image-preview" style="max-width: 100%; display: none;" />
        </div>
        <input type="hidden" name="cropped_images[]" class="cropped-image-input" />
        <label class="inline-flex items-center mb-2">
            <input type="radio" name="main_image" value="${index}" class="main-image-radio">
            <span class="ml-2 text-sm text-gray-700">Main Image</span>
        </label>
        <button type="button" class="crop-button bg-gray-600 text-white px-4 py-2 rounded mb-2" style="display:none;">Crop Image</button>
        <button type="button" class="remove-image bg-red-600 text-white px-2 py-1 rounded" style="display:none;">Remove</button>
    `;
            container.appendChild(entry);

            const input = entry.querySelector('.image-input');
            const preview = entry.querySelector('.image-preview');
            const cropButton = entry.querySelector('.crop-button');
            const hiddenInput = entry.querySelector('.cropped-image-input');
            initializeCropper(input, preview, cropButton, hiddenInput, entry);

            const addButton = productId ?
                document.querySelector(`.add-image[data-product-id="${productId}"]`) :
                document.getElementById('add-image');
            const limitMessage = productId ?
                document.getElementById(`edit-image-limit-message-${productId}`) :
                document.getElementById('image-limit-message');
            updateImageCount(container, addButton, limitMessage);
        }

        // Remove image field
        function removeImageField(entry, container, productId = null) {
            const imageId = entry.dataset.imageId;
            if (imageId) {
                const deletedContainer = document.getElementById(`deleted-images-container-${productId}`);
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_images[]';
                input.value = imageId;
                deletedContainer.appendChild(input);
            }
            entry.remove();
            const addButton = productId ?
                document.querySelector(`.add-image[data-product-id="${productId}"]`) :
                document.getElementById('add-image');
            const limitMessage = productId ?
                document.getElementById(`edit-image-limit-message-${productId}`) :
                document.getElementById('image-limit-message');
            updateImageCount(container, addButton, limitMessage);
        }

        // Add new data field
        function addDataField(container, indexOffset = 0) {
            const index = container.querySelectorAll('.data-entry').length + indexOffset;
            if (index >= 8) return;
            const entry = document.createElement('div');
            entry.className = 'data-entry flex space-x-4 items-start';
            entry.innerHTML = `
        <div class="flex-1">
            <label class="block font-medium text-sm text-gray-700">Name</label>
            <input type="text" name="data[${index}][name]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
        </div>
        <div class="flex-1">
            <label class="block font-medium text-sm text-gray-700">Description</label>
            <input type="text" name="data[${index}][description]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
        </div>
        <button type="button" class="remove-data bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
    `;
            container.appendChild(entry);

            entry.querySelector('.remove-data').addEventListener('click', function() {
                entry.remove();
                updateDataCount(container, container.closest('.edit-tab-content') ?
                        document.querySelector(`.add-data[data-product-id="${container.closest('.edit-tab-content').id.split('-').pop()}"]`) :
                        document.getElementById('add-data'),
                    container.closest('.edit-tab-content') ?
                        document.getElementById(`edit-data-limit-message-${container.closest('.edit-tab-content').id.split('-').pop()}`) :
                        document.getElementById('data-limit-message')
                );
            });

            updateDataCount(container, container.closest('.edit-tab-content') ?
                    document.querySelector(`.add-data[data-product-id="${container.closest('.edit-tab-content').id.split('-').pop()}"]`) :
                    document.getElementById('add-data'),
                container.closest('.edit-tab-content') ?
                    document.getElementById(`edit-data-limit-message-${container.closest('.edit-tab-content').id.split('-').pop()}`) :
                    document.getElementById('data-limit-message')
            );
        }

        // Add new tag field
        function addTagField(container, indexOffset = 0) {
            const index = container.querySelectorAll('.tag-entry').length + indexOffset;
            if (index >= 6) return;
            const entry = document.createElement('div');
            entry.className = 'tag-entry flex space-x-4 items-start';
            entry.innerHTML = `
        <div class="flex-1">
            <label class="block font-medium text-sm text-gray-700">Name</label>
            <input type="text" name="tags[${index}][name]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" />
        </div>
        <div class="flex-1">
            <label class="block font-medium text-sm text-gray-700">Description</label>
            <textarea name="tags[${index}][description]" class="border-gray-300 rounded-md shadow-sm mt-1 block w-full" rows="4"></textarea>
        </div>
        <button type="button" class="remove-tag bg-red-600 text-white px-2 py-1 rounded mt-6">Remove</button>
    `;
            container.appendChild(entry);

            entry.querySelector('.remove-tag').addEventListener('click', function() {
                entry.remove();
                updateTagCount(container, container.closest('.edit-tab-content') ?
                        document.querySelector(`.add-tag[data-product-id="${container.closest('.edit-tab-content').id.split('-').pop()}"]`) :
                        document.getElementById('add-tag'),
                    container.closest('.edit-tab-content') ?
                        document.getElementById(`edit-tag-limit-message-${container.closest('.edit-tab-content').id.split('-').pop()}`) :
                        document.getElementById('tag-limit-message')
                );
            });

            updateTagCount(container, container.closest('.edit-tab-content') ?
                    document.querySelector(`.add-tag[data-product-id="${container.closest('.edit-tab-content').id.split('-').pop()}"]`) :
                    document.getElementById('add-tag'),
                container.closest('.edit-tab-content') ?
                    document.getElementById(`edit-tag-limit-message-${container.closest('.edit-tab-content').id.split('-').pop()}`) :
                    document.getElementById('tag-limit-message')
            );
        }

        // Event delegation for all add buttons
        document.addEventListener('click', function(e) {
            // Add image in create form
            if (e.target && e.target.id === 'add-image') {
                const container = document.getElementById('image-container');
                if (container.querySelectorAll('.image-entry').length < 5) {
                    addImageField(container);
                }
            }
            // Add image in edit form
            else if (e.target && e.target.classList.contains('add-image')) {
                const productId = e.target.dataset.productId;
                const container = document.getElementById(`edit-image-container-${productId}`);
                if (container.querySelectorAll('.image-entry').length < 5) {
                    addImageField(container, productId);
                }
            }
            // Add data in create form
            else if (e.target && e.target.id === 'add-data') {
                const container = document.getElementById('data-container');
                if (container.querySelectorAll('.data-entry').length < 8) {
                    addDataField(container);
                }
            }
            // Add data in edit form
            else if (e.target && e.target.classList.contains('add-data')) {
                const productId = e.target.dataset.productId;
                const container = document.getElementById(`edit-data-container-${productId}`);
                if (container.querySelectorAll('.data-entry').length < 8) {
                    addDataField(container, container.querySelectorAll('.data-entry').length);
                }
            }
            // Add tag in create form
            else if (e.target && e.target.id === 'add-tag') {
                const container = document.getElementById('tag-container');
                if (container.querySelectorAll('.tag-entry').length < 6) {
                    addTagField(container);
                }
            }
            // Add tag in edit form
            else if (e.target && e.target.classList.contains('add-tag')) {
                const productId = e.target.dataset.productId;
                const container = document.getElementById(`edit-tag-container-${productId}`);
                if (container.querySelectorAll('.tag-entry').length < 6) {
                    addTagField(container, container.querySelectorAll('.tag-entry').length);
                }
            }
            // Remove image
            else if (e.target && e.target.classList.contains('remove-image')) {
                const entry = e.target.closest('.image-entry');
                const container = entry.closest('#image-container') || entry.closest(`[id^="edit-image-container-"]`);
                const productId = container.id.includes('edit-image-container-') ? container.id.split('-').pop() : null;
                removeImageField(entry, container, productId);
            }
            // Remove data
            else if (e.target && e.target.classList.contains('remove-data')) {
                const entry = e.target.closest('.data-entry');
                const container = entry.closest('#data-container') || entry.closest(`[id^="edit-data-container-"]`);
                entry.remove();
                updateDataCount(container, container.closest('.edit-tab-content') ?
                        document.querySelector(`.add-data[data-product-id="${container.closest('.edit-tab-content').id.split('-').pop()}"]`) :
                        document.getElementById('add-data'),
                    container.closest('.edit-tab-content') ?
                        document.getElementById(`edit-data-limit-message-${container.closest('.edit-tab-content').id.split('-').pop()}`) :
                        document.getElementById('data-limit-message')
                );
            }
            // Remove tag
            else if (e.target && e.target.classList.contains('remove-tag')) {
                const entry = e.target.closest('.tag-entry');
                const container = entry.closest('#tag-container') || entry.closest(`[id^="edit-tag-container-"]`);
                entry.remove();
                updateTagCount(container, container.closest('.edit-tab-content') ?
                        document.querySelector(`.add-tag[data-product-id="${container.closest('.edit-tab-content').id.split('-').pop()}"]`) :
                        document.getElementById('add-tag'),
                    container.closest('.edit-tab-content') ?
                        document.getElementById(`edit-tag-limit-message-${container.closest('.edit-tab-content').id.split('-').pop()}`) :
                        document.getElementById('tag-limit-message')
                );
            }
        });

        // Ensure at least one main image is selected before submission for add and edit forms
        document.querySelectorAll('#add-product-form, [id^="edit-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                const mainImageRadios = form.querySelectorAll('input[name="main_image"]:checked');
                if (!mainImageRadios.length) {
                    e.preventDefault();
                    alert('Please select a main image.');
                }
            });
        });

        // Dialog close on backdrop click
        const dialogs = document.querySelectorAll('dialog');
        dialogs.forEach(dialog => {
            dialog.addEventListener('click', function(e) {
                if (e.target === dialog) {
                    dialog.close();
                }
            });
        });

        // Clean up croppers on dialog close
        dialogs.forEach(dialog => {
            dialog.addEventListener('close', function() {
                Object.values(croppers).forEach(cropper => {
                    if (cropper) cropper.destroy();
                });
                croppers = {};
            });
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
