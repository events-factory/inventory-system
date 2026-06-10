<div class="space-y-6">
    <!-- Dropdowns and Inputs Row -->
    <div class="flex flex-col sm:flex-row sm:flex-wrap gap-4 items-end">
        {{-- Category Select --}}
        <div class="w-full sm:flex-1 min-w-[140px]">
            <label for="category" class="block text-sm font-medium text-white mb-1">Category</label>
            <select wire:model.live="selectedCategory"
                id="category"
                class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            <option value="" class="bg-white text-black dark:bg-gray-900 dark:text-white">-- Select Category --</option>
            @foreach($this->categories as $id => $name)
                <option value="{{ $id }}" wire:key="cat-{{ $id }}" class="bg-white text-black dark:bg-gray-900 dark:text-white">{{ $name }}</option>
            @endforeach
        </select>
        </div>

        {{-- Subcategory Select --}}
        <div class="w-full sm:flex-1 min-w-[140px]">
            <label for="subcategory" class="block text-sm font-medium text-white mb-1">Subcategory</label>
            <select wire:model.live="selectedSubcategory"
                    id="subcategory"
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <option value="" class="bg-white text-black dark:bg-gray-900 dark:text-white">-- Select Subcategory --</option>
                @foreach($this->subcategories as $id => $name)
                    <option value="{{ $id }}" wire:key="sub-{{ $id }}" class="bg-white text-black dark:bg-gray-900 dark:text-white">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Group Select --}}
        <div class="w-full sm:flex-1 min-w-[140px]">
            <label for="group" class="block text-sm font-medium text-white mb-1">Group</label>
             <select wire:model.live="selectedGroup"
                    id="group"
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <option value="" class="bg-white text-black dark:bg-gray-900 dark:text-white">-- Select Group --</option>
                @foreach($this->groups as $id => $name)
                    <option value="{{ $id }}" wire:key="grp-{{ $id }}" class="bg-white text-black dark:bg-gray-900 dark:text-white">{{ $name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Item Select --}}
        <div class="w-full sm:flex-1 min-w-[140px]">
            <label for="item" class="block text-sm font-medium text-white mb-1">Item</label>
            <select wire:model.live="selectedItem"
                    id="item"
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <option value="" class="bg-white text-black dark:bg-gray-900 dark:text-white">-- Select Item --</option>
                @foreach($this->itemsList as $id => $name)
                    <option value="{{ $id }}" wire:key="item-{{ $id }}" class="bg-white text-black dark:bg-gray-900 dark:text-white">{{ $name }}</option>
                @endforeach
            </select>
        </div>

{{-- Quantity Input --}}
<div class="w-full sm:flex-1 min-w-[140px]">
    <label for="quantity" class="block text-sm font-medium text-white mb-1">Quantity</label>
    <input type="number" id="quantity" min="1" wire:model="quantity"
        class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-black 
               dark:bg-gray-800 dark:text-white dark:border-gray-600 shadow-sm 
               focus:border-primary-500 focus:ring-primary-500 sm:text-sm placeholder:text-gray-500 
               dark:placeholder:text-gray-400" />

    @if ($quantityWarning)
        <p class="text-sm text-red-500 mt-1">{{ $quantityWarning }}</p>
    @endif
</div>



        {{-- Add Item Button --}}
        <div class="w-full sm:w-auto pt-2 sm:pt-7">
            <button type="button" wire:click="addItem"
                class="inline-flex items-center justify-center rounded-md border border-transparent bg-primary-600 p-2 text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                aria-label="Add Item" title="Add Item">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Items Table -->
    @if ($addedItems)
        <table class="w-full mt-6 text-sm border-collapse border border-gray-300 dark:border-gray-600 bg-white text-black dark:bg-gray-800 dark:text-white">
    <thead>
        <tr class="bg-gray-100 dark:bg-gray-700">
            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">Item</th>
            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">Quantity</th>
            <th class="border border-gray-300 dark:border-gray-600 px-4 py-2 text-left">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($addedItems as $index => $item)
            <tr wire:key="added-{{ $item['item_id'] }}" class="odd:bg-white even:bg-gray-100 dark:odd:bg-gray-800 dark:even:bg-gray-700">
                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">{{ $item['item_name'] }}</td>
                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                    <div class="flex items-center space-x-1">
                        <button type="button" wire:click="decrementQuantity({{ $index }})" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition disabled:opacity-50"
                            {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                            -
                        </button>
                        <input type="number" min="1" max="{{ $item['available_quantity'] ?? 9999 }}" 
                            wire:model.live="addedItems.{{ $index }}.quantity" 
                            wire:change="validateItemQuantity({{ $index }})"
                            class="w-16 text-center rounded border border-gray-300 bg-white text-black dark:bg-gray-800 dark:text-white dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" />
                        <button type="button" wire:click="incrementQuantity({{ $index }})" 
                            class="inline-flex items-center justify-center w-8 h-8 rounded border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition disabled:opacity-50"
                            {{ $item['quantity'] >= ($item['available_quantity'] ?? 9999) ? 'disabled' : '' }}>
                            +
                        </button>
                        @if(isset($item['available_quantity']))
                            <span class="text-xs text-gray-500 dark:text-gray-400 pl-2">
                                / {{ $item['available_quantity'] }} available
                            </span>
                        @endif
                    </div>
                </td>
                <td class="border border-gray-300 dark:border-gray-600 px-4 py-2">
                    <button wire:click="removeItem({{ $index }})"
                        class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300"
                        title="Remove item" type="button" aria-label="Remove item">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-4 
                                    0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4"/>
                        </svg>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

    @endif

    <!-- Hidden Input for JSON -->
    <input type="hidden" name="requisition_items_json" value="{{ json_encode($addedItems) }}">
</div>