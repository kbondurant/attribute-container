<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Kbondurant\AttributeContainer\BindTo;

#[BindTo(Bar::class, true)]
interface Singleton
{
}
