<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HomeSectionResource\Pages;
use App\Models\Category;
use App\Models\HomeSection;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HomeSectionResource extends Resource
{
    protected static ?string $model = HomeSection::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|UnitEnum|null $navigationGroup = 'Բովանդակություն';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Գլխավոր էջի բաժին';

    protected static ?string $pluralModelLabel = 'Գլխավոր էջի բաժիններ';

    protected static ?string $navigationLabel = 'Գլխավոր էջի բաժիններ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Կարգավորում')
                ->schema([
                    TextInput::make('key')
                        ->label('Ներքին անուն / key')
                        ->maxLength(255)
                        ->nullable()
                        ->helperText('Օրինակ՝ equestrian-line կամ custom-leather։'),

                    Select::make('type')
                        ->label('Տեսակ')
                        ->options([
                            'editorial' => 'Editorial / campaign',
                            'collection' => 'Հավաքածու',
                            'story' => 'Brand story',
                        ])
                        ->default('editorial')
                        ->required(),

                    Select::make('category_id')
                        ->label('Կապել կատեգորիայի հետ')
                        ->options(fn () => Category::query()->orderBy('sort_order')->pluck('name_hy', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable(),

                    Select::make('layout')
                        ->label('Դիզայն')
                        ->options([
                            'full_bleed' => 'Full screen image + text',
                            'split' => 'Նկար + տեքստ կողք կողքի',
                        ])
                        ->default('full_bleed')
                        ->required(),

                    Select::make('text_position')
                        ->label('Տեքստի դիրք')
                        ->options([
                            'bottom_center' => 'Ներքև կենտրոն',
                            'bottom_left' => 'Ներքև ձախ',
                            'center' => 'Կենտրոն',
                        ])
                        ->default('bottom_center')
                        ->required(),

                    Select::make('theme')
                        ->label('Տեքստի գույն')
                        ->options([
                            'dark' => 'Սպիտակ տեքստ մուգ նկարի վրա',
                            'light' => 'Մուգ տեքստ բաց ֆոնի վրա',
                        ])
                        ->default('dark')
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Ակտիվ է')
                        ->default(true),

                    TextInput::make('sort_order')
                        ->label('Հերթականություն')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])
                ->columns(3),

            Section::make('Մեդիա')
                ->schema([
                    FileUpload::make('image')
                        ->label('Desktop նկար')
                        ->disk('public')
                        ->directory('homepage/sections')
                        ->visibility('public')
                        ->image()
                        ->nullable(),

                    FileUpload::make('mobile_image')
                        ->label('Mobile նկար')
                        ->disk('public')
                        ->directory('homepage/sections/mobile')
                        ->visibility('public')
                        ->image()
                        ->nullable(),

                    FileUpload::make('video')
                        ->label('Վիդեո')
                        ->disk('public')
                        ->directory('homepage/sections/video')
                        ->visibility('public')
                        ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/quicktime', 'video/ogg'])
                        ->maxSize(204800) // 200 MB
                        ->nullable(),
                ])
                ->columns(3),

            Section::make('Կոճակ')
                ->schema([
                    TextInput::make('button_link')
                        ->label('Հղում')
                        ->maxLength(255)
                        ->nullable(),
                ]),

            Tabs::make('Թարգմանություններ')
                ->tabs([
                    Tab::make('Հայերեն')->schema([
                        TextInput::make('eyebrow_hy')->label('Փոքր վերնագիր'),
                        TextInput::make('title_hy')->label('Վերնագիր'),
                        Textarea::make('description_hy')->label('Նկարագրություն')->rows(4),
                        TextInput::make('button_text_hy')->label('Կոճակի տեքստ'),
                    ]),
                    Tab::make('Ռուսերեն')->schema([
                        TextInput::make('eyebrow_ru')->label('Փոքր վերնագիր'),
                        TextInput::make('title_ru')->label('Վերնագիր'),
                        Textarea::make('description_ru')->label('Նկարագրություն')->rows(4),
                        TextInput::make('button_text_ru')->label('Կոճակի տեքստ'),
                    ]),
                    Tab::make('Անգլերեն')->schema([
                        TextInput::make('eyebrow_en')->label('Փոքր վերնագիր'),
                        TextInput::make('title_en')->label('Վերնագիր'),
                        Textarea::make('description_en')->label('Նկարագրություն')->rows(4),
                        TextInput::make('button_text_en')->label('Կոճակի տեքստ'),
                    ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->label('Նկար')->disk('public')->square(),
                TextColumn::make('title_hy')->label('Վերնագիր')->searchable()->sortable(),
                TextColumn::make('type')->label('Տեսակ')->sortable(),
                TextColumn::make('sort_order')->label('Հերթ')->sortable(),
                IconColumn::make('is_active')->label('Ակտիվ')->boolean(),
            ])
            ->actions([
                EditAction::make()->label('Խմբագրել'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Ջնջել'),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeSections::route('/'),
            'create' => Pages\CreateHomeSection::route('/create'),
            'edit' => Pages\EditHomeSection::route('/{record}/edit'),
        ];
    }
}
