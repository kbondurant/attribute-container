<?php

declare(strict_types=1);

namespace Tests\Fixtures;

use Attribute;
use Kbondurant\AttributeContainer\BindingAttribute;

#[Attribute]
class MyBindTo implements BindingAttribute
{
    /**
     * @param class-string $class
     */
    public function __construct(
        private string $class,
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function isShared(): bool
    {
        return false;
    }
}
