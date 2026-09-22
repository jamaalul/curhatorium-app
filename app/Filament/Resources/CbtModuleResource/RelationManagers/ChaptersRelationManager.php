<?php

namespace App\Filament\Resources\CbtModuleResource\RelationManagers;

use App\ChapterType;
use App\Models\Chapter;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\QuizQuestionType;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Unique;

class ChaptersRelationManager extends RelationManager
{
    protected static string $relationship = 'chapters';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->label('Tipe')
                    ->options([
                        ChapterType::Reading->value => 'Reading',
                        ChapterType::Video->value => 'Video',
                        ChapterType::Quiz->value => 'Quiz',
                    ])
                    ->required()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        if ($state !== ChapterType::Reading->value) {
                            $set('text_content', null);
                        }

                        if ($state !== ChapterType::Video->value) {
                            $set('video_url', null);
                        }

                        if ($state !== ChapterType::Quiz->value) {
                            $set('questions', []);
                        }
                    }),
                Forms\Components\RichEditor::make('text_content')
                    ->label('Konten Bacaan')
                    ->required(fn (Get $get): bool => $get('type') === ChapterType::Reading->value)
                    ->visible(fn (Get $get): bool => $get('type') === ChapterType::Reading->value)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('video_url')
                    ->label('URL Video')
                    ->required(fn (Get $get): bool => $get('type') === ChapterType::Video->value)
                    ->visible(fn (Get $get): bool => $get('type') === ChapterType::Video->value)
                    ->url()
                    ->maxLength(2048)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('order_number')
                    ->label('Urutan')
                    ->required()
                    ->integer()
                    ->minValue(1)
                    ->default(fn (RelationManager $livewire): int => ((int) $livewire->getOwnerRecord()->chapters()->withTrashed()->max('order_number')) + 1)
                    ->unique(
                        table: Chapter::class,
                        column: 'order_number',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, RelationManager $livewire): Unique => $rule->where(
                            'cbt_module_id',
                            $livewire->getOwnerRecord()->getKey(),
                        ),
                    ),
                Forms\Components\Repeater::make('questions')
                    ->label('Pertanyaan Quiz')
                    ->relationship()
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        if (isset($data['id'])) {
                            $data['accepted_answer'] = QuizQuestion::query()
                                ->whereKey($data['id'])
                                ->value('accepted_answer');
                        }

                        return $data;
                    })
                    ->schema([
                        Forms\Components\Textarea::make('question')
                            ->label('Pertanyaan')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Pertanyaan')
                            ->options([
                                QuizQuestionType::MultipleChoice->value => 'Multiple Choice',
                                QuizQuestionType::ShortAnswer->value => 'Short Answer',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if ($state === QuizQuestionType::MultipleChoice->value) {
                                    $set('accepted_answer', null);
                                }

                                if ($state === QuizQuestionType::ShortAnswer->value) {
                                    $set('options', []);
                                }
                            }),
                        Forms\Components\TextInput::make('accepted_answer')
                            ->label('Jawaban yang Diterima')
                            ->required(fn (Get $get): bool => $get('type') === QuizQuestionType::ShortAnswer->value)
                            ->visible(fn (Get $get): bool => $get('type') === QuizQuestionType::ShortAnswer->value)
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('points')
                            ->label('Poin')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->default(1),
                        Forms\Components\TextInput::make('order_number')
                            ->label('Urutan Pertanyaan')
                            ->required()
                            ->integer()
                            ->minValue(1)
                            ->distinct(),
                        Forms\Components\Repeater::make('options')
                            ->label('Pilihan Jawaban')
                            ->relationship()
                            ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                                if (isset($data['id'])) {
                                    $data['is_correct'] = (bool) QuizQuestionOption::query()
                                        ->whereKey($data['id'])
                                        ->value('is_correct');
                                }

                                return $data;
                            })
                            ->schema([
                                Forms\Components\Textarea::make('option_text')
                                    ->label('Teks Opsi')
                                    ->required(),
                                Forms\Components\Toggle::make('is_correct')
                                    ->label('Jawaban Benar')
                                    ->default(false),
                                Forms\Components\TextInput::make('order_number')
                                    ->label('Urutan Opsi')
                                    ->required()
                                    ->integer()
                                    ->minValue(1)
                                    ->distinct(),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Opsi')
                            ->collapsible()
                            ->required(fn (Get $get): bool => $get('type') === QuizQuestionType::MultipleChoice->value)
                            ->minItems(1)
                            ->visible(fn (Get $get): bool => $get('type') === QuizQuestionType::MultipleChoice->value)
                            ->rule(fn (Get $get): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                if ($get('type') !== QuizQuestionType::MultipleChoice->value) {
                                    return;
                                }

                                $hasCorrectOption = collect(is_array($value) ? $value : [])
                                    ->contains(fn (array $option): bool => (bool) ($option['is_correct'] ?? false));

                                if (! $hasCorrectOption) {
                                    $fail('Minimal satu opsi harus ditandai sebagai jawaban benar.');
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->defaultItems(0)
                    ->addActionLabel('Tambah Pertanyaan')
                    ->collapsible()
                    ->visible(fn (Get $get): bool => $get('type') === ChapterType::Quiz->value)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge(),
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Pertanyaan')
                    ->counts('questions'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make()
                    ->visible(fn (Chapter $record): bool => ! $record->questions()->withTrashed()->exists()
                        && ! $record->chapterProgresses()->exists()
                        && ! $record->quizAttempts()->exists()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('order_number')
            ->reorderable('order_number')
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    /** @param array<int|string> $order */
    public function reorderTable(array $order): void
    {
        $recordIds = array_values(array_unique($order));
        $ownerRecord = $this->getOwnerRecord();
        $validRecordCount = $ownerRecord->chapters()
            ->whereIn('id', $recordIds)
            ->count();

        abort_unless($validRecordCount === count($recordIds), 404);

        DB::transaction(function () use ($ownerRecord, $recordIds): void {
            $offset = ((int) $ownerRecord->chapters()->withTrashed()->max('order_number')) + count($recordIds) + 1;

            Chapter::query()
                ->where('cbt_module_id', $ownerRecord->getKey())
                ->whereIn('id', $recordIds)
                ->increment('order_number', $offset);

            foreach ($recordIds as $index => $recordId) {
                Chapter::query()
                    ->where('cbt_module_id', $ownerRecord->getKey())
                    ->whereKey($recordId)
                    ->update([
                        'order_number' => $index + 1,
                        'updated_at' => now(),
                    ]);
            }
        });
    }
}
