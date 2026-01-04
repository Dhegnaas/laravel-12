<?php

namespace App\Core\Traits;

use App\Models\Settings;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// please create here only simple func that will use more pages not one ot two
trait GlobalTraits
{

   

    public function paginate($object, $request, $type = false, $is_filter = false)
    {
     
        $lengthTSshow = $is_filter ? 200 : ($request->length > 100 ? 0 : $request->length);
        $allRows = $object->count();

        $dataQuery = $object->skip($request->start)->take($lengthTSshow);

        $data = $type ? $dataQuery : $dataQuery->orderBy('created_at', 'DESC')->get();

        return [
            'recordsTotal' => $allRows,
            'recordsFiltered' => $allRows,
            'data' => $data->values()->toArray(),  // Ensure data is indexed as an array
        ];
    }


    


    public function filter($query, $condition, $request)
    {
        if ($condition) {
            $query = $query->whereRaw($condition);
        }
        $query = $query->orderByDesc('id');
        return $query->limit(1000)->get();
    }

}
