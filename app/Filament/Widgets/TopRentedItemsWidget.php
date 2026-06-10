<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopRentedItemsWidget extends BaseWidget
{
    protected static ?string $heading = 'Top Rented Items';

    protected int | string | array $columnSpan = 1;

    protected static ?int $sort = 2; // Position next to ItemsChart

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Item::query()
                    ->join('item_requisition', 'items.id', '=', 'item_requisition.item_id')
                    ->select('items.id', 'items.name', DB::raw('SUM(item_requisition.quantity) as total_rented'))
                    ->groupBy('items.id', 'items.name')
                    ->orderByDesc('total_rented')
                    ->limit(3)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Item Name'),
            ])
            ->paginated(false);
    }
}
