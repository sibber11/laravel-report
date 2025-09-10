<?php

namespace BlinkerBoy\Report\Commands;

use Illuminate\Console\Command;

class MakeReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report-generator:make {name} {--single}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new report class.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');

        if (!$name) {
            $name = $this->ask('Name of report');
        }

        $modelName = str_replace('Report', '', $name);

        if ($this->option('single')) {
            $stub = file_get_contents(__DIR__ . '/../stubs/report.php.stub');
        } else {
            $stub = file_get_contents(__DIR__ . '/../stubs/report-multiple.php.stub');

        }

        $stub = str_replace('{{ MODEL }}', $modelName, $stub);
        $stub = str_replace('{{ CLASS }}', $name, $stub);

        // check if the report directory exists
        $reportDir = config('report-generator.report-dir');
        if (!is_dir(app_path($reportDir))) {
            mkdir(app_path($reportDir));
        }

        $path = $reportDir . DIRECTORY_SEPARATOR . $name . '.php';
        $filename = app_path($path);

        if (file_exists($filename)) {
            $this->warn($name . ' already exists!');

            return;
        }

        file_put_contents(app_path($path), $stub);
        $this->info($name . ' created successfully!');
    }
}
