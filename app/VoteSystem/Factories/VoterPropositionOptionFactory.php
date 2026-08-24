<?php

namespace App\VoteSystem\Factories;

use App\Models\Proposition;
use App\Models\Voter;
use App\Models\VoterPropositionOption;
use Exception;
use Illuminate\Support\Collection;

class VoterPropositionOptionFactory
{
    public static function make(
        Voter $voter,
        Proposition $proposition,
        Collection $answers
    ): Collection {
        return $answers->map(
            fn (string $vertical, string $horizontal) => match ($proposition->type) {
                "grid" => self::mapRow(
                    $voter->id,
                    $proposition->id,
                    $vertical,
                    $horizontal
                ),
                "list" => self::mapRow(
                    $voter->id,
                    $proposition->id,
                    $horizontal,
                    $vertical
                ),
                default => self::rejectType($proposition->type)
            }
        );
    }

    /**
     * @throws Exception
     */
    protected static function rejectType(string $type): never
    {
        throw new Exception("Invalid type \"$type\"");
    }

    protected static function mapRow(
        string $voterId,
        string $propositionId,
        string $horizontal,
        string $vertical
    ): VoterPropositionOption {
        return new VoterPropositionOption([
            'voter_id' => $voterId,
            'proposition_id' => $propositionId,
            'horizontal_option_id' => $horizontal,
            'vertical_option_id' => $vertical,
        ]);
    }
}
