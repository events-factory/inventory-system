<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\Requisition;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected array $requisitionData = [];

    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Extract requisition-specific data
    $this->requisitionData = [
    'expected_pickup_date' => $data['requisition_expected_pickup_date'],
    'expected_return_date' => $data['requisition_expected_return_date'],
    'number_of_items' => $data['number_of_items'],
    'category_id' => $data['requisition_category_id'] ?? null,
    'subcategory_id' => $data['requisition_subcategory_id'] ?? null,
    'group_id' => $data['requisition_group_id'] ?? null,
    'item_ids' => $data['requisition_item_ids'] ?? [], // Notice it's plural
    'status' => 'pending',
];

    // Unset them from Event data (because Event model doesn't have these columns)
    unset(
        $data['requisition_expected_pickup_date'],
        $data['requisition_expected_return_date'],
        $data['number_of_items'],
        $data['requisition_category_id'],
        $data['requisition_subcategory_id'],
        $data['requisition_group_id'],
        $data['requisition_item_ids'], // Changed from 'requisition_item_id' to 'requisition_item_ids'
    );

    return $data;
}

protected function afterCreate(): void
{
    $event = $this->record; // Event is created here

    // Create Requisition linked to this event
    $requisition = Requisition::create([
        'expected_pickup_date' => $this->requisitionData['expected_pickup_date'],
        'expected_return_date' => $this->requisitionData['expected_return_date'],
        'category_id' => $this->requisitionData['category_id'],
        'subcategory_id' => $this->requisitionData['subcategory_id'],
        'group_id' => $this->requisitionData['group_id'],
        'number_of_items' => $this->requisitionData['number_of_items'],
        'status' => 'pending',
        'event_id' => $event->id,
    ]);

    // Attach multiple items
    if (!empty($this->requisitionData['item_ids'])) {
        $requisition->items()->attach($this->requisitionData['item_ids']);
    }
}

}
