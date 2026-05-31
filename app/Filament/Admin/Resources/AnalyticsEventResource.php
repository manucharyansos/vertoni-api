<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnalyticsEventResource\Pages;
use App\Models\AnalyticsEvent;
use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use UnitEnum;

class AnalyticsEventResource extends Resource
{
    protected static ?string $model = AnalyticsEvent::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cursor-arrow-rays';

    protected static string|UnitEnum|null $navigationGroup = 'Վերլուծություն';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Գործողություններ';

    protected static ?string $modelLabel = 'Գործողություն';

    protected static ?string $pluralModelLabel = 'Գործողություններ';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')->label('Ժամանակ')->dateTime('Y-m-d H:i:s')->sortable(),
                TextColumn::make('event_name')->label('Event')->badge()->searchable()->sortable(),
                TextColumn::make('event_label')->label('Նշում')->searchable()->limit(45),
                TextColumn::make('path')->label('Էջ')->searchable()->limit(55),
                TextColumn::make('visitor_id')->label('Այցելու')->searchable()->copyable()->limit(12),
                TextColumn::make('session_id')->label('Սեսիա')->searchable()->copyable()->limit(12)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payload')
                            ->label('Տվյալներ')
                            ->formatStateUsing(fn ($state): string => is_array($state) ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string) $state)
                            ->limit(80)
                            ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('event_name')
                    ->label('Event')
                    ->searchable()
                    ->options(fn (): array => DatabaseSchema::hasTable('analytics_events')
                        ? AnalyticsEvent::query()
                            ->select('event_name')
                            ->groupBy('event_name')
                            ->orderBy('event_name')
                            ->pluck('event_name', 'event_name')
                            ->all()
                        : []),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Ջնջել'),
            ])
            ->defaultSort('occurred_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyticsEvents::route('/'),
        ];
    }
}
