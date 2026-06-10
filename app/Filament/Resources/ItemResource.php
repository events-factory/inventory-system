<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Inventory';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Item Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Item Name')
                        ->required(),
                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('subcategory_id')
                        ->label('Subcategory')
                        ->relationship('subcategory', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('group_id')
                        ->label('Group (Optional)')
                        ->relationship('group', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('model')
                        ->label('Model (Optional)'),
                    Forms\Components\TextInput::make('serial_number')
                        ->label('Serial Number (Optional)'),
                    Forms\Components\Select::make('unit')
                        ->label('Unit')
                        ->options([
                            'Kg' => 'Kg',
                            'Cartons' => 'Cartons',
                            'PC' => 'PC',
                            'L' => 'L',
                            'M' => 'M',
                            'Sqm' => 'Sqm',
                        ])
                        ->default('PC')
                        ->required(),
                    Forms\Components\TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric()
                        ->default(1)
                        ->required(),
                    Forms\Components\TextInput::make('flight_case')
                        ->label('Flight Case (Optional)'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'available' => 'Available',
                            'damaged' => 'Damaged',
                            'lost' => 'Lost',
                        ])
                        ->default('available')
                        ->required(),
                    Forms\Components\TextInput::make('remarks')
                        ->label('Remarks'),
                    Forms\Components\FileUpload::make('image')
                        ->label('Item Image')
                        ->image()
                        ->directory('items'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['category', 'subcategory']))
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('image')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('model')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable(),
                TextColumn::make('subcategory.name')->label('Subcategory')->sortable(),
                TextColumn::make('quantity')->sortable(),
                TextColumn::make('unit')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'damaged' => 'warning',
                        'lost' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('subcategory')
                    ->relationship('subcategory', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if ($user instanceof User && $user->hasRole('storekeeper')) {
            return false;
        }
        return true;
    }
}
