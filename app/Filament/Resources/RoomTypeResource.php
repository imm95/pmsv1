<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomTypeResource\Pages;
use App\Filament\Resources\RoomTypeResource\RelationManagers;
use App\Models\RoomType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components;
use App\Filament\Resources\RoomTypeResource\RelationManagers\RoomsRelationManager;

class RoomTypeResource extends Resource
{
    protected static ?string $model = RoomType::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                    
                Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                    
                Components\TextInput::make('base_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                    
                Components\TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                    
                Components\TagsInput::make('facilities')
                    ->placeholder('Add facility'),
                    
                Components\Select::make('bed_type')
                    ->options([
                        'twin' => 'Twin Bed',
                        'double' => 'Double Bed',
                        'queen' => 'Queen Bed',
                        'king' => 'King Bed',
                    ])
                    ->required(),
                    
                Components\TextInput::make('size')
                    ->numeric()
                    ->suffix('m²'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('base_price')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('capacity')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('bed_type')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bed_type')
                    ->options([
                        'twin' => 'Twin',
                        'double' => 'Double',
                        'queen' => 'Queen',
                        'king' => 'King',
                    ]),
                
            ])
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
           // Relations\RoomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoomTypes::route('/'),
            'create' => Pages\CreateRoomType::route('/create'),
            'edit' => Pages\EditRoomType::route('/{record}/edit'),
        ];
    }
}
