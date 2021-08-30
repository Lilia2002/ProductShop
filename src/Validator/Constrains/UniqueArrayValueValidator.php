<?php


namespace App\Validator\Constrains;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;


class UniqueArrayValueValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueArrayValue) {
            throw new UnexpectedTypeException($constraint, UniqueArrayValue::class);
        }

        if (!is_array($value) && !$value instanceof \Traversable) {
            throw new UnexpectedValueException($value, 'array or Traversable');
        }

        foreach ($value as $keys => $specification) {
            $name = $specification->getName();
            foreach ($value as $key => $spec) {
                if ($keys == $key) {
                    continue;
                }
                if ($spec->getName() == $name) {
                    $this->context
                        ->buildViolation($constraint->message)
                        ->addViolation();

                }
            }
        }
    }
}
