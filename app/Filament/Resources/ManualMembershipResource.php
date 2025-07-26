<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManualMembershipResource\Pages;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserMembership;
use App\Models\MembershipTicket;
use App\Models\UserTicket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ManualMembershipResource extends Resource
{
    protected static ?string $model = UserMembership::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';
    
    protected static ?string $navigationLabel = 'Grant Membership';
    
    protected static ?string $modelLabel = 'Manual Membership Grant';
    
    protected static ?string $pluralModelLabel = 'Manual Membership Grants';

    protected static ?string $slug = 'manual-membership';

    public static function canCreate(): bool
    {
        return Auth::user()->is_admin ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // No editing allowed for granted memberships
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // No deletion allowed for granted memberships
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->is_admin ?? false;
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return Auth::user()->is_admin ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Grant Membership')
                    ->description('Grant membership to users after WhatsApp payment verification')
                    ->schema([
                        Forms\Components\TextInput::make('user_id')
                            ->label('User ID')
                            ->required()
                            ->numeric()
                            ->placeholder('Enter user ID...')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $user = User::find($state);
                                    if ($user) {
                                        $set('user_info', "{$user->username} (" . ($user->name ? $user->name : 'No name') . ") - {$user->email}");
                                    } else {
                                        $set('user_info', 'User not found');
                                    }
                                } else {
                                    $set('user_info', '');
                                }
                            }),

                        Forms\Components\Placeholder::make('user_info')
                            ->label('User Information')
                            ->content(function ($get) {
                                $userId = $get('user_id');
                                if (!$userId) return '';
                                
                                $user = User::find($userId);
                                if (!$user) return 'User not found';
                                
                                return "{$user->username} (" . ($user->name ? $user->name : 'No name') . ") - {$user->email}";
                            }),

                        Forms\Components\Select::make('membership_id')
                            ->label('Select Membership')
                            ->options(function () {
                                return Membership::where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->map(function ($name, $id) {
                                        $membership = Membership::find($id);
                                        return "{$name} - Rp" . number_format($membership->price, 0, ',', '.');
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $membership = Membership::find($state);
                                    if ($membership) {
                                        $duration = $membership->duration_days > 0 
                                            ? "{$membership->duration_days} days" 
                                            : 'One-time';
                                        $set('membership_info', "Price: Rp" . number_format($membership->price, 0, ',', '.') . " | Duration: {$duration}");
                                    }
                                } else {
                                    $set('membership_info', '');
                                }
                            }),

                        Forms\Components\Placeholder::make('membership_info')
                            ->label('Membership Details')
                            ->content(function ($get) {
                                $membershipId = $get('membership_id');
                                if (!$membershipId) return '';
                                
                                $membership = Membership::find($membershipId);
                                if (!$membership) return '';
                                
                                $duration = $membership->duration_days > 0 
                                    ? "{$membership->duration_days} days" 
                                    : 'One-time';
                                
                                return "Price: Rp" . number_format($membership->price, 0, ',', '.') . " | Duration: {$duration}";
                            }),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('membership.name')
                    ->label('Membership')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('started_at')
                    ->label('Granted At')
                    ->dateTime()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'danger' : 'success')
                    ->formatStateUsing(fn ($record) => $record->expires_at && $record->expires_at->isPast() ? 'Expired' : 'Active'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('membership')
                    ->relationship('membership', 'name')
                    ->label('Membership Type'),
                    
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Status')
                    ->queries(
                        true: fn ($query) => $query->where('expires_at', '>', now()),
                        false: fn ($query) => $query->where('expires_at', '<=', now()),
                        blank: fn ($query) => $query,
                    )
                    ->trueLabel('Active')
                    ->falseLabel('Expired'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed for this use case
            ])
            ->defaultSort('started_at', 'desc');
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
            'index' => Pages\ListManualMemberships::route('/'),
            'create' => Pages\CreateManualMembership::route('/create'),
            'view' => Pages\ViewManualMembership::route('/{record}'),
        ];
    }
} 