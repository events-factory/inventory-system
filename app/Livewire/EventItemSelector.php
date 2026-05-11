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

        if (collect($this->addedItems)->pluck('item_id')->contains($item->id)) {
            $this->addError('selectedItem', 'This item is already added.');
            return;
        }

        $this->addedItems[] = [
            'category_id' => $this->selectedCategory,
            'subcategory_id' => $this->selectedSubcategory,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'quantity' => $this->quantity,
        ];

        $this->dispatch('updateItems', addedItems: $this->addedItems);


        $this->selectedItem = null;
        $this->quantity = 1;
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
