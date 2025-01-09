<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Filament\Resources\ContactResource\RelationManagers;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = '문의 관리';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->maxLength(100)->disabled(),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->maxLength(11)->disabled(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255)->disabled(),
                Forms\Components\TextInput::make('urls')
                    ->maxLength(255)->disabled(),
                Forms\Components\TextInput::make('project_price')
                    ->maxLength(255)->disabled(),
                Forms\Components\TextInput::make('project_date')
                    ->maxLength(255)->disabled(),
                Forms\Components\Textarea::make('information')
                    ->columnSpanFull()->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('urls')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_price')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project_date')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('등록일')
                    ->date('m-d-y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->label('보기'),
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
            'index' => Pages\ListContacts::route('/'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }
}
