<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'Adres e-mail "{{ value }}" jest już zajęty.';
}
