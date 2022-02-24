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

    /** @var BindTo[] */
    private array $cache = [];

    public function get(string $id)
    {
        $bindTo = $this->getBindFromCache($id) ?? $this->getBoundClass($id);

        assert($this->container instanceof Container);

        $this->container->add($id, $bindTo->getClass())
            ->setShared($bindTo->isShared());

        return $this->container->get($bindTo->getClass());
    }

    public function has(string $id): bool
    {
        try {
            $bindTo = $this->getBoundClass($id);
        } catch (ContainerExceptionInterface) {
            return false;
        }

        $this->cache[$id] = $bindTo;

        return true;
    }

    private function getBoundClass(string $id): BindTo
    {
        if (!interface_exists($id)) {
            throw new NotFoundException(sprintf('%s does not exists or is not an interface', $id));
        }

        $attributes = (new ReflectionClass($id))->getAttributes(BindTo::class);
        $bindCount = count($attributes);

        $bindTo = match (true) {
            $bindCount > 1 => throw new ContainerException(sprintf('%s has more than one binding declared', $id)),
            $bindCount < 1 => throw new NotFoundException(sprintf('%s has no binding attribute', $id)),
            default => $attributes[0]->newInstance(),
        };

        assert($bindTo instanceof BindTo);

        return $bindTo;
    }

    private function getBindFromCache(string $id): ?BindTo
    {
        if (!array_key_exists($id, $this->cache)) {
            return null;
        }

        $bindTo = $this->cache[$id];
        unset($this->cache[$id]);

        return $bindTo;
    }
}
