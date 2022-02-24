<?php

declare(strict_types=1);

namespace Kbondurant\AttributeContainer;

use League\Container\Container;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use League\Container\Exception\ContainerException;
use League\Container\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class AttributeContainer implements ContainerAwareInterface, ContainerInterface
{
    use ContainerAwareTrait;

    /** @var BindingAttribute[] */
    private array $cache = [];

    public function get(string $id)
    {
        $bindingAttribute = $this->getAttributeFromCache($id) ?? $this->getBindingAttribute($id);

        assert($this->container instanceof Container);

        $this->container->add($id, $bindingAttribute->getClass())
            ->setShared($bindingAttribute->isShared());

        return $this->container->get($bindingAttribute->getClass());
    }

    public function has(string $id): bool
    {
        try {
            $bindingAttribute = $this->getBindingAttribute($id);
        } catch (ContainerExceptionInterface) {
            return false;
        }

        $this->cache[$id] = $bindingAttribute;

        return true;
    }

    private function getBindingAttribute(string $id): BindingAttribute
    {
        if (!interface_exists($id)) {
            throw new NotFoundException(sprintf('%s does not exists or is not an interface', $id));
        }

        $attributes = (new ReflectionClass($id))->getAttributes(BindingAttribute::class, 2);
        $bindCount = count($attributes);

        $bindingAttribute = match (true) {
            $bindCount > 1 => throw new ContainerException(sprintf('%s has more than one binding declared', $id)),
            $bindCount < 1 => throw new NotFoundException(sprintf('%s has no binding attribute', $id)),
            default => $attributes[0]->newInstance(),
        };

        assert($bindingAttribute instanceof BindingAttribute);

        return $bindingAttribute;
    }

    private function getAttributeFromCache(string $id): ?BindingAttribute
    {
        if (!array_key_exists($id, $this->cache)) {
            return null;
        }

        $bindTo = $this->cache[$id];
        unset($this->cache[$id]);

        return $bindTo;
    }
}
