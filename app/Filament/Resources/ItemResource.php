<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItemResource\Pages;
use App\Filament\Resources\ItemResource\RelationManagers;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextArea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ImageColumn\Directory;


class ItemResource extends Resource
{
    protected static ?string $model = Item::class;
    protected static ?string $navigationLabel = 'Items';
    protected static ?string $navigationGroup = 'Inventory';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('name')->required(), 
            Select::make('category_id')->relationship('category', 'name')->required(),           
            Select::make('subcategory_id')->relationship('subcategory', 'name')->required(),            
            Select::make('group_id')->relationship('group', 'name')->nullable(),
            Select::make('unit')
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
            TextInput::make('quantity')->numeric()->required(),
            TextInput::make('serial_number'),
            TextInput::make('model')->nullable(),
            TextInput::make('flight_case_number')->nullable(),
            TextInput::make('remarks')->nullable(),
            FileUpload::make('image')
                ->label('Item Image')
                ->image()
                ->disk('public')
                ->directory('items')
                ->preserveFilenames()
                ->enableOpen()
                ->enableDownload(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('quantity')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('serial_number')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('category.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('subcategory.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('group.name')->sortable()->searchable(),
                ImageColumn::make('image')
                    ->label('Image')
                    ->size(50)
                    ->circular()
                    ->disk('public') // This is the disk where you save files
                    ->visibility('public'),  
            ])->defaultSort('id', 'desc')
            
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
