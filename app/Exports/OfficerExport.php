<?php

namespace App\Exports;
use OwenIt\Auditing\Contracts\Auditable;
use DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OfficerExport implements FromCollection, WithHeadings,Auditable
{
    use \OwenIt\Auditing\Auditable;
    public function collection(): \Illuminate\Support\Enumerable
    {

        $query = DB::table("officer")
            ->get();
        return $query;

    }

    public function headings(): array
    {
        $data["tables"] = DB::select('show columns from tbl_officer');
        $str = "";
        for($i=0; $i < count($data["tables"]); $i++){
            $str .= $data["tables"][$i]->Field.",";
        }
        $newArr = explode(",", $str);
        array_pop($newArr);
        $dataFields = $newArr;
        return $dataFields;

            //  return [
            //     "id",
            //     "payment_no",
            //     "payment_id",
            //     "customer_id",
            //     "document",
            //     "create_by",
            //     "record_by",
            //     "payment_date",
            //     "payment_type",
            //     "description",
            //     "amount",
            //     "entry_date",
            //     "is_active",
            //     "bu",
            //     "ou"
            // ];
    }
}
