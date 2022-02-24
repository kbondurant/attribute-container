<?php

declare(strict_types=1);

namespace Kbondurant\AttributeContainer;

interface BindingAttribute
{
    /**
     * @return class-string
     */
    public function getClass(): string;

    public function isShared(): bool;
}
