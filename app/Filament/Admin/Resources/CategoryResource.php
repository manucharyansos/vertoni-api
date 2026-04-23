<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CategoryResource\Pages;
use App\Models\Category;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|UnitEnum|null $navigationGroup = 'Կատալոգ';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Կատեգորիա';

    protected static ?string $pluralModelLabel = 'Կատեգորիաներ';

    protected static ?string $navigationLabel = 'Կատեգորիաներ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('type')->default('catalog'),
            Hidden::make('menu_order')->default(0),

            Section::make('Հիմնական տվյալներ')
                ->schema([
                    Select::make('parent_id')
                        ->label('Ծնող կատեգորիա')
                        ->options(fn () => Category::query()
                            ->orderBy('sort_order')
                            ->pluck('name_hy', 'id'))
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->helperText('Եթե սա գլխավոր բաժին է, դատարկ թող'),

                    FileUpload::make('image')
                        ->label('Նկար')
                        ->disk('public')
                        ->directory('categories')
                        ->visibility('public')
                        ->image()
                        ->nullable(),

                    Toggle::make('is_active')
                        ->label('Ակտիվ է')
                        ->default(true),

                    TextInput::make('sort_order')
                        ->label('Դասավորություն')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])
                ->columns(2),

            Section::make('Գլխավոր էջում երևալու կարգավորումներ')
                ->description('Սա է կառավարում գլխավոր էջի 3 մեծ կատեգորիաները՝ նկարը, վերնագիրը և տեքստը։')
                ->schema([
                    Toggle::make('show_on_home')
                        ->label('Ցույց տալ գլխավոր էջում')
                        ->default(false),

                    TextInput::make('home_sort_order')
                        ->label('Գլխավոր էջի հերթականություն')
                        ->numeric()
                        ->default(0),

                    FileUpload::make('home_image')
                        ->label('Գլխավոր էջի նկար')
                        ->disk('public')
                        ->directory('homepage/categories')
                        ->visibility('public')
                        ->image()
                        ->nullable()
                        ->helperText('Եթե դատարկ թողնեք, կօգտագործվի կատեգորիայի հիմնական նկարը։'),
                ])
                ->columns(3),

            Tabs::make('Թարգմանություններ')
                ->tabs([
                    Tab::make('Հայերեն')
                        ->schema([
                            TextInput::make('name_hy')
                                ->label('Անվանում')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('slug_hy')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255),

                            Textarea::make('description_hy')
                                ->label('Նկարագրություն')
                                ->rows(4)
                                ->nullable(),

                            TextInput::make('home_title_hy')
                                ->label('Գլխավոր էջի վերնագիր')
                                ->maxLength(255)
                                ->nullable(),

                            Textarea::make('home_description_hy')
                                ->label('Գլխավոր էջի նկարագրություն')
                                ->rows(3)
                                ->nullable(),
                        ]),

                    Tab::make('Ռուսերեն')
                        ->schema([
                            TextInput::make('name_ru')
                                ->label('Անվանում')
                                ->maxLength(255)
                                ->nullable(),

                            TextInput::make('slug_ru')
                                ->label('Slug')
                                ->maxLength(255)
                                ->nullable(),

                            Textarea::make('description_ru')
                                ->label('Նկարագրություն')
                                ->rows(4)
                                ->nullable(),

                            TextInput::make('home_title_ru')
                                ->label('Գլխավոր էջի վերնագիր')
                                ->maxLength(255)
                                ->nullable(),

                            Textarea::make('home_description_ru')
                                ->label('Գլխավոր էջի նկարագրություն')
                                ->rows(3)
                                ->nullable(),
                        ]),

                    Tab::make('Անգլերեն')
                        ->schema([
                            TextInput::make('name_en')
                                ->label('Անվանում')
                                ->maxLength(255)
                                ->nullable(),

                            TextInput::make('slug_en')
                                ->label('Slug')
                                ->maxLength(255)
                                ->nullable(),

                            Textarea::make('description_en')
                                ->label('Նկարագրություն')
                                ->rows(4)
                                ->nullable(),

                            TextInput::make('home_title_en')
                                ->label('Գլխավոր էջի վերնագիր')
                                ->maxLength(255)
                                ->nullable(),

                            Textarea::make('home_description_en')
                                ->label('Գլխավոր էջի նկարագրություն')
                                ->rows(3)
                                ->nullable(),
                        ]),
                ])
                ->columnSpanFull(),

            Section::make('SEO')
                ->schema([
                    TextInput::make('meta_title_hy')
                        ->label('Meta title (հայերեն)')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('meta_title_ru')
                        ->label('Meta title (ռուսերեն)')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('meta_title_en')
                        ->label('Meta title (անգլերեն)')
                        ->maxLength(255)
                        ->nullable(),

                    Textarea::make('meta_description_hy')
                        ->label('Meta description (հայերեն)')
                        ->rows(3)
                        ->nullable(),

                    Textarea::make('meta_description_ru')
                        ->label('Meta description (ռուսերեն)')
                        ->rows(3)
                        ->nullable(),

                    Textarea::make('meta_description_en')
                        ->label('Meta description (անգլերեն)')
                        ->rows(3)
                        ->nullable(),
                ])
                ->columns(3)
                ->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Նկար')
                    ->disk('public')
                    ->square(),

                TextColumn::make('name_hy')
                    ->label('Անվանում')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name_hy')
                    ->label('Ծնող կատեգորիա')
                    ->placeholder('Գլխավոր բաժին')
                    ->searchable(),

                TextColumn::make('sort_order')
                    ->label('Դասավորություն')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Ակտիվ')
                    ->boolean(),

                IconColumn::make('show_on_home')
                    ->label('Գլխավոր')
                    ->boolean(),

                TextColumn::make('home_sort_order')
                    ->label('Գլխ. հերթ')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ստեղծված է')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
