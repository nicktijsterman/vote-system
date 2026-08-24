<?php


namespace App\Http\Controllers\Voter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voter\PropositionVotesSubmitRequest;
use App\Models\Proposition;
use App\VoteSystem\Services\PropositionService;
use Illuminate\Http\Response;

/** @psalm-suppress UnusedClass Resolved by Laravel's router via an array-callable route
 * binding in routes/api.php, invisible to Psalm's static usage analysis. */
class PropositionVotesController extends Controller
{
    public function __construct(private readonly PropositionService $propositionService)
    {
        parent::__construct();
    }

    public function store(PropositionVotesSubmitRequest $request, Proposition $proposition): Response
    {
        abort_unless(
            $proposition->is_open,
            400,
            trans('The answer for the previous proposition has not been registered as the proposition was already closed')
        );

        /** @psalm-suppress InvalidArgument This route sits behind the web-voter guard
         * middleware, so $request->user() is always a Voter here - Psalm's Laravel plugin
         * can't see the route-level guard binding and falls back to the default User type. */
        abort_if(
            $this->propositionService->propositionHasVoter($proposition, $request->user()),
            400,
            trans('You already answered this proposition')
        );

        $answers = collect($request->get('answers'));

        /** @psalm-suppress InvalidArgument Same as above - always a Voter here. */
        $this->propositionService->answerProposition(
            $request->user(),
            $proposition,
            $answers
        );

        return response(null, 204);
    }
}
