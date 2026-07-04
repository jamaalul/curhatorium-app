<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    private const MEDIA_IMAGE_FILE_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    private const MEDIA_VIDEO_FILE_TYPES = [
        'video/mp4',
        'video/quicktime',
        'video/webm',
    ];

    private const MEDIA_FILE_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'mp4',
        'mov',
        'webm',
    ];

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produk Marketplace';

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk Marketplace';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state): void {
                        if ((string) ($get('slug') ?? '') !== Str::slug((string) $old)) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    }),
                Forms\Components\Select::make('product_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->alphaDash()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(5)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->prefix('Rp')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(9999999999.99)
                    ->step(0.01),
                Forms\Components\Repeater::make('ecommerceLinks')
                    ->label('Link E-commerce')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('ecommerce_name')
                            ->label('E-commerce')
                            ->options([
                                'shopee' => 'Shopee',
                                'tokopedia' => 'Tokopedia',
                                'other' => 'Lainnya',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->maxLength(2048),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Link E-commerce')
                    ->collapsible()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published')
                    ->default(false),
                Forms\Components\Repeater::make('media')
                    ->label('Media Produk')
                    ->relationship()
                    ->schema(static::mediaUploadSchema())
                    ->orderColumn('order_number')
                    ->reorderableWithButtons()
                    ->addActionLabel('Tambah Media')
                    ->collapsible()
                    ->defaultItems(0)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ecommerce_links_count')
                    ->label('Jumlah Link')
                    ->counts('ecommerceLinks')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('product_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Status')
                    ->placeholder('Semua status')
                    ->trueLabel('Published')
                    ->falseLabel('Draft'),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\MediaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, Forms\Components\Component>
     */
    public static function mediaUploadSchema(): array
    {
        return [
            Forms\Components\Select::make('media_type')
                ->label('Tipe Media')
                ->options([
                    'image' => 'Foto',
                    'video' => 'Video',
                ])
                ->default('image')
                ->required()
                ->live()
                ->native(false),
            Forms\Components\FileUpload::make('media_url')
                ->label('File Media')
                ->disk('public')
                ->directory('product-media')
                ->visibility('public')
                ->acceptedFileTypes(fn (Get $get): array => static::acceptedMediaFileTypes($get('media_type')))
                ->rules(['mimes:'.implode(',', self::MEDIA_FILE_EXTENSIONS)])
                ->maxSize(51200)
                ->openable()
                ->downloadable()
                ->previewable()
                ->helperText('File disimpan di storage lokal. Tipe file harus sesuai dengan tipe media.')
                ->required()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function acceptedMediaFileTypes(?string $mediaType): array
    {
        return match ($mediaType) {
            'image' => self::MEDIA_IMAGE_FILE_TYPES,
            'video' => self::MEDIA_VIDEO_FILE_TYPES,
            default => [
                ...self::MEDIA_IMAGE_FILE_TYPES,
                ...self::MEDIA_VIDEO_FILE_TYPES,
            ],
        };
    }
}
