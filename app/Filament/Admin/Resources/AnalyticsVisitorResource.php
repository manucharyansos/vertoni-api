<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnalyticsVisitorResource\Pages;
use App\Models\AnalyticsVisitor;
use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AnalyticsVisitorResource extends Resource
{
    protected static ?string $model = AnalyticsVisitor::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Վերլուծություն';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Հաճախողներ';

    protected static ?string $modelLabel = 'Հաճախող';

    protected static ?string $pluralModelLabel = 'Հաճախողներ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visitor_id')->label('Visitor ID')->copyable()->searchable()->limit(16),
                TextColumn::make('first_seen_at')->label('Առաջին այց')->dateTime('Y-m-d H:i')->sortable(),
                TextColumn::make('last_seen_at')->label('Վերջին այց')->dateTime('Y-m-d H:i')->sortable(),
                TextColumn::make('visits_count')->label('Սեսիաներ')->sortable(),
                TextColumn::make('page_views_count')->label('Բացված էջեր')->sortable(),
                TextColumn::make('last_path')->label('Վերջին բացված էջ')->searchable()->copyable()->limit(75)->tooltip(fn (AnalyticsVisitor $record): ?string => $record->last_path),
                TextColumn::make('device_type')->label('Սարք')->badge()->sortable(),
                TextColumn::make('browser')->label('Բրաուզեր')->sortable(),
                TextColumn::make('os')->label('OS')->sortable(),
                TextColumn::make('locale')->label('Լեզու')->badge()->sortable(),
                TextColumn::make('last_referrer')->label('Վերջին աղբյուր')->limit(45)->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('device_type')->label('Սարք')->options([
                    'desktop' => 'Desktop',
                    'mobile' => 'Mobile',
                    'tablet' => 'Tablet',
                    'bot' => 'Bot',
                ]),
                SelectFilter::make('locale')->label('Լեզու')->options([
                    'hy' => 'Հայերեն',
                    'ru' => 'Ռուսերեն',
                    'en' => 'English',
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Ջնջել'),
            ])
            ->defaultSort('last_seen_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyticsVisitors::route('/'),
        ];
    }
}
