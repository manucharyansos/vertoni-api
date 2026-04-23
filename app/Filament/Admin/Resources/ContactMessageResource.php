<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use BackedEnum;
use UnitEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|UnitEnum|null $navigationGroup = 'Բովանդակություն';

    protected static ?string $navigationLabel = 'Կոնտակտային հաղորդագրություններ';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Հաճախորդ')
                ->schema([
                    TextInput::make('name')->label('Անուն')->disabled(),
                    TextInput::make('phone')->label('Հեռախոս')->disabled(),
                    TextInput::make('email')->label('Էլ. փոստ')->disabled(),
                ])
                ->columns(2),

            Section::make('Հաղորդագրություն')
                ->schema([
                    Textarea::make('message')
                        ->label('Տեքստ')
                        ->rows(8)
                        ->disabled(),
                ]),

            Section::make('Կառավարում')
                ->schema([
                    Select::make('status')
                        ->label('Կարգավիճակ')
                        ->options([
                            'new' => 'Նոր',
                            'read' => 'Կարդացված',
                            'replied' => 'Պատասխանված',
                        ])
                        ->required(),

                    Textarea::make('admin_note')
                        ->label('Ադմինի նշում')
                        ->rows(5),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('Անուն')->searchable()->sortable(),
                TextColumn::make('phone')->label('Հեռախոս')->searchable()->toggleable(),
                TextColumn::make('email')->label('Էլ. փոստ')->searchable()->toggleable(),
                TextColumn::make('status')->label('Կարգավիճակ')->badge()->sortable(),
                TextColumn::make('created_at')->label('Ստեղծված է')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Կարգավիճակ')
                    ->options([
                        'new' => 'Նոր',
                        'read' => 'Կարդացված',
                        'replied' => 'Պատասխանված',
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
            'index' => Pages\ListContactMessages::route('/'),
            'create' => Pages\CreateContactMessage::route('/create'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
