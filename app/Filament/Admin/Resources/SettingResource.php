<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SettingResource\Pages;
use App\Models\Setting;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|UnitEnum|null $navigationGroup = 'Կարգավորումներ';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Կարգավորում';

    protected static ?string $pluralModelLabel = 'Կարգավորումներ';

    protected static ?string $navigationLabel = 'Կայքի կարգավորումներ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Կայքի public կարգավորումներ')
                ->description('Օրինակ՝ phone, email, instagram_url, facebook_url, country_label, customer_service_text։ Դրանք կարող են երևալ footer/header/service բաժիններում։')
                ->schema([
                    TextInput::make('key')
                        ->label('Key')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('label')
                        ->label('Admin label')
                        ->maxLength(255)
                        ->nullable(),

                    Select::make('group')
                        ->label('Խումբ')
                        ->options([
                            'site' => 'Site',
                            'contact' => 'Contact',
                            'social' => 'Social',
                            'customer_care' => 'Customer care',
                            'footer' => 'Footer',
                        ])
                        ->default('site')
                        ->required(),

                    Select::make('type')
                        ->label('Տեսակ')
                        ->options([
                            'text' => 'Text',
                            'url' => 'URL',
                            'email' => 'Email',
                            'phone' => 'Phone',
                            'textarea' => 'Textarea',
                        ])
                        ->default('text')
                        ->required(),

                    Textarea::make('value')
                        ->label('Արժեք')
                        ->rows(4)
                        ->columnSpanFull(),

                    Toggle::make('is_public')
                        ->label('Public API-ում երևա')
                        ->default(true),

                    TextInput::make('sort_order')
                        ->label('Հերթականություն')
                        ->numeric()
                        ->default(0)
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')->label('Key')->searchable()->sortable(),
                TextColumn::make('group')->label('Խումբ')->sortable(),
                TextColumn::make('label')->label('Անվանում')->searchable(),
                TextColumn::make('value')->label('Արժեք')->limit(40),
                IconColumn::make('is_public')->label('Public')->boolean(),
                TextColumn::make('sort_order')->label('Հերթ')->sortable(),
            ])
            ->actions([
                EditAction::make()->label('Խմբագրել'),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Ջնջել'),
            ])
            ->defaultSort('group');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
