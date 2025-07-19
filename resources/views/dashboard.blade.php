<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard - Incoming Requests
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto space-y-10">
        <!-- Contact Messages -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3">Contact Messages</h3>
            <ul>
                @forelse ($contacts ?? collect() as $item)
                    <li class="mb-2">
                        <strong>{{ $item->email ?? 'N/A' }}</strong>: {{ $item->message ?? 'No message' }}
                    </li>
                @empty
                    <li>No contact messages yet.</li>
                @endforelse
            </ul>
        </div>

        <!-- Product Requests -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3">Product Requests</h3>
            <ul>
                @forelse ($products ?? collect() as $item)
                    <li class="mb-2">
                        <strong>{{ $item->email ?? 'N/A' }}</strong>: {{ $item->product ?? 'N/A' }} - {{ $item->message ?? 'No message' }}
                    </li>
                @empty
                    <li>No product requests yet.</li>
                @endforelse
            </ul>
        </div>

        <!-- Company Requests -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3">Company Requests</h3>
            <ul>
                @forelse ($companies ?? collect() as $item)
                    <li class="mb-2">
                        <strong>{{ $item->email ?? 'N/A' }}</strong> - {{ $item->company ?? 'N/A' }} | Ship to: {{ $item->ship_to ?? 'N/A' }}
                    </li>
                @empty
                    <li>No company requests yet.</li>
                @endforelse
            </ul>
        </div>

        <!-- Full Product Requests -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3">Full Product Requests</h3>
            <ul>
                @forelse ($fullRequests ?? collect() as $item)
                    <li class="mb-4 border-b pb-2">
                        <strong>{{ $item->full_name ?? 'N/A' }}</strong> ({{ $item->email ?? 'N/A' }})<br>
                        Product: {{ $item->product_type ?? 'N/A' }} - Shape: {{ $item->product_shape ?? 'N/A' }}<br>
                        Quantity: {{ $item->quantity ?? 'N/A' }} | Ship To: {{ $item->ship_to ?? 'N/A' }}
                    </li>
                @empty
                    <li>No full product requests yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>
