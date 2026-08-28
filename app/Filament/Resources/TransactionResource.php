<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TransactionResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'Transactions';

    protected static ?string $modelLabel = 'Transaction';

    protected static ?string $pluralModelLabel = 'Transactions';

    protected static ?string $recordTitleAttribute = 'order_ref';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $orders = DB::table('orders')
            ->select([
                'id',
                'order_ref',
                'user_id',
                'orderable_type',
                'orderable_id',
                'quantity',
                'unit_price',
                'gross_amount',
                'status',
                'expired_at',
                'created_at',
                'updated_at',
            ]);

        $fakeOrders = DB::table('fake_orders')
            ->select([
                'id',
                'order_ref',
                'user_id',
                'orderable_type',
                'orderable_id',
                'quantity',
                'unit_price',
                'gross_amount',
                'status',
                'expired_at',
                'created_at',
                'updated_at',
            ]);

        $union = $orders->unionAll($fakeOrders);

        return parent::getEloquentQuery()
            ->fromSub($union, 'orders');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_ref')
                    ->label('Order Ref')
                    ->disabled(),
                Forms\Components\TextInput::make('orderable_id')
                    ->label('Orderable ID')
                    ->disabled(),
                Forms\Components\TextInput::make('gross_amount')
                    ->label('Gross Amount')
                    ->disabled(),
                Forms\Components\TextInput::make('status')
                    ->label('Status')
                    ->disabled(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_ref')
                    ->label('Order Ref')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('orderable_id')
                    ->label('Orderable ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Gross Amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'expired', 'cancelled' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('orderable_type')
                    ->label('Orderable Type')
                    ->options(function () {
                        $orderTypes = DB::table('orders')->distinct()->pluck('orderable_type')->filter();
                        $fakeTypes = DB::table('fake_orders')->distinct()->pluck('orderable_type')->filter();

                        $allTypes = collect([
                            'App\Models\MembershipPlan',
                            'App\Models\Ebook',
                            'App\Models\CbtModule',
                            'App\Models\Consultation',
                            'App\Models\SgdGroup',
                        ])->merge($orderTypes)->merge($fakeTypes)->unique()->values();

                        return $allTypes->mapWithKeys(fn ($type) => [$type => class_basename($type)." ({$type})"])->toArray();
                    })
                    ->searchable(),
                Tables\Filters\Filter::make('orderable_id')
                    ->form([
                        Forms\Components\TextInput::make('orderable_id')
                            ->label('Orderable ID')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['orderable_id'] ?? null,
                            fn (Builder $query, $orderableId): Builder => $query->where('orderable_id', $orderableId),
                        );
                    }),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
