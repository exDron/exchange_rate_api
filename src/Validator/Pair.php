<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Validates a currency/asset pair string in the form BASE/QUOTE.
 * Rules:
 *  - Non-empty string
 *  - Exactly one '/'
 *  - BASE and QUOTE must be non-empty
 *  - Each symbol exactly 3 uppercase letters A–Z
 *  - BASE and QUOTE must be different.
 */
class Pair extends Constraint
{
    public string $messageMissing = 'Missing pair. Expected format like BTC/USD';
    public string $messageSlash = 'Invalid pair. Expected exactly one "/" in format BASE/QUOTE, e.g., BTC/USD';
    public string $messageNonEmpty = 'Invalid pair. BASE and QUOTE must be non-empty';
    public string $messageTicker = 'Invalid pair. BASE and QUOTE must be exactly 3 uppercase letters A-Z';
    public string $messageDifferent = 'Invalid pair. BASE and QUOTE must be different';

    #[\Override]
    public function validatedBy(): string
    {
        return PairValidator::class;
    }
}
