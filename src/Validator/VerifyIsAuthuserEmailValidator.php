<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VerifyIsAuthuserEmailValidator extends ConstraintValidator
{
    function __construct(
        private readonly Security $security
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        /** @var VerifyIsAuthuserEmail $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if ($value !== $this->security->getUser()->getEmail()) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        }

    }
}
