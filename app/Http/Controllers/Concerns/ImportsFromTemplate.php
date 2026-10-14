<?php

namespace App\Http\Controllers\Concerns;

use App\Imports\TableImport;
use CommonService;
use Illuminate\Http\Request;

/**
 * POST {list}/import for a list controller: imports a file in the list's template format (App\Imports\TableImport).
 * Also add the table to ImportController::TABLES (preview, template) and an import_{table} permission.
 * The controller's model is $this->db_table (or $this->dbTable).
 */
trait ImportsFromTemplate
{
    public function import(Request $request)
    {
        return CommonService::importSpreadsheet(new TableImport($this->db_table ?? $this->dbTable), $request);
    }
}
