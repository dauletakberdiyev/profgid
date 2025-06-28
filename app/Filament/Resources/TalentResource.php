<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TalentResource\Pages;
use App\Filament\Resources\TalentResource\RelationManagers;
use App\Models\Talent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TalentResource extends Resource
{
    protected static ?string $model = Talent::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Таланты';

    protected static ?string $modelLabel = 'Талант';

    protected static ?string $pluralModelLabel = 'Таланты';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('talent_domain_id')
                    ->relationship('domain', 'name')
                    ->label('Домен таланта')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Название')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('icon')
                    ->label('Иконка')
                    ->helperText('Название иконки или URL')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Описание')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('short_description')
                    ->label('Краткое описание')
                    ->helperText('Краткое описание таланта (показывается всем пользователям)')
                    ->maxLength(500)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('advice')
                    ->label('Советы')
                    ->helperText('5 советов для человека с этим талантом (можно использовать HTML)')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain.name')
                ->label('Домен')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('advice')
                    ->label('Есть советы')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('answers_count')
                    ->label('Кол-во вопросов')
                    ->counts('answers')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Обновлено')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('domain')
                    ->label('Домен')
                    ->relationship('domain', 'name')
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
            RelationManagers\AnswersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTalent::route('/'),
            'create' => Pages\CreateTalent::route('/create'),
            'edit' => Pages\EditTalent::route('/{record}/edit'),
        ];
    }
}
