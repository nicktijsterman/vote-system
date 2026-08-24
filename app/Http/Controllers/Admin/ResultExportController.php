<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResultExportRequest;
use App\VoteSystem\Exports\ResultsExport;
use Illuminate\Contracts\Support\Responsable;

/** @psalm-suppress UnusedClass Resolved by Laravel's router via an array-callable route
 * binding in routes/web.php, invisible to Psalm's static usage analysis. */
class ResultExportController extends Controller
{
    public function index(ResultsExport $export, ResultExportRequest $request): Responsable
    {
        if ($request->has('proposition_id')) {
            $export->setPropositionId($request->get('proposition_id'));
        }

        return $export;
    }
}
