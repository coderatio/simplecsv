<?php
declare(strict_types=1);

namespace Coderatio\SimpleCsv;


use Exception;
use RuntimeException;

class SimpleCsv
{
    /** @var array $data */
    protected $data = [];

    /** @var array $headers */
    protected $headers = [];

    /** @var array $rows */
    protected $rows = [];

    /**
     * Adds a new row to the sheet
     *
     * @param string $title
     * @param int $dashes
     * @param int $margin_left
     * @param string $dash_type
     * @return $this
     */
    public function addTitle(string $title, $dashes = 0, $margin_left = 0, $dash_type = '-'): self
    {
        $newTitle = [];
        $titleDashes = '';

        for ($margin = 0; $margin < $margin_left; $margin++) {
            $newTitle[] = '';
        }

        for ($dash = 0; $dash < $dashes; $dash++) {
            $titleDashes .= $dash_type;
        }

        if ($titleDashes !== '') {
            $title = " {$title} ";
        }

        $newTitle[] = "{$titleDashes}{$title}{$titleDashes}";

        $this->data[] = $newTitle;


        return $this;
    }

    /**
     * Adds header row to the sheet
     *
     * @param iterable $headers
     * @return $this
     */
    public function setHeaders(iterable $headers): self
    {
        foreach ($headers as $header) {
            $this->headers[] = $header;
        }

        $this->data[] = $this->headers;

        return $this;
    }

    /**
     * Adds a new row as body to the sheet
     *
     * @param array $columns
     * @return $this
     */
    public function setRow(array $columns): self
    {
        foreach ($columns as $column) {
            $this->rows[] = $column;
        }

        $this->data[] = $this->rows;
        $this->rows = [];

        return $this;
    }

    /**
     * Adds rows as body to the sheet
     *
     * @param iterable $rows
     * @return SimpleCsv
     */
    public function setRows(iterable $rows): self
    {
        foreach ($rows as $row) {
            $this->setRow($row);
        }

        return $this;
    }

    /**
     * Adds a blank row or number of rows as provided to the sheet
     *
     * @param int $spaces
     * @return $this
     */
    public function addSpace(int $spaces = 0): self
    {
        for ($number = 0; $number < $spaces; $number++) {
            $this->data[] = [];
        }

        return $this;
    }

    /**
     * Get the prepared data
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Downloads the prepared csv file
     *
     * @param string $fileName
     * @return void
     * @throws Exception
     */
    public function download(string $fileName = ''): void
    {
        if ($fileName === '') {
            $fileName = 'simple_csv_file_' . str_shuffle('sample_csv');
        }

        header('Content-Type: application/csv');
        header('Content-Disposition: attachement; filename="' . $fileName . '.csv"');

        ob_start();
        $file = fopen('php://output', 'wb');

        $number = 0;

        foreach ($this->data as $line) {
            $number++;

            if (!fputcsv($file, $line)) {
                throw new RuntimeException("Can't write line {$number}: {$line}");
            }
        }

        if (!fclose($file)) {
            throw new RuntimeException("Can't close php://output");
        }

        $csvContents = ob_get_clean();

        echo $csvContents;
        exit;
    }
}