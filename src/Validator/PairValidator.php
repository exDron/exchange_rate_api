<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PairValidator extends ConstraintValidator
{
    /**
     * @param string[] $exchangeRatePairs
     */
    public function __construct(private readonly array $exchangeRatePairs)
    {
    }

    /**
     * @param string|null $value
     * @param Pair        $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Pair) {
            return;
        }

        $pair = strtoupper(trim((string) ($value ?? '')));
        if ('' === $pair) {
            $this->context->buildViolation($constraint->messageMissing)->addViolation();

            return;
        }

        if (1 !== substr_count($pair, '/')) {
            $this->context->buildViolation($constraint->messageSlash)->addViolation();

            return;
        }

        [$base, $quote] = array_map('trim', explode('/', $pair));
        if ('' === $base || '' === $quote) {
            $this->context->buildViolation($constraint->messageNonEmpty)->addViolation();

            return;
        }

        if (!preg_match('/^[A-Z]{3}$/', $base) || !preg_match('/^[A-Z]{3}$/', $quote)) {
            $this->context->buildViolation($constraint->messageTicker)->addViolation();

            return;
        }

        if ($base === $quote) {
            $this->context->buildViolation($constraint->messageDifferent)->addViolation();
        }

        if (!in_array($quote.$base, $this->exchangeRatePairs, true)) {
            $this->context->buildViolation($constraint->messageNotSupported)->addViolation();
        }
    }
}
