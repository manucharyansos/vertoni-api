<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static string|UnitEnum|null $navigationGroup = 'Կատալոգ';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Ապրանքներ';

    protected static ?string $modelLabel = 'Ապրանք';

    protected static ?string $pluralModelLabel = 'Ապրանքներ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Հիմնական տվյալներ')
                ->schema([
                    Select::make('category_id')
                        ->label('Կատեգորիա')
                        ->options(Category::query()->orderBy('sort_order')->pluck('name_hy', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->live(),

                    Placeholder::make('category_attribute_hint')
                        ->label('Կատեգորիայի հուշումներ')
                        ->content(function (Get $get) {
                            $categoryId = $get('category_id');

                            if (! $categoryId) {
                                return 'Ընտրիր կատեգորիա՝ տեսնելու համար, թե այս ապրանքի համար ինչ դաշտեր են պետք լրացնել։';
                            }

                            $category = Category::query()->with('parent')->find($categoryId);
                            $schema = $category?->effective_attribute_schema ?? [];

                            if (empty($schema)) {
                                return 'Այս կատեգորիայի համար դեռ հատուկ պարամետրեր սահմանված չեն։';
                            }

                            return collect($schema)
                                ->map(fn ($item) => ($item['label'] ?? $item['key'] ?? 'Դաշտ') . ' (' . ($item['type'] ?? 'text') . ')')
                                ->implode(', ');
                        })
                        ->columnSpanFull(),

                    TextInput::make('sku')
                        ->label('Արտիկուլ')
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->nullable(),

                    TextInput::make('price')
                        ->label('Գին')
                        ->numeric()
                        ->nullable()
                        ->helperText('Հիմնական գին այն ապրանքների համար, որոնք տարբերակներ չունեն։'),

                    TextInput::make('old_price')
                        ->label('Հին գին')
                        ->numeric()
                        ->nullable(),

                    TextInput::make('stock')
                        ->label('Մնացորդ')
                        ->numeric()
                        ->default(0)
                        ->nullable()
                        ->helperText('Հիմնական մնացորդ այն ապրանքների համար, որոնք տարբերակներ չունեն։'),

                    Toggle::make('has_variants')
                        ->label('Ունի տարբերակներ / չափեր')
                        ->default(false)
                        ->live(),

                    FileUpload::make('main_image')
                        ->label('Հիմնական նկար')
                        ->disk('public')
                        ->directory('products')
                        ->visibility('public')
                        ->image()
                        ->nullable(),

                    Toggle::make('is_active')
                        ->label('Ակտիվ է')
                        ->default(true),

                    Toggle::make('is_featured')
                        ->label('Առանձնացված է')
                        ->default(false),

                    Toggle::make('show_on_home')
                        ->label('Ցույց տալ գլխավոր էջի Highlights բաժնում')
                        ->default(false),

                    TextInput::make('home_sort_order')
                        ->label('Գլխավոր էջի հերթականություն')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),

            Section::make('Բնութագրեր և առանձնահատկություններ')
                ->schema([
                    Repeater::make('specifications')
                        ->label('Բնութագրեր')
                        ->schema([
                            TextInput::make('key')->label('Բանալին')->required()->maxLength(100),
                            TextInput::make('label')->label('Անվանում')->required()->maxLength(255),
                            TextInput::make('value')->label('Արժեք')->required()->maxLength(255),
                        ])
                        ->columns(3)
                        ->collapsible()
                        ->defaultItems(0),

                    Repeater::make('highlights')
                        ->label('Առանձնահատկություններ')
                        ->simple(
                            TextInput::make('value')
                                ->label('Տեքստ')
                                ->required()
                                ->maxLength(255)
                        )
                        ->defaultItems(0),
                ]),

            Tabs::make('Թարգմանություններ')
                ->tabs([
                    Tab::make('Հայերեն')
                        ->schema([
                            TextInput::make('name_hy')->label('Անվանում')->required()->maxLength(255),
                            TextInput::make('slug_hy')->label('Slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                            Textarea::make('short_description_hy')->label('Կարճ նկարագրություն')->rows(3),
                            Textarea::make('description_hy')->label('Լրիվ նկարագրություն')->rows(6),
                            TextInput::make('meta_title_hy')->label('Meta title')->maxLength(255),
                            Textarea::make('meta_description_hy')->label('Meta description')->rows(3),
                        ]),

                    Tab::make('Ռուսերեն')
                        ->schema([
                            TextInput::make('name_ru')->label('Անվանում')->maxLength(255),
                            TextInput::make('slug_ru')->label('Slug')->maxLength(255)->unique(ignoreRecord: true),
                            Textarea::make('short_description_ru')->label('Կարճ նկարագրություն')->rows(3),
                            Textarea::make('description_ru')->label('Լրիվ նկարագրություն')->rows(6),
                            TextInput::make('meta_title_ru')->label('Meta title')->maxLength(255),
                            Textarea::make('meta_description_ru')->label('Meta description')->rows(3),
                        ]),

                    Tab::make('Անգլերեն')
                        ->schema([
                            TextInput::make('name_en')->label('Անվանում')->maxLength(255),
                            TextInput::make('slug_en')->label('Slug')->maxLength(255)->unique(ignoreRecord: true),
                            Textarea::make('short_description_en')->label('Կարճ նկարագրություն')->rows(3),
                            Textarea::make('description_en')->label('Լրիվ նկարագրություն')->rows(6),
                            TextInput::make('meta_title_en')->label('Meta title')->maxLength(255),
                            Textarea::make('meta_description_en')->label('Meta description')->rows(3),
                        ]),
                ])
                ->columnSpanFull(),

            Repeater::make('variants')
                ->label('Տարբերակներ')
                ->relationship()
                ->schema([
                    TextInput::make('size')->label('Չափ')->maxLength(100)->nullable(),
                    TextInput::make('color')->label('Գույն')->maxLength(100)->nullable(),
                    TextInput::make('sku')->label('Արտիկուլ')->maxLength(255)->nullable(),

                    FileUpload::make('image')
                        ->label('Նկար')
                        ->disk('public')
                        ->directory('products/variants')
                        ->visibility('public')
                        ->image()
                        ->nullable(),

                    TextInput::make('price')
                        ->label('Գին')
                        ->numeric()
                        ->nullable()
                        ->helperText('Եթե դատարկ թողնես, կարելի է օգտագործել հիմնական գինը։'),

                    TextInput::make('stock')
                        ->label('Մնացորդ')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Ակտիվ է')
                        ->default(true),

                    TextInput::make('sort_order')
                        ->label('Դասավորություն')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Repeater::make('attributes')
                        ->label('Լրացուցիչ հատկանիշներ')
                        ->schema([
                            TextInput::make('key')->label('Բանալին')->required()->maxLength(100),
                            TextInput::make('label')->label('Անվանում')->required()->maxLength(255),
                            TextInput::make('value')->label('Արժեք')->required()->maxLength(255),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->collapsible(),
                ])
                ->columns(2)
                ->collapsible()
                ->defaultItems(0)
                ->visible(fn (Get $get) => (bool) $get('has_variants')),

            Section::make('Նկարներ')
                ->schema([
                    Repeater::make('images')
                        ->label('Գալերիա')
                        ->relationship()
                        ->schema([
                            FileUpload::make('image')
                                ->label('Նկար')
                                ->disk('public')
                                ->directory('products/gallery')
                                ->visibility('public')
                                ->image()
                                ->required(),

                            TextInput::make('sort_order')
                                ->label('Դասավորություն')
                                ->numeric()
                                ->default(0)
                                ->required(),
                        ])
                        ->columns(2)
                        ->collapsible()
                        ->defaultItems(0),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('main_image')
                    ->label('Նկար')
                    ->disk('public')
                    ->square(),

                TextColumn::make('name_hy')
                    ->label('Անվանում')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name_hy')
                    ->label('Կատեգորիա')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Գին')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('Մնացորդ')
                    ->sortable(),

                IconColumn::make('has_variants')
                    ->label('Տարբերակներ')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Ակտիվ')
                    ->boolean(),

                IconColumn::make('is_featured')
                    ->label('Առանձնացված')
                    ->boolean(),

                IconColumn::make('show_on_home')
                    ->label('Գլխավոր')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Ստեղծված է')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Կատեգորիա')
                    ->options(Category::query()->orderBy('sort_order')->pluck('name_hy', 'id')),

                Tables\Filters\TernaryFilter::make('is_active')->label('Ակտիվ է'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Առանձնացված է'),
                Tables\Filters\TernaryFilter::make('show_on_home')->label('Գլխավոր էջում է'),
                Tables\Filters\TernaryFilter::make('has_variants')->label('Ունի տարբերակներ'),
            ])
            ->actions([
                EditAction::make()->label('Խմբագրել'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Ջնջել'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
