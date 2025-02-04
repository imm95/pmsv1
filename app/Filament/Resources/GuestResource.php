<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Guest;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\GuestResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\GuestResource\RelationManagers;

class GuestResource extends Resource
{
    protected static ?string $model = Guest::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->required()
                    ->maxLength(50),
                    
                Components\TextInput::make('last_name')
                    ->required()
                    ->maxLength(50),
                    
                Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                    
                Components\TextInput::make('phone')
                    ->tel()
                    ->required(),
                    
                Components\Textarea::make('address')
                    ->required(),
                    
                DatePicker::make('date_of_birth')
                    ->native(false),
                    
                Components\Select::make('id_type')
                    ->options([
                        'KTP' => 'KTP',
                        'SIM' => 'SIM',
                        'Passport' => 'Passport',
                    ]),
                    
                Components\TextInput::make('id_number'),
                    
                Components\Textarea::make('special_requests')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->getStateUsing(fn ($record) => $record->first_name.' '.$record->last_name)
                    ->searchable(['first_name', 'last_name']),
                    
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('phone'),
                    
                Tables\Columns\BadgeColumn::make('special_requests')
                    ->getStateUsing(fn ($record) => $record->special_requests ? 'Yes' : 'No')
                    ->colors(['success' => 'Yes', 'warning' => 'No'])
            ])
            ->filters([
                Tables\Filters\Filter::make('has_special_requests')
                    ->query(fn ($query) => $query->whereNotNull('special_requests')),
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
            'index' => Pages\ListGuests::route('/'),
            'create' => Pages\CreateGuest::route('/create'),
            'edit' => Pages\EditGuest::route('/{record}/edit'),
        ];
    }
}
