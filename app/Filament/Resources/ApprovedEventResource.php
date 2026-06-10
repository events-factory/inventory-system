<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApprovedEventResource\Pages\ListApprovedEvents;
use App\Filament\Resources\ApprovedEventResource\Pages\ViewApprovedEvent;
use App\Models\Event;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfoSection;

class ApprovedEventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationLabel = 'Approved Events';
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Stock Management';


    // Add this method to eager load relationships
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('requisition.stockMovements.item');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Event Details')
                    ->schema([
                        TextEntry::make('event_name')->label('Event Name'),
                        TextEntry::make('event_date')->label('Event Date')->date(),
                        TextEntry::make('event_location')->label('Event Location'),
                        TextEntry::make('responsible_person_name')->label('Responsible Person'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->query(
            Event::whereHas('requisition', fn ($query) =>
                $query->where('status', 'approved')
            )
        )
        ->columns([
            TextColumn::make('event_name')
                ->label('Event Name')
                ->searchable(),

            TextColumn::make('event_date')
                ->label('Event Date')
                ->sortable()
                ->date() // Formats it as a date
                ->searchable(),

            TextColumn::make('event_location')
                ->label('Event Location')
                ->searchable(),

            TextColumn::make('responsible_person_name')
                ->label('Responsible Person')
                ->searchable(),

            TextColumn::make('requisition.status')
                ->label('Requisition Status')
                ->badge()
                ->colors([
                    'success' => 'approved',
                    'warning' => 'pending',
                    'danger' => 'rejected',
                ]),
        ])
        ->filters([
            // Optional: add a date filter
            Filter::make('event_date')
                ->form([
                    DatePicker::make('from'),
                    DatePicker::make('until'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['from'], fn ($q) => $q->whereDate('event_date', '>=', $data['from']))
                        ->when($data['until'], fn ($q) => $q->whereDate('event_date', '<=', $data['until']));
                }),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
        ])
        ->defaultSort('created_at', 'desc') // Sort newest first
        ->searchDebounce(500); // Optional: adds a slight delay to reduce DB load
}


    public static function getPages(): array
    {
        return [
            'index' => ListApprovedEvents::route('/'),
            'view' => ViewApprovedEvent::route('/{record}'),
        ];
    }
}
