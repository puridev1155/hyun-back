<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Filament\Resources\SliderResource\RelationManagers;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;// Correct import
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Livewire\Livewire;

class SliderResource extends Resource
{
    protected static ?string $model = Slider::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = '슬라이더 관리'; // Custom label in Korean
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('title')
                ->label('제목')
                ->maxLength(255),
            Grid::make(1)
                ->schema([
                    FileUpload::make('slider_image')
                        ->imageEditor()
                        ->label('썸네일 30MB 미만')
                        ->disk('s3')
                        ->directory('slider-image')
                        //->preserveFilenames()
                        ->visibility('public')
                        ->default(fn ($record) => $record ? self::getThumbnailUrl($record) : null)
                        ->dehydrateStateUsing(fn ($state) => json_encode($state)),
                ]),
            Forms\Components\TextInput::make('url')
                ->label('사이트 URL 주소')
                ->placeholder('https://...')
                ->maxLength(255),
            RichEditor::make('content')
                ->fileAttachmentsDisk('s3')
                ->fileAttachmentsDirectory('attachments')
                ->fileAttachmentsVisibility('public')
                ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('title')
            ->label('제목')
            ->searchable(),
            Tables\Columns\TextColumn::make('url')
            ->label('url'),
            Tables\Columns\ImageColumn::make('slider_image')
                ->label('이미지')
                ->disk('s3')
                , // Adjust the width as needed
            Tables\Columns\TextColumn::make('updated_at')
            ->label('수정일')
            ->date('m-d-y'),
        ])
        ->filters([
            //
        ])
        ->actions([
            EditAction::make()
            ->label('수정'),
            DeleteAction::make()
            ->label('삭제'),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                ->label('선택 삭제'),
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
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }

    protected static function getThumbnailUrl($record)
    {
        if ($record && $record->slider_image) {
            $sliderImage = json_decode($record->slider_image, true);
            $imageKey = reset($sliderImage);
            return Storage::disk('s3')->url($imageKey);
        }
        return null;
    }
}
