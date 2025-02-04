<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Reservation;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Reservations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('guest_id')
                    ->relationship('guest', 'first_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('room_id')
                    ->relationship('room', 'room_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('check_in')
                    ->required()
                    ->live(),
                Forms\Components\DatePicker::make('check_out')
                    ->required()
                    ->live(),
                Forms\Components\Select::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('adults')
                    ->label('Number of Adults')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'partial' => 'Partial Payment',
                        'refunded' => 'Refunded',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('total_price')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inv')
                    ->badge(),
                Tables\Columns\TextColumn::make('guest.first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room.room_number'),
                Tables\Columns\TextColumn::make('check_in')
                    ->date(),
                Tables\Columns\TextColumn::make('check_out')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR', true)
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'confirmed' => 'Confirmed',
                        'pending' => 'Pending',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'partial' => 'Partial',
                        'refunded' => 'Refunded',
                    ])
                    ->query(fn (Builder $query) => $query),
                Tables\Filters\Filter::make('check_in')
                    ->form([
                        Forms\Components\DatePicker::make('check_in_from')->label('Check In From'),
                        Forms\Components\DatePicker::make('check_in_to')->label('Check In To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['check_in_from'], fn ($query, $date) => $query->whereDate('check_in', '>=', $date))
                            ->when($data['check_in_to'], fn ($query, $date) => $query->whereDate('check_in', '<=', $date));
                    }),
                Tables\Filters\Filter::make('check_out')
                    ->form([
                        Forms\Components\DatePicker::make('check_out_from')->label('Check Out From'),
                        Forms\Components\DatePicker::make('check_out_to')->label('Check Out To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['check_out_from'], fn ($query, $date) => $query->whereDate('check_out', '>=', $date))
                            ->when($data['check_out_to'], fn ($query, $date) => $query->whereDate('check_out', '<=', $date));
                    }),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
