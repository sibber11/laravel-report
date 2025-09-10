<?php

namespace BlinkerBoy\Report;

//todo: add thead class
//todo: add tbody class
class TableBuilder
{
    protected ?string $title;

    protected string $table_class;

    protected array $headers;

    protected string $header_class;

    protected array $rows;

    protected string $row_class;

    protected ?array $totals;

    protected bool $has_serial = false;

    protected string $title_class;

    public function __construct($title = '', $has_serial = false)
    {
        $this->reset();
        if (!empty($title)) {
            $this->setTitle($title);
        }
        $this->has_serial = $has_serial;
    }

    public function reset(): void
    {
        $this->title = null;
        $this->title_class = '';
        $this->headers = [];
        $this->rows = [];
        $this->totals = [];
        $this->row_class = '';
        $this->header_class = '';
        $this->table_class = '';
    }

    /**
     * Sets the title of the table
     */
    public function setTitle(string $title): static
    {
        $this->title = __($title);

        return $this;
    }

    public function hasSerial(): static
    {
        $this->has_serial = true;

        return $this;
    }

    /**
     * Sets column definitions as array of ['key' => 'Value'] pair
     *
     * it also accepts two row of headers such that the first row will be the main header
     * with row span of 2, the second header will contain sub columns
     *
     * for nightmare use case pass array as ['key' => ['value' => 'Value', 'data' => ['sub_col_1_key' =>'Sub Col 1', 'sub_col_2_key'=>'Sub Column 2']]]
     *
     * for simple use case just pass an associative array with key value pair.
     *
     * pass row_span as 2
     *
     * don't forget to pass rows as ['key' => ['sub_col_1_key' => 'Value', 'sub_col_2_key' => 'Value']]
     *
     * it only works for two rows of headers
     *
     * when I wrote this, only I and god knew what I was doing
     * now only god knows
     * don't even try to understand it
     * this is forbidden function
     * if you do understand it and are able to make it readable
     * please let me know at 'check commit log for my email'
     */
    public function setHeaders(array $headers, array $header_classes = [], $row_span = 1): static
    {
        if ($row_span > 1) {
            $header_line_one = array_map(function ($header) use ($row_span) {
                if (is_array($header)) {
                    return [
                        'value' => __($header['value']),
                        'col_span' => count($header['data']),
                    ];
                }

                return [
                    'value' => __($header),
                    'row_span' => $row_span,
                ];
            }, $headers);

            $header_line_two = [];
            foreach ($headers as $key => $value) {
                if (!is_array($value)) {
                    continue;
                }
                foreach ($value['data'] as $key2 => $value2) {
                    $header_line_two["$key.$key2"] = __($value2);
                }
            }
            $headers = compact('header_line_one', 'header_line_two');

        } else {
            $headers = array_map(function ($header) {
                if (is_array($header)) {
                    throw new \Exception('Row span must be 2 for this type of header');
                }

                return __($header);
            }, $headers);
        }

        $this->headers = [
            'data' => $headers,
            'classes' => $header_classes,
        ];

        return $this;
    }

    public function addRow(array $row, $row_class = '', $row_classes = []): static
    {
        $this->rows[] = [
            'data' => $row,
            'classes' => $row_classes,
            'class' => $row_class,
        ];

        return $this;
    }

    public function setTotals(array $totals): static
    {
        $this->totals = $totals;

        return $this;
    }

    public function build()
    {
        if (empty($this->headers)) {
            return '';
        }

        return view('report::components.table', [
            'title' => $this->title,
            'title_class' => $this->title_class,
            'headers' => $this->headers,
            'rows' => $this->rows,
            'totals' => $this->totals,
            'has_serial' => $this->has_serial,
            'row_class' => $this->row_class,
            'header_class' => $this->header_class,
            'table_class' => $this->table_class,
        ]);
    }

    public function setHeaderClass($style): static
    {
        $this->header_class = $style;

        return $this;
    }

    public function setRowClass($style): static
    {
        $this->row_class = $style;

        return $this;
    }

    public function setTableClass(string $table_class): static
    {
        $this->table_class = $table_class;

        return $this;
    }

    public function setTitleClass(string $title_class): void
    {
        $this->title_class = $title_class;
    }

    /**
     * this function sums all the values of a column
     *
     * it can be used with nightmare header above
     *
     * pass array as ['nightmare_col' => ['sub_col1','sub_col2', ...], 'simple_col']
     */
    public function autoSum(array $columns, string $total_col, string $total_label = 'Total:'): static
    {
        //todo: auto generate total col
        //todo: try to colspan total col
        //todo: add total style?
        $this->totals[$total_col] = $total_label;
        $columns = $this->dotArr($columns);

        foreach ($columns as $col) {
            $total = 0;
            foreach ($this->rows as $row) {
                $res = data_get($row['data'], $col);
                if (is_string($res)) {
                    continue;
                }
                $total += $res;
            }
            $this->totals[$col] = $total;
        }

        return $this;
    }

    /**
     * this function will convert array of columns to dot notation
     *
     * this accepts ['col' => ['row1', 'row2']]
     *
     * and returns ['col.row1', 'col.row2']
     */
    protected function dotArr(array $columns): array
    {
        $genCols = [];
        foreach ($columns as $key => $values) {
            if (is_string($values)) {
                $genCols[] = $values;

                continue;
            }

            foreach ($values as $value) {
                $genCols[] = "$key.$value";
            }
        }

        return $genCols;
    }
}
