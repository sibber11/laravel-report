<?php

namespace BlinkerBoy\Report;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

abstract class Report
{
    protected array $columns = [];

    protected string $orientation = 'Landscape';

    protected bool $multi_table = true;

    protected array $tables = [];

    protected Builder|QueryBuilder $query;

    protected array $date;

    public function __construct()
    {
        $this->date = $this->getDateRange();
        \Illuminate\Support\Facades\View::share('name', $this->getTitle());
    }

    public function getDateRange(): array
    {
        // input date format: 2020-01-01 to 2020-01-31
        $range = explode(' to ', request()->input('date'));
        // if date is not provided, return empty array
        if ($range[0] == '') {
            return [];
        }
        // if only one date is provided, return array with same date
        if (count($range) == 1) {
            $range[] = $range[0];
        }

        return $range;
    }

    private function getTitle(): string
    {
        return __(str_replace(' Report', '', Str::headline(class_basename(static::class))));
    }

    public function getQuery(): Builder|QueryBuilder
    {
        return clone $this->query;
    }

    public function setQuery($query)
    {
        $this->query = $query;
    }

    public function generate(): View
    {
        if ($this->multi_table) {
            $this->buildQuery($this->date);
            $this->generateTables($this->date);

            $data = [
                'tables' => $this->tables,
            ];
        } else {
            $report = $this->getReport();
            $formattedReport = $this->format($report);
            $data = [
                'data' => $formattedReport,
                'columns' => $this->columns,
                'totals' => $this->totals($formattedReport),
                'orientation' => $this->orientation,
            ];
        }

        $arr = [];
        if (isset($this->date[0]))
            $arr['from'] = $this->date[0];

        if (isset($this->date[1]))
            $arr['to'] = $this->date[1];

        return view('report::reports', [
            ...$data,
            ...$arr
        ]);
    }

    public function buildQuery(array $date): void
    {
    }

    public function generateTables(array $date)
    {
    }

    public function getReport()
    {

    }

    public function format($report)
    {

    }

    public function totals($report)
    {

    }

    public function landscape(): static
    {
        $this->orientation = 'Landscape';

        return $this;
    }

    public function portrait(): static
    {
        $this->orientation = 'Portrait';

        return $this;
    }

    public function orientation(): string
    {
        return $this->orientation;
    }

    protected function addTable(TableBuilder $builder): void
    {
        $this->tables[] = $builder->build();
    }

    protected function getTypeName($name): string
    {
        return __(Str::headline(class_basename($name)));
    }

    protected function formatDate(Carbon|string|null $date)
    {
        if (empty($date)) {
            return '';
        }

        if (is_string($date)) {
            $date = \Illuminate\Support\Carbon::make($date);
        }

        return $date->format('d-m-Y');
    }
}
