# Laravel Report Generator

A comprehensive reporting system for Laravel applications that provides an easy way to create, manage, and display various types of reports with customizable filters, tables, and export capabilities.

## Table of Contents

- [Installation](#installation)
- [Quick Start](#quick-start)
- [Creating Reports](#creating-reports)
- [Report Configuration](#report-configuration)
- [Report Types and Methods](#report-types-and-methods)
- [Table Builder](#table-builder)
- [Frontend Integration](#frontend-integration)
- [Available Commands](#available-commands)
- [Examples](#examples)
- [Customization](#customization)
- [Troubleshooting](#troubleshooting)

## Installation

### 1. Install Package

```bash
composer require blinkerboy/report-generator
```
### 1(custom). Install without composer
on your project root, clone the repository and run the following commands
```bash
mkdir packages
cd packages
mkdir blinkerboy
cd blinkerboy
git clone https://github.com/blinkerboy/report-generator.git
```
then add the following to your `composer.json` file
```json
"autoload": {
        //... your other autoloads
            "BlinkerBoy\\Report\\": "packages/blinkerboy/src/Report/"
        //... your other autoloads
    },
```
register the service provider in your `config/app.php` file
```php
'providers' => [
    //... your other providers
    BlinkerBoy\Report\ReportServiceProvider::class,
    //... your other providers
],
```

### 2. Publish Assets and Configuration

```bash
# Publish service provider and config
php artisan vendor:publish --provider="BlinkerBoy\Report\ReportServiceProvider"

# Publish views and frontend assets
php artisan vendor:publish --provider="BlinkerBoy\Report\ReportServiceProvider" --tag=views

# Generate report styles (optional)
php artisan report-generator:styles
```

### 3. Configure Vite (if using Vite)

Add the report CSS to your `vite.config.js`:

```javascript
export default defineConfig({
  build: {
    rollupOptions: {
      input: {
        // ... your other inputs
        'resources/css/report.css': './resources/css/report.css',
      }
    }
  }
});
```

### 4. Configure Routes

The package automatically registers routes at `/reports`. Make sure your application has authentication middleware for the reports routes.

## Quick Start

### 1. Create a Simple Report

```bash
php artisan report-generator:make SalesReport
```

### 2. Register the Report

In `routes/reports.php`:

```php
use App\Reports\SalesReport;

ReportType::name('sales', SalesReport::class);
```

### 3. Implement the Report

```php
<?php

namespace App\Reports;

use App\Models\Sale;
use BlinkerBoy\Report\Report;
use BlinkerBoy\Report\TableBuilder;

class SalesReport extends Report
{
    public function getReport()
    {
        return Sale::whereDate('created_at', '>=', $this->date[0])
                  ->whereDate('created_at', '<=', $this->date[1])
                  ->get();
    }

    public function generateTables(array $date)
    {
        $table = new TableBuilder('Sales Report', true);

        $table->setHeaders([
            'date' => 'Date',
            'total' => 'Total Amount',
            'status' => 'Status'
        ]);

        foreach ($this->getReport() as $sale) {
            $table->addRow([
                'date' => $this->formatDate($sale->created_at),
                'total' => $sale->total,
                'status' => $sale->status
            ]);
        }

        $table->autoSum(['total'], 'date');
        $this->addTable($table);
    }
}
```

## Creating Reports

### Using Artisan Command

```bash
# Create a single-table report
php artisan report-generator:make SalesReport

# Create a multi-table report
php artisan report-generator:make SalesReport --single
```

### Manual Creation

Create a new class in `app/Reports/` that extends `BlinkerBoy\Report\Report`:

```php
<?php

namespace App\Reports;

use BlinkerBoy\Report\Report;

class CustomReport extends Report
{
    // Implement required methods
}
```

## Report Configuration

### Basic Registration

In `routes/reports.php`:

```php
use App\Reports\SalesReport;

ReportType::name('sales', SalesReport::class);
```

### Advanced Configuration

```php
// Basic report
ReportType::name('sales', SalesReport::class);

// Report with filters
ReportType::name('sales_by_product', SalesReport::class)
    ->select('Product');

// Report with hidden parameters
ReportType::name('due_sales', SalesReport::class)
    ->hidden('has_due', true);

// Report with multiple filters
ReportType::name('advanced_sales', SalesReport::class)
    ->select('Product')
    ->select('Staff')
    ->activeable();

// Exclude from menu
ReportType::name('internal', InternalReport::class)
    ->exclude();
```

### Filter Options

#### Select Filter
```php
->select('label', 'model_name', 'placeholder', required, multiple, options, remote_route)
```

#### Active/Inactive Filter
```php
->activeable(required)
```

#### Hidden Parameters
```php
->hidden('parameter_name', 'value')
```

#### Date Configuration
```php
->noDateRange()     // Remove date range picker
->singleDate()      // Use single date picker
->dateNotRequired() // Make date optional
```

## Report Types and Methods

### Base Report Class Methods

#### Data Retrieval
- `getReport()` - Return the main dataset
- `buildQuery(array $date)` - Build the base query
- `generateTables(array $date)` - Generate table structures

#### Formatting
- `format($report)` - Format report data for single-table reports
- `totals($report)` - Calculate totals for single-table reports

#### Configuration
- `landscape()` / `portrait()` - Set page orientation
- `orientation()` - Get current orientation

#### Utility Methods
- `formatDate($date)` - Format dates consistently
- `getTypeName($name)` - Get translated type names

### Table Builder Methods

#### Basic Setup
```php
$table = new TableBuilder('Table Title', true); // true for serial numbers
```

#### Headers
```php
// Simple headers
$table->setHeaders([
    'name' => 'Name',
    'email' => 'Email',
    'total' => 'Total'
]);

// Complex headers with rowspan/colspan
$table->setHeaders([
    'user' => [
        'value' => 'User Information',
        'data' => [
            'name' => 'Name',
            'email' => 'Email'
        ]
    ],
    'total' => 'Total'
], [], 2); // 2 = rowspan
```

#### Rows
```php
$table->addRow([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'total' => 150.00
]);
```

#### Totals and Summation
```php
// Auto-sum columns
$table->autoSum(['total', 'quantity'], 'date');

// Manual totals
$table->setTotals([
    'total' => 'Grand Total',
    'quantity' => 150,
    'amount' => 2500.00
]);
```

#### Styling
```php
$table->setHeaderClass('bg-gray-100 font-bold');
$table->setRowClass('hover:bg-gray-50');
$table->setTableClass('border-collapse');
```

## Frontend Integration

### Vue Components

The package provides Vue components for the frontend:

- `Reports/Index.vue` - List all available reports
- `Reports/Fields.vue` - Report configuration form
- `Reports/Report.vue` - Report generation interface

### Accessing Reports

```php
// Get all reports
GET /reports

// View report form
GET /reports/{type}

// Generate report
POST /reports/{type}/show
```

### Form Parameters

```javascript
{
  date: "2024-01-01 to 2024-01-31",  // Date range
  "from-date": "2024-01-01",          // From date
  "to-date": "2024-01-31",            // To date
  product_id: 123,                    // Selected product
  staff_id: 456,                      // Selected staff
  // ... other filters
}
```

## Available Commands

### Create Report
```bash
php artisan report-generator:make {name} [--single]
```

### Generate Styles
```bash
php artisan report-generator:styles [--watch]
```

## Examples

### Sales Report with Filters

```php
<?php

namespace App\Reports;

use App\Models\Sale;
use BlinkerBoy\Report\Report;
use BlinkerBoy\Report\TableBuilder;

class SalesReport extends Report
{
    public function getReport()
    {
        return Sale::query()
            ->when(request('product_id'), function($query) {
                $query->whereHas('details', function($q) {
                    $q->where('product_id', request('product_id'));
                });
            })
            ->when(request('staff_id'), function($query) {
                $query->where('sold_by', request('staff_id'));
            })
            ->whereDate('sale_date', '>=', $this->date[0])
            ->whereDate('sale_date', '<=', $this->date[1])
            ->with(['soldBy', 'paymentMethod'])
            ->get();
    }

    public function generateTables(array $date)
    {
        $table = new TableBuilder('Sales Report', true);

        $table->setHeaders([
            'date' => 'Date',
            'invoice' => 'Invoice',
            'customer' => 'Customer',
            'total' => 'Total',
            'paid' => 'Paid',
            'due' => 'Due',
            'method' => 'Payment Method',
            'staff' => 'Staff'
        ]);

        foreach ($this->getReport() as $sale) {
            $table->addRow([
                'date' => $this->formatDate($sale->sale_date),
                'invoice' => $sale->id,
                'customer' => $sale->customer?->name ?? 'Walk-in',
                'total' => $sale->grand_total,
                'paid' => $sale->paid_amount,
                'due' => $sale->due_amount,
                'method' => $sale->paymentMethod?->name,
                'staff' => $sale->soldBy?->name
            ]);
        }

        $table->autoSum(['total', 'paid', 'due'], 'date');
        $this->addTable($table);
    }
}
```

### Product-wise Report

```php
<?php

namespace App\Reports;

use App\Models\SaleDetails;
use BlinkerBoy\Report\Report;
use BlinkerBoy\Report\TableBuilder;
use Illuminate\Support\Facades\DB;

class ProductReport extends Report
{
    public function getReport()
    {
        return SaleDetails::query()
            ->select([
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(total_amount) as total_amount'),
                'product_name',
                DB::raw('DATE(created_at) as sale_date')
            ])
            ->whereDate('created_at', '>=', $this->date[0])
            ->whereDate('created_at', '<=', $this->date[1])
            ->when(request('product_id'), function($query) {
                $query->whereHas('outletStock', function($q) {
                    $q->where('product_id', request('product_id'));
                });
            })
            ->groupBy('sale_date', 'product_name')
            ->get();
    }

    public function generateTables(array $date)
    {
        $table = new TableBuilder('Product-wise Sales', true);

        $table->setHeaders([
            'date' => 'Date',
            'product' => 'Product',
            'quantity' => 'Quantity',
            'amount' => 'Amount'
        ]);

        foreach ($this->getReport() as $item) {
            $table->addRow([
                'date' => $this->formatDate($item->sale_date),
                'product' => $item->product_name,
                'quantity' => $item->total_qty,
                'amount' => $item->total_amount
            ]);
        }

        $table->autoSum(['quantity', 'amount'], 'date');
        $this->addTable($table);
    }
}
```

## Customization

### Custom CSS Classes

```php
// In your report class
$table->setHeaderClass('custom-header-class');
$table->setRowClass('custom-row-class');
$table->setTableClass('custom-table-class');
```

### Custom Date Formatting

```php
protected function formatDate(Carbon|string|null $date)
{
    if (empty($date)) {
        return '';
    }

    if (is_string($date)) {
        $date = Carbon::make($date);
    }

    return $date->format('d/m/Y'); // Custom format
}
```

### Custom Report Layout

Create custom views in `resources/views/vendor/report/`:

```php
// Custom layout
@extends('layouts.app')

@section('content')
    <div class="custom-report-wrapper">
        @yield('report-content')
    </div>
@endsection
```

### Custom Table Component

```php
// In resources/views/vendor/report/components/custom-table.blade.php
<table class="custom-table {{ $table_class }}">
    <!-- Custom table implementation -->
</table>
```

## Troubleshooting

### Common Issues

1. **Report not showing in menu**
   - Check if report is registered in `routes/reports.php`
   - Ensure report class exists and is properly namespaced

2. **Date filters not working**
   - Verify date format in requests
   - Check if date validation is properly configured

3. **Styling issues**
   - Ensure CSS is properly included in Vite config
   - Check for CSS class conflicts

4. **Permission issues**
   - Verify user has proper permissions for reports
   - Check middleware configuration

### Debug Commands

```bash
# Check registered reports
php artisan tinker
>>> app('report-type')->getTypes()

# Test report generation
>>> $report = new App\Reports\SalesReport();
>>> $report->getReport()
```

### Performance Tips

1. **Use eager loading** for relationships
2. **Implement proper indexing** on frequently filtered columns
3. **Use chunking** for large datasets
4. **Cache expensive calculations** when possible

---

For more examples and advanced usage, check the existing reports in `app/Reports/` directory.
