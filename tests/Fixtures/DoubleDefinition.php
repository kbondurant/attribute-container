<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Kbondurant\AttributeContainer\BindTo;

#[BindTo(Bar::class)]
#[BindTo(Bar::class)] // @phpstan-ignore-line
interface DoubleDefinition
{
}
