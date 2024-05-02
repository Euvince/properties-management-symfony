<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class VerifyIsAuthuserUsername extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    function __construct(
        public $message = "Le nom d'utilisateur renseigné est incorrect.",
        public mixed $options = null,
        ?array $groups = null,
        mixed $payload = null
    )
    {
        parent::__construct($options, $groups, $payload);
    }
}
