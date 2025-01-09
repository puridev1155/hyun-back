<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\DateColumn;
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
use Filament\Forms\Components\DateTimePicker;
use Livewire\Livewire;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static ?string $navigationLabel = '프로잭트 관리'; // Custom label in Korean
    protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)
                ->schema([
                    Select::make('category_id')
                            ->label('프로젝트 카테고리')
                            ->options(Category::whereNotIn('category_name', ['소개', '질문'])->get()->pluck('category_name', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('category_name')
                                    ->label('New Category Name')
                                    ->required(),
                            ])
                            ->afterStateUpdated(function (Select $component, $state) {
                                if ($state === 'create-new') {
                                    $component->openModal('createCategoryModal');
                                }
                            })
                            ->createOptionUsing(function ($data) {
                                return Category::create(['category_name' => $data['category_name']])->id;
                            }),
                    TextInput::make('title')
                    ->label('제목')
                        ->maxLength(255),
                ]),
                Grid::make(2)
                ->schema([
                    TextInput::make('years')
                    ->label('년도 (예시 : 2024)')
                    ->numeric()
                    ->maxLength(50),
                    TextInput::make('clients')
                        ->label('클라이언트')
                        ->maxLength(50),
                    TextInput::make('specs')
                        ->label('사양')
                        ->maxLength(50),
                    TextInput::make('dates')
                        ->label('작업일')
                        ->maxLength(50),
                ]),
                Grid::make(1)
                ->schema([
                    FileUpload::make('post_thumbnail')
                        ->imageEditor()
                        ->label('썸네일 이미지 30MB 미만, 크기 : 600x600px')
                        ->disk('s3')
                        ->directory('post-thumbnail')
                        //->preserveFilenames()
                        ->visibility('private')
                        ->default(fn ($record) => $record ? self::getThumbnailUrl($record) : null)
                        ->dehydrateStateUsing(fn ($state) => json_encode($state)),
                ]),
                RichEditor::make('내용')
                ->fileAttachmentsDisk('s3')
                ->fileAttachmentsDirectory('attachments')
                ->fileAttachmentsVisibility('public')
                ->columnSpanFull(),
                Grid::make(1)
                ->schema([
                    FileUpload::make('post_images')
                        ->helperText('여러 파일 등록 가능')
                        ->label('파일')
                        ->disk('s3')
                        ->directory('post-images')
                        ->multiple()
                        //->preserveFilenames()
                        ->visibility('public')
                        ->default(fn ($record) => $record ? json_decode($record->post_images, true) : [])
                        ->dehydrateStateUsing(fn ($state) => is_array($state) ? json_encode($state) : $state),
                ]),
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->hidden() // Hide the field
                    ->disabled(), // Make the field read-only
                DateTimePicker::make('updated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.category_name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->width('50px'),
                Tables\Columns\ImageColumn::make('post_thumbnail')
                    ->label('이미지')
                    ->disk('s3')
                    ->width('40px'), // Adjust the width as needed
                Tables\Columns\TextColumn::make('title')
                    ->label('제목')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('수정일')
                    ->date('m-d-y'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(Category::all()->pluck('category_name', 'id')->toArray())
                    ->searchable(),
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
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    protected static function getThumbnailUrl($record)
    {
        if ($record && $record->post_thumbnail) {
            $postThumbnail = json_decode($record->post_thumbnail, true);
            $imageKey = reset($postThumbnail);
            return Storage::disk('s3')->url($imageKey);
        }
        return null;
    }
}
