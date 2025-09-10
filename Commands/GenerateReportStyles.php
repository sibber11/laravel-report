<?php

namespace BlinkerBoy\Report\Commands;

use Illuminate\Console\Command;

class GenerateReportStyles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report-generator:styles {--w|watch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates report styles for the reports.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating report styles...');
        $tailwind_config = __DIR__ . '/../tailwind.config.js';
        $app_css = __DIR__ . '/../resources/css/app.css';
        $report_css = './public/build/css/report.css';
        if ($this->option('watch')) {
            $watch = '--watch';
        } else {
            $watch = '';
        }
        shell_exec("tailwindcss -c $tailwind_config -i $app_css -o $report_css $watch");
        $this->info('Report styles generated successfully.');
    }
}
