<?php

namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Models\Proposition;
use App\VoteSystem\Services\PropositionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/** @psalm-suppress UnusedClass Resolved by Laravel's router via an array-callable route
 * binding in routes/web.php, invisible to Psalm's static usage analysis. */
class PropositionController extends Controller
{
    public function __construct(private readonly PropositionService $propositionService)
    {
        parent::__construct();
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        /** @psalm-suppress InvalidArgument This route sits behind the web-voter guard
         * middleware, so $request->user() is always a Voter here - Psalm's Laravel plugin
         * can't see the route-level guard binding and falls back to the default User type. */
        $proposition = $this->propositionService->getNextProposition($user);

        return $this->show($request, $proposition);
    }

    public function show(Request $request, ?Proposition $proposition): View
    {
        $answeredPropositionIds = $request->user()
            ->answers()
            ->distinct('proposition_id')
            ->get(['proposition_id'])
            ->pluck('proposition_id');

        return view('views.voter.show', [
            'proposition' => $proposition,
            'answeredPropositions' => $answeredPropositionIds
        ]);
    }
}
