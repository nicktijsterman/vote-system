<?php


namespace App\Http\Controllers\Admin\Api;

use App\Http\Requests\Admin\Api\PropositionUpdateRequest;
use App\Models\Proposition;
use Illuminate\Http\JsonResponse;

/** @psalm-suppress UnusedClass Resolved by Laravel's router via an array-callable route
 * binding in routes/api.php, invisible to Psalm's static usage analysis. */
class PropositionController
{
    public function update(PropositionUpdateRequest $request, Proposition $proposition): JsonResponse
    {
        $proposition->update($request->validated());
        return response()->json($proposition);
    }
}
