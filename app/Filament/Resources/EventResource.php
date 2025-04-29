<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Category;
use App\Models\Event;
use App\Models\Group;
use App\Models\Item;
use App\Models\Subcategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Event Details')
                    ->schema([
                        TextInput::make('event_name')
                            ->required()
                            ->label('Event Name'),

                        DatePicker::make('event_date')
                            ->required()
                            ->label('Event Date'),

                        TextInput::make('event_location')
                            ->required()
                            ->label('Event Location'),

                        TextInput::make('event_type')
                            ->required()
                            ->label('Event Type'),

                        TextInput::make('customer')
                            ->required()
                            ->label('Customer'),

                        TextInput::make('responsible_person_name')
                            ->required()
                            ->label('Responsible Person Name'),

                        TextInput::make('responsible_person_phone')
                            ->required()
                            ->label('Responsible Person Phone'),

                        TextInput::make('responsible_person_email')
                            ->required()
                            ->email()
                            ->label('Responsible Person Email'),

                        Select::make('urgency')
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                            ])
                            ->required()
                            ->label('Urgency'),

                        Textarea::make('notes')
                            ->nullable()
                            ->label('Notes'),
                    ]),

                    Section::make('Requisition Details')
    ->schema([
        DatePicker::make('requisition_expected_pickup_date')
            ->required()
            ->label('Expected Pick-Up Date'),

        DatePicker::make('requisition_expected_return_date')
            ->required()
            ->label('Expected Return Date'),

        Select::make('requisition_category_id')
    ->options(Category::all()->pluck('name', 'id')) // Fetch categories from the database
    ->required()
    ->label('Category')
    ->reactive()
    ->afterStateUpdated(function (callable $set) {
        // Only reset subcategory when category changes, but don't reset items
        $set('requisition_subcategory_id', null); 
    }),

Select::make('requisition_subcategory_id')
    ->options(function (callable $get) {
        $categoryId = $get('requisition_category_id');
        return Subcategory::where('category_id', $categoryId)->pluck('name', 'id');
    })
    ->required()
    ->label('Subcategory')
    ->reactive()
    ->afterStateUpdated(function (callable $set) {
        // Don't reset items when subcategory changes
    }),

        // Select Group
        Select::make('requisition_group_id')
            ->options(function (callable $get) {
                $subcategoryId = $get('requisition_subcategory_id');
                
                if ($subcategoryId) {
                    // Get the unique group_ids from the items table
                    $groupIds = Item::where('subcategory_id', $subcategoryId)
                        ->pluck('group_id')
                        ->unique(); // Get unique group_ids

                    // Fetch group names from the Group model using the unique group_ids
                    return Group::whereIn('id', $groupIds)
                        ->pluck('name', 'id') // Fetch group_name based on the group_id
                        ->toArray();
                }

                return []; // Return empty if no subcategory is selected
            })
            ->nullable() // Make this field optional
            ->label('Group'),

        TextInput::make('number_of_items')
            ->numeric()
            ->required()
            ->label('Number of Items'),
        // Items selection
Select::make('requisition_item_ids')
    ->multiple() 
    ->options(function (callable $get) {
        $categoryId = $get('requisition_category_id');
        $subcategoryId = $get('requisition_subcategory_id');
        return Item::where('category_id', $categoryId)
            ->where('subcategory_id', $subcategoryId)
            ->pluck('name', 'id');
    })
    ->reactive()
    ->label('Items'),
    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_name')->label('Event Name'),
                Tables\Columns\TextColumn::make('event_date')->label('Event Date'),
                Tables\Columns\TextColumn::make('event_location')->label('Location'),
                Tables\Columns\TextColumn::make('urgency')->label('Urgency'),
                Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->filters([
                // Add filters here if needed
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // You can add relationships here if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
