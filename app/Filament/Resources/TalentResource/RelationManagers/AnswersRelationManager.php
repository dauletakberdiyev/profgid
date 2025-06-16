<?php

namespace App\Filament\Resources\TalentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $recordTitleAttribute = 'question';
    
    protected static ?string $title = 'Вопросы';
    
    protected static ?string $label = 'Вопрос';
    
    protected static ?string $pluralLabel = 'Вопросы';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->label('Вопрос')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('value')
                    ->label('Значение')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\TextInput::make('order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label('Вопрос')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('value')
                    ->label('Значение')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Порядок')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order');
    }
}
