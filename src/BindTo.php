<?php

declare(strict_types=1);

namespace Kbondurant\AttributeContainer;

use Attribute;

#[Attribute]
class BindTo implements BindingAttribute
{
    /**
     * @param class-string $class
     * @param bool $shared
     */
    public function __construct(
        private string $class,
        private bool $shared = false,
    ) {
    }

    /**
     * @return class-string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }
}
