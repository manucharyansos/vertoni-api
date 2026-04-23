<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CustomOrderResource\Pages;
use App\Models\CustomOrder;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomOrderResource extends Resource
{
    protected static ?string $model = CustomOrder::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Պատվերներ';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Անհատական պատվերներ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Հաճախորդ')
                ->schema([
                    TextInput::make('name')->label('Անուն')->disabled(),
                    TextInput::make('phone')->label('Հեռախոս')->disabled(),
                    TextInput::make('email')->label('Էլ. փոստ')->disabled(),
                    TextInput::make('preferred_contact_method')->label('Կապի նախընտրելի ձև')->disabled(),
                ])
                ->columns(2),

            Section::make('Պատվերի տվյալներ')
                ->schema([
                    TextInput::make('title')->label('Անվանում')->disabled(),
                    TextInput::make('quantity')->label('Քանակ')->disabled(),
                    TextInput::make('size')->label('Չափ')->disabled(),
                    TextInput::make('color')->label('Գույն')->disabled(),
                    TextInput::make('budget')->label('Բյուջե')->disabled(),
                    DatePicker::make('deadline')->label('Վերջնաժամկետ')->disabled(),
                    Textarea::make('description')->label('Նկարագրություն')->disabled()->rows(6),
                ])
                ->columns(2),

            Section::make('Կառավարում')
                ->schema([
                    Select::make('status')
                        ->label('Կարգավիճակ')
                        ->options([
                            'new' => 'Նոր',
                            'in_review' => 'Քննարկման մեջ',
                            'approved' => 'Հաստատված',
                            'rejected' => 'Մերժված',
                            'in_progress' => 'Ընթացքի մեջ',
                            'completed' => 'Ավարտված',
                            'cancelled' => 'Չեղարկված',
                        ])
                        ->required(),

                    Textarea::make('admin_note')
                        ->label('Ադմինի նշում')
                        ->rows(5),
                ]),

            Section::make('Կապված ապրանք')
                ->schema([
                    Placeholder::make('product_info')
                        ->label('Ապրանք')
                        ->content(fn (?CustomOrder $record): string => $record?->product?->name_hy ?? '—'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('Անուն')->searchable()->sortable(),
                TextColumn::make('phone')->label('Հեռախոս')->searchable(),
                TextColumn::make('product.name_hy')->label('Ապրանք')->searchable()->toggleable(),
                TextColumn::make('quantity')->label('Քանակ')->sortable(),
                TextColumn::make('status')->label('Կարգավիճակ')->badge()->sortable(),
                TextColumn::make('created_at')->label('Ստեղծված է')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Կարգավիճակ')
                    ->options([
                        'new' => 'Նոր',
                        'in_review' => 'Քննարկման մեջ',
                        'approved' => 'Հաստատված',
                        'rejected' => 'Մերժված',
                        'in_progress' => 'Ընթացքի մեջ',
                        'completed' => 'Ավարտված',
                        'cancelled' => 'Չեղարկված',
                    ]),
            ])
            ->actions([
                EditAction::make()->label('Բացել / թարմացնել'),
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
            'index' => Pages\ListCustomOrders::route('/'),
            'create' => Pages\CreateCustomOrder::route('/create'),
            'edit' => Pages\EditCustomOrder::route('/{record}/edit'),
        ];
    }
}
