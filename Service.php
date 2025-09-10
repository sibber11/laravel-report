<?php

namespace BlinkerBoy\Report;

use Illuminate\Support\Arr;

class Service
{
    protected Report $instance;

    /**
     * register names of the reports
     */
    protected array $types;

    public function __construct()
    {
    }

    public function registerReports(): void
    {
        $reports_file = base_path('routes/reports.php');

        if (!file_exists($reports_file) ) {
            if (app()->runningInConsole())
                return;
            throw new \Exception("Reports file $reports_file not found. Did you publish the package?");
        }

        require $reports_file;
    }

    public function getTypes(): array
    {
        return array_filter($this->types, fn($item) => $item->included());
    }

    public function generate(string $type)
    {
        // if the type is not registered, and app is not in production, throw an exception
        if (!in_array($type, $this->getTypeNames())) {
            if (app()->isProduction()) {
                abort(404, "Report type not found");
            }
            throw new \Exception("Invalid report type ($type). Did you register it?");
        }

        $class = $this->getType($type)
            ->getClass();
        $this->instance = new $class();

        // call the generate method
        return $this->instance->generate();
    }

    public function getTypeNames(): array
    {
        return array_map(fn($type) => $type->getName(), $this->types);
    }

    public function getPermittedNames(): array
    {
        // array values is required so array index is recreated
//        return array_values(array_filter($this->getTypeNames(), fn($type) => auth()->user()->can('report ' . $type)));
        return array_values($this->getTypeNames());
    }

    public function getType($type): Type
    {
        return Arr::first($this->types, fn($item) => $item->getName() == $type);
    }

    public function addReport(Type $object): Type
    {
        $this->types[] = $object;
        return $object;
    }
}
