<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                 Select::make('reservations_id')
                    ->relationship('reservation', 'id')
                    ->required()
                    ->searchable(),
                
                Select::make('method')
                    ->options([
                        'cash' => 'Cash',
                        'edc' => 'Credit/Debit Card (EDC)',
                        'transfer' => 'Bank Transfer',
                        'ota' => 'OTA Payment'
                    ])
                    ->reactive()
                    ->required(),
                
                TextInput::make('amount')
                    ->numeric()
                    ->required(),
                
                FileUpload::make('payment_proof')
                    ->label('Bukti Pembayaran')
                    ->directory('payments')
                    ->visibility('private')
                    ->acceptedFileTypes(['image/*', 'application/pdf']),
                
                // Dynamic fields based on payment method
                Group::make()
                    ->schema(function (Forms\Get $get) {
                        $method = $get('method');
                        
                        return match ($method) {
                            'edc' => [
                                TextInput::make('card_last4')
                                    ->label('Last 4 Digits Card')
                                    ->mask(fn (TextInput\Mask $mask) => $mask->pattern('0000'))
                                    ->required(),
                                TextInput::make('reference_number')
                                    ->label('EDC Approval Code')
                                    ->required()
                            ],
                            'transfer' => [
                                TextInput::make('bank_name')
                                    ->required(),
                                TextInput::make('reference_number')
                                    ->label('Transfer Reference Number')
                                    ->required()
                            ],
                            'ota' => [
                                TextInput::make('ota_reference')
                                    ->label('OTA Booking Reference')
                                    ->required(),
                                Select::make('ota_provider')
                                    ->options([
                                        'agoda' => 'Agoda',
                                        'booking' => 'Booking.com',
                                        'expedia' => 'Expedia'
                                    ])
                            ],
                            default => []
                        };
                    }),
                
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed'
                    ])
                    ->required(),
                
                Textarea::make('notes')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reservations.id')
                    ->sortable(),
                BadgeColumn::make('method')
                    ->colors([
                        'cash' => 'success',
                        'edc' => 'primary',
                        'transfer' => 'warning',
                        'ota' => 'danger'
                    ]),
                TextColumn::make('amount')
                    ->money('IDR'),
                BadgeColumn::make('status')
                    ->colors([
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger'
                    ]),
                TextColumn::make('created_at')
                    ->dateTime()
            ])
            ->filters([
                SelectFilter::make('method')
                    ->options([
                        'cash' => 'Cash',
                        'edc' => 'EDC',
                        'transfer' => 'Transfer',
                        'ota' => 'OTA'
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed'
                    ])
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
    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
