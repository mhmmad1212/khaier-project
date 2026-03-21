<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Policy;
use App\Models\Regulation;
use App\Models\FinancialReport;

class ShortLinkController extends Controller
{
    public function resolve($code)
    {
        $models = [
            News::class,
            Policy::class,
            Regulation::class,
            FinancialReport::class,
        ];

        foreach ($models as $model) {
            $record = $model::where('short_code', $code)->first();

            if ($record) {
                return redirect()->to($record->url ?? '/');
            }
        }

        abort(404);
    }
}
