<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Промокоды';

    protected static ?string $modelLabel = 'Промокод';

    protected static ?string $pluralModelLabel = 'Промокоды';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Информация о промокоде')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Код')
                            ->required()
                            ->length(6)
                            ->placeholder('123456')
                            ->helperText('Код должен состоять из 6 цифр')
                            ->default(fn() => PromoCode::generateCode())
                            ->unique(ignoreRecord: true)
                            ->rules(['regex:/^\d{6}$/'])
                            ->validationMessages([
                                'regex' => 'Код должен состоять из 6 цифр',
                                'length' => 'Код должен быть длиной 6 символов',
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Описание')
                            ->placeholder('Описание промокода...')
                            ->rows(3),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Активен')
                            ->default(true)
                            ->helperText('Неактивные промокоды нельзя использовать'),
                    ]),

                Forms\Components\Section::make('Информация об использовании')
                    ->schema([
                        Forms\Components\Toggle::make('is_used')
                            ->label('Использован')
                            ->disabled()
                            ->helperText('Автоматически устанавливается при использовании'),

                        Forms\Components\Select::make('used_by')
                            ->label('Использован пользователем')
                            ->relationship('usedBy', 'email')
                            ->disabled()
                            ->placeholder('Не использован'),

                        Forms\Components\DateTimePicker::make('used_at')
                            ->label('Дата использования')
                            ->disabled()
                            ->placeholder('Не использован'),
                    ])
                    ->visible(fn($record) => $record?->exists),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Код')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Код скопирован!')
                    ->weight('medium')
                    ->fontFamily('mono'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Описание')
                    ->searchable()
                    ->limit(50)
                    ->placeholder('Без описания'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Активен')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_used')
                    ->label('Использован')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->sortable(),

                Tables\Columns\TextColumn::make('usedBy.email')
                    ->label('Использован пользователем')
                    ->searchable()
                    ->placeholder('Не использован')
                    ->limit(30),

                Tables\Columns\TextColumn::make('used_at')
                    ->label('Дата использования')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Не использован')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Активность')
                    ->placeholder('Все')
                    ->trueLabel('Только активные')
                    ->falseLabel('Только неактивные'),

                Tables\Filters\TernaryFilter::make('is_used')
                    ->label('Использование')
                    ->placeholder('Все')
                    ->trueLabel('Только использованные')
                    ->falseLabel('Только неиспользованные'),

                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('activate')
                        ->label('Активировать')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Активировать промокоды')
                        ->modalDescription('Вы уверены, что хотите активировать выбранные промокоды?'),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Деактивировать')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Деактивировать промокоды')
                        ->modalDescription('Вы уверены, что хотите деактивировать выбранные промокоды?'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'view' => Pages\ViewPromoCode::route('/{record}'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->unused()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
