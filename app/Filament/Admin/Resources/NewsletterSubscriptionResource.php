<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsletterSubscriptionResource\Pages;
use App\Models\NewsletterSubscription;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NewsletterSubscriptionResource extends Resource
{
    protected static ?string $model = NewsletterSubscription::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-at-symbol';

    protected static string|UnitEnum|null $navigationGroup = 'Բովանդակություն';

    protected static ?string $navigationLabel = 'Բաժանորդագրություններ';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Բաժանորդ')
                ->schema([
                    TextInput::make('email')->label('Էլ. փոստ')->email()->required(),
                    TextInput::make('locale')->label('Լեզու')->maxLength(10),
                    TextInput::make('source')->label('Աղբյուր')->maxLength(100),
                    Select::make('status')
                        ->label('Կարգավիճակ')
                        ->options([
                            'active' => 'Ակտիվ',
                            'unsubscribed' => 'Չեղարկված',
                        ])
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('email')->label('Էլ. փոստ')->searchable()->sortable(),
                TextColumn::make('locale')->label('Լեզու')->badge()->sortable(),
                TextColumn::make('source')->label('Աղբյուր')->toggleable()->sortable(),
                TextColumn::make('status')->label('Կարգավիճակ')->badge()->sortable(),
                TextColumn::make('subscribed_at')->label('Բաժանորդագրվել է')->dateTime('Y-m-d H:i')->sortable(),
                TextColumn::make('created_at')->label('Ստեղծված է')->dateTime('Y-m-d H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Կարգավիճակ')
                    ->options([
                        'active' => 'Ակտիվ',
                        'unsubscribed' => 'Չեղարկված',
                    ]),
                Tables\Filters\SelectFilter::make('locale')
                    ->label('Լեզու')
                    ->options([
                        'hy' => 'Հայերեն',
                        'ru' => 'Русский',
                        'en' => 'English',
                    ]),
            ])
            ->actions([
                EditAction::make()->label('Բացել'),
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
            'index' => Pages\ListNewsletterSubscriptions::route('/'),
            'create' => Pages\CreateNewsletterSubscription::route('/create'),
            'edit' => Pages\EditNewsletterSubscription::route('/{record}/edit'),
        ];
    }
}
