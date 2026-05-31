<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AnalyticsPageViewResource\Pages;
use App\Models\AnalyticsPageView;
use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use UnitEnum;

class AnalyticsPageViewResource extends Resource
{
    protected static ?string $model = AnalyticsPageView::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string|UnitEnum|null $navigationGroup = 'Վերլուծություն';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Բացված էջեր';

    protected static ?string $modelLabel = 'Էջի դիտում';

    protected static ?string $pluralModelLabel = 'Բացված էջեր';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('viewed_at')
                    ->label('Ժամանակ')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                TextColumn::make('path')
                    ->label('Բացված էջ')
                    ->searchable()
                    ->copyable()
                    ->limit(80)
                    ->tooltip(fn (AnalyticsPageView $record): ?string => $record->url ?: $record->path),

                TextColumn::make('title')
                    ->label('Վերնագիր')
                    ->searchable()
                    ->limit(45)
                    ->toggleable(),

                TextColumn::make('visitor_id')
                    ->label('Այցելու')
                    ->searchable()
                    ->copyable()
                    ->limit(12),

                TextColumn::make('session_id')
                    ->label('Սեսիա')
                    ->searchable()
                    ->copyable()
                    ->limit(12)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('locale')
                    ->label('Լեզու')
                    ->badge()
                    ->sortable(),

                TextColumn::make('device_type')
                    ->label('Սարք')
                    ->badge()
                    ->sortable(),

                TextColumn::make('browser')
                    ->label('Բրաուզեր')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('os')
                    ->label('OS')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('referrer_domain')
                    ->label('Աղբյուր')
                    ->searchable()
                    ->limit(30)
                    ->placeholder('Direct'),

                TextColumn::make('utm_source')
                    ->label('UTM source')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('utm_campaign')
                    ->label('UTM campaign')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('page_loaded_ms')
                    ->label('Load ms')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('time_on_page_seconds')
                    ->label('Ժամանակ էջում')
                    ->placeholder('Դեռ բաց է / չի փակվել')
                    ->formatStateUsing(fn ($state): string => $state ? $state . ' վրկ' : '—')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('locale')
                    ->label('Լեզու')
                    ->options([
                        'hy' => 'Հայերեն',
                        'ru' => 'Ռուսերեն',
                        'en' => 'English',
                    ]),

                SelectFilter::make('device_type')
                    ->label('Սարք')
                    ->options([
                        'desktop' => 'Desktop',
                        'mobile' => 'Mobile',
                        'tablet' => 'Tablet',
                        'bot' => 'Bot',
                    ]),

                SelectFilter::make('path')
                    ->label('Էջ')
                    ->searchable()
                    ->options(fn (): array => DatabaseSchema::hasTable('analytics_page_views')
                        ? AnalyticsPageView::query()
                            ->select('path')
                            ->whereNotNull('path')
                            ->groupBy('path')
                            ->orderByRaw('MAX(viewed_at) DESC')
                            ->limit(75)
                            ->pluck('path', 'path')
                            ->all()
                        : []),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Ջնջել'),
            ])
            ->defaultSort('viewed_at', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnalyticsPageViews::route('/'),
        ];
    }
}
