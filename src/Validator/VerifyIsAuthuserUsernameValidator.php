<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VerifyIsAuthuserUsernameValidator extends ConstraintValidator
{
    function __construct(
        private readonly Security $security
    )
    {
    }

    public function validate($value, Constraint $constraint)
    {
        /** @var VerifyIsAuthuserUsername $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if ($value !== $this->security->getUser()->getUsername()) {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        }

    }
}
