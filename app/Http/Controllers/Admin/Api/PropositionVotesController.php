<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Proposition;
use App\VoteSystem\Repositories\VoterPropositionOptionRepository;
use Illuminate\Http\JsonResponse;

/** @psalm-suppress UnusedClass Resolved by Laravel's router via an array-callable route
 * binding in routes/api.php, invisible to Psalm's static usage analysis. */
class PropositionVotesController extends Controller
{
    public function __construct(private readonly VoterPropositionOptionRepository $optionRepository)
    {
        parent::__construct();
    }

    public function __invoke(Proposition $proposition): JsonResponse
    {
        $timestamp = microtime(true);
        return response()->json([
            'data' => $this->optionRepository->findVotesBefore($proposition, $timestamp),
            'timestamp' => $timestamp,
        ]);
    }
}
