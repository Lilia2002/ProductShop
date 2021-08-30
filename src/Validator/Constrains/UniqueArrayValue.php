<?php


namespace App\Validator\Constrains;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 *
 */
//#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]

class UniqueArrayValue extends Constraint
{
    public $message = 'One product cannot have more than one specification with each name.';

}
