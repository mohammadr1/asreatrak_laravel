<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Services\NewsStatistics;

class ListNews extends ListRecords
{
    protected static string $resource = NewsResource::class;

    protected static string $view = 'filament.resources.news.list-news';

    public function applyTableFilters(): void
    {
        $this->getTableFiltersForm()->getState();

        parent::applyTableFilters();
    }

    public function getNewsStatistics(): array
    {
        abort_unless($this->canViewNewsStatistics(), 403);

        $statistics = app(NewsStatistics::class);

        return [
            'overview' => $statistics->summarize(static::getResource()::getEloquentQuery()),
            'filtered' => $statistics->summarize($this->getFilteredTableQuery()),
        ];
    }

    public function canViewNewsStatistics(): bool
    {
        return auth()->user()?->hasRole('Admin') === true
            && static::getResource()::canViewAny();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
