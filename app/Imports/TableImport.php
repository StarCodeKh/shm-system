<?php

namespace App\Imports;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Import of a list from its template (GET /api/v1/import/template?table=): row 1 = column names.
 * A row with the id of a record the user can see updates it through the model (audit trail, data scope) when it
 * changes something;
 * any other row is added (with its id when it has one). Columns not in the file are left alone, empty cells
 * are saved empty, dates are read from Excel day numbers or text.
 * created_by / modified_by and the timestamps are set here, never read from the file.
 */
class TableImport implements ToModel, WithHeadingRow
{
    private const SERVER_COLUMNS = ['created_by', 'modified_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'];

    /** column => type name, without the server columns */
    private array $columns;

    /** the server columns the table has */
    private array $server;

    public function __construct(private Model $model)
    {
        $all = collect(Schema::getColumns($model->getTable()))->mapWithKeys(fn ($column) => [$column['name'] => strtolower($column['type_name'])]);
        $this->columns = $all->except(self::SERVER_COLUMNS)->all();
        $this->server = array_flip(array_intersect(self::SERVER_COLUMNS, $all->keys()->all()));
    }

    public function model(array $row): Model|array|null
    {
        $values = [];
        foreach ($this->columns as $name => $type) {
            if (array_key_exists($name, $row)) {
                $value = is_string($row[$name]) ? trim($row[$name]) : $row[$name];
                $values[$name] = $value === '' || $value === null ? null : $this->value($value, $type);
            }
        }
        if (! $values) {
            return null;
        }

        $current = isset($values['id']) ? $this->model->newQuery()->find($values['id']) : null;
        if ($current) {
            // Only a row that changes something is saved (and audited): an unchanged row is left as it is
            $current->fill($values);
            if ($current->isDirty()) {
                $current->fill($this->stamps(['modified_by' => Auth::id()]))->save();
            }
        } else {
            if (! isset($values['id'])) {
                unset($values['id']);
            }
            $this->model->newQuery()->insert($values + $this->stamps(['created_by' => Auth::id(), 'created_at' => now(), 'updated_at' => now()]));
        }

        return null; // the row is saved above, nothing for Laravel Excel to save
    }

    /** The given server columns the table has */
    private function stamps(array $values): array
    {
        return array_intersect_key($values, $this->server);
    }

    /** Dates from an Excel day number or text; everything else as given */
    private function value($value, string $type)
    {
        if (! in_array($type, ['date', 'datetime', 'timestamp'], true)) {
            return $value;
        }
        try {
            $date = is_numeric($value) ? Carbon::instance(Date::excelToDateTimeObject($value)) : Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }

        return $date->format($type === 'date' ? 'Y-m-d' : 'Y-m-d H:i:s');
    }
}
