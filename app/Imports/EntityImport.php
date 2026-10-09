<?php

namespace App\Imports;

use App\Models\Settings\Entity;
use Auth;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EntityImport implements ToModel, WithHeadingRow
{
    /**
     * @return Model|null
     */
    public function model(array $row): Model|array|null
    {
        $queryCheck = Entity::where('id', (int) $row['id'])->first();
        if (! $queryCheck) {
            Entity::insert([
                'id' => $row['id'],
                'code' => $row['code'],
                'department_id' => $row['department_id'],
                'parent_id' => $row['parent_id'],
                'name_en' => $row['name_en'],
                'name_kh' => $row['name_kh'],
                'abbreviation' => $row['abbreviation'],
                'order_level' => $row['order_level'],
                'is_active' => 1,
                'created_by' => Auth::user()->id,
            ]);
        } else {
            Entity::where('id', $row['id'])->update([
                'code' => $row['code'],
                'department_id' => $row['department_id'],
                'parent_id' => $row['parent_id'],
                'name_en' => $row['name_en'],
                'name_kh' => $row['name_kh'],
                'abbreviation' => $row['abbreviation'],
                'order_level' => $row['order_level'],
                'is_active' => 1,
                'created_by' => Auth::user()->id,
            ]);
        }

        return null; // the row is saved above, nothing for Laravel Excel to save
    }
}
