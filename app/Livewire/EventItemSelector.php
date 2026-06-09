<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Group;
use App\Models\Item;

class EventItemSelector extends Component
{
    public $selectedCategory = null;
    public $selectedSubcategory = null;
    public $selectedGroup = null;
    public $selectedItem = null;
    public int $quantity = 1;

    public array $addedItems = [];

    public $availableQuantity;
    public $quantityWarning;

    public function mount(): void
    {
        // No need to pluck categories in mount if using computed
    }

    // Validate the quantity against the available stock
    public function updatedSelectedItem($value): void
    {
        $item = Item::find($value);

        if ($item) {
            $this->availableQuantity = $item->quantity;
        } else {
            $this->availableQuantity = null;
        }

        $this->validateQuantity(); // Check if current quantity is still valid
    }

    // Validate the quantity when it is updated
    public function updatedQuantity($value): void
    {
        $this->validateQuantity();
    }
    // Validate the quantity against the available stock
    private function validateQuantity(): void
    {
        if ($this->selectedItem && $this->availableQuantity !== null) {
            if ($this->quantity > $this->availableQuantity) {
                $this->quantityWarning = "Only {$this->availableQuantity} item(s) available.";
            } else {
                $this->quantityWarning = null;
            }
        }
    }

    public function updatedSelectedCategory($value): void
    {
        $this->reset(['selectedSubcategory', 'selectedGroup', 'selectedItem']);
    }

    public function updatedSelectedSubcategory($value): void
    {
        $this->reset(['selectedGroup', 'selectedItem']);
    }

    public function updatedSelectedGroup($value): void
    {
        $this->reset('selectedItem');
    }

    #[\Livewire\Attributes\Computed]
    public function categories(): array
    {
        return Category::pluck('name', 'id')->toArray();
    }

    #[\Livewire\Attributes\Computed]
    public function subcategories(): array
    {
        if (!$this->selectedCategory) return [];
        return Subcategory::where('category_id', $this->selectedCategory)->pluck('name', 'id')->toArray();
    }

    #[\Livewire\Attributes\Computed]
    public function groups(): array
    {
        if (!$this->selectedSubcategory) return [];
        return Group::where('subcategory_id', $this->selectedSubcategory)->pluck('name', 'id')->toArray();
    }

    #[\Livewire\Attributes\Computed]
    public function itemsList(): array
    {
        if (!$this->selectedSubcategory && !$this->selectedGroup) return [];

        $query = Item::query();
        if ($this->selectedGroup) {
            $query->where('group_id', $this->selectedGroup);
        } else {
            $query->where('subcategory_id', $this->selectedSubcategory)->whereNull('group_id');
        }

        $items = $query->pluck('name', 'id');
        
        if (!$this->selectedGroup && $this->selectedSubcategory) {
            return $items->map(fn($name) => $name . ' (Ungrouped)')->toArray();
        }

        return $items->toArray();
    }

    public function addItem(): void
    {
        if (!$this->selectedItem || $this->quantity < 1)
            return;

        $item = Item::find($this->selectedItem);

        if (!$item) {
            $this->addError('selectedItem', 'Invalid item selected.');
            return;
        }

        if ($this->quantity > $item->quantity) {
            $this->addError('quantity', "Only {$item->quantity} item(s) available.");
            return;
        }

        // Check if item is already in the list, if so increment its quantity
        foreach ($this->addedItems as &$addedItem) {
            if ($addedItem['item_id'] === $item->id) {
                $newQuantity = $addedItem['quantity'] + $this->quantity;
                if ($newQuantity > $item->quantity) {
                    $this->addError('quantity', "Cannot add {$this->quantity} more. Only {$item->quantity} item(s) available in total (already added: {$addedItem['quantity']}).");
                    return;
                }
                $addedItem['quantity'] = $newQuantity;
                $addedItem['available_quantity'] = $item->quantity;
                $this->dispatch('updateItems', addedItems: $this->addedItems);

                $this->selectedItem = null;
                $this->quantity = 1;
                return;
            }
        }

        $this->addedItems[] = [
            'category_id' => $this->selectedCategory,
            'subcategory_id' => $this->selectedSubcategory,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'quantity' => $this->quantity,
            'available_quantity' => $item->quantity,
        ];

        $this->dispatch('updateItems', addedItems: $this->addedItems);


        $this->selectedItem = null;
        $this->quantity = 1;
    }

    public function incrementQuantity(int $index): void
    {
        if (!isset($this->addedItems[$index])) return;

        $itemId = $this->addedItems[$index]['item_id'];
        $item = Item::find($itemId);

        if (!$item) return;

        if ($this->addedItems[$index]['quantity'] >= $item->quantity) {
            return;
        }

        $this->addedItems[$index]['quantity']++;
        $this->addedItems[$index]['available_quantity'] = $item->quantity;
        $this->dispatch('updateItems', addedItems: $this->addedItems);
    }

    public function decrementQuantity(int $index): void
    {
        if (!isset($this->addedItems[$index])) return;

        if ($this->addedItems[$index]['quantity'] <= 1) {
            return;
        }

        $this->addedItems[$index]['quantity']--;
        $this->dispatch('updateItems', addedItems: $this->addedItems);
    }

    public function validateItemQuantity(int $index): void
    {
        if (!isset($this->addedItems[$index])) return;

        $quantity = (int) $this->addedItems[$index]['quantity'];
        $itemId = $this->addedItems[$index]['item_id'];
        $item = Item::find($itemId);

        if (!$item) return;

        if ($quantity < 1) {
            $quantity = 1;
        }

        if ($quantity > $item->quantity) {
            $quantity = $item->quantity;
        }

        $this->addedItems[$index]['quantity'] = $quantity;
        $this->addedItems[$index]['available_quantity'] = $item->quantity;
        $this->dispatch('updateItems', addedItems: $this->addedItems);
    }

    public function removeItem(int $index): void
    {
        unset($this->addedItems[$index]);
        $this->addedItems = array_values($this->addedItems);

        $this->dispatch('updateItems', addedItems: $this->addedItems);
    }

    public function render()
    {
        return view('livewire.event-item-selector');
    }
}
