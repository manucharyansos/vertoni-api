<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BannerResource\Pages;
use App\Models\Banner;
use BackedEnum;
use UnitEnum;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;

use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

use Filament\Actions\EditAction;
use Filament\Actions\DeleteBulkAction;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-photo';

    protected static string|UnitEnum|null $navigationGroup = 'Բովանդակություն';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Հիմնական տվյալներ')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Մեդիա (նկար կամ վիդեո)')
                            ->disk('public')
                            ->directory('banners')
                            ->visibility('public')
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'image/gif',
                                'video/mp4',
                                'video/webm',
                                'video/ogg',
                                'video/quicktime',
                            ])
                            ->maxSize(51200) // 50 MB
                            ->openable()
                            ->downloadable()
                            ->previewable(true)
                            ->nullable()
                            ->helperText('Կարող եք ներբեռնել նկար կամ կարճ վիդեո, առավելագույնը՝ 50MB'),

                        TextInput::make('button_link')
                            ->label('Կոճակի հղում')
                            ->maxLength(255)
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

                Tabs::make('Թարգմանություններ')
                    ->tabs([
                        Tab::make('Հայերեն')
                            ->schema([
                                TextInput::make('title_hy')->label('Վերնագիր'),
                                Textarea::make('subtitle_hy')->label('Ենթավերնագիր')->rows(4),
                                TextInput::make('button_text_hy')->label('Կոճակի տեքստ'),
                            ]),

                        Tab::make('Ռուսերեն')
                            ->schema([
                                TextInput::make('title_ru')->label('Վերնագիր'),
                                Textarea::make('subtitle_ru')->label('Ենթավերնագիր')->rows(4),
                                TextInput::make('button_text_ru')->label('Կոճակի տեքստ'),
                            ]),

                        Tab::make('Անգլերեն')
                            ->schema([
                                TextInput::make('title_en')->label('Վերնագիր'),
                                Textarea::make('subtitle_en')->label('Ենթավերնագիր')->rows(4),
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
                ImageColumn::make('image')
                    ->label('Նկար')
                    ->disk('public')
                    ->square(),

                TextColumn::make('title_hy')
                    ->label('Վերնագիր')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('button_link')
                    ->label('Հղում')
                    ->limit(30),

                TextColumn::make('sort_order')
                    ->label('Դասավորություն')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Ակտիվ')
                    ->boolean(),

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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
