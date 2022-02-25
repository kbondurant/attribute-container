<?php

declare(strict_types=1);

namespace Tests;

use Kbondurant\AttributeContainer\AttributeContainer;
use Kbondurant\AttributeContainer\BindTo;
use League\Container\Container;
use League\Container\Definition\Definition;
use League\Container\Exception\ContainerException;
use League\Container\Exception\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Tests\Fixtures\Bar;
use Tests\Fixtures\DoubleDefinition;
use Tests\Fixtures\Foo;
use Tests\Fixtures\MyFoo;
use Tests\Fixtures\NoBinding;
use Tests\Fixtures\NotAnInterface;
use Tests\Fixtures\Singleton;

/**
 * @covers \Kbondurant\AttributeContainer\AttributeContainer
 * @covers \Kbondurant\AttributeContainer\BindTo
 */
class AttributeContainerTest extends TestCase
{
    private AttributeContainer $container;

    /** @var Container & MockObject */
    private Container | MockObject $mockContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockContainer = $this->createMock(Container::class);

        $this->container = new AttributeContainer();
        $this->container->setContainer($this->mockContainer);
    }

    public function test_it_returns_true_if_the_interface_has_a_valid_attribute_binding(): void
    {
        $this->assertTrue($this->container->has(Foo::class));
    }

    public function test_it_can_use_any_attribute_class_that_implements_binding_attribute_interface(): void
    {
        $this->assertTrue($this->container->has(MyFoo::class));
    }

    public function test_it_returns_false_if_no_binding_is_defined(): void
    {
        $this->assertFalse($this->container->has(NoBinding::class));
    }

    public function test_it_throws_an_exception_if_no_binding_is_defined(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Tests\Fixtures\NoBinding has no binding attribute');

        $this->container->get(NoBinding::class);
    }

    public function test_it_returns_false_if_more_than_one_binding_is_defined(): void
    {
        $this->assertFalse($this->container->has(DoubleDefinition::class));
    }

    public function test_it_throws_an_exception_if_more_than_one_binding_is_defined(): void
    {
        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Tests\Fixtures\DoubleDefinition has more than one binding declared');

        $this->container->get(DoubleDefinition::class);
    }

    public function test_it_returns_false_if_id_is_not_an_interface(): void
    {
        $this->assertFalse($this->container->has(NotAnInterface::class));
    }

    public function test_it_throws_an_exception_if_id_is_not_an_interface(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Tests\Fixtures\NotAnInterface does not exists or is not an interface');

        $this->container->get(NotAnInterface::class);
    }

    public function test_it_binds_the_resolved_class_in_the_container_then_returns_it_from_it(): void
    {
        $definition = $this->createMock(Definition::class);
        $definition->expects($this->once())
            ->method('setShared')
            ->with(false);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with(Bar::class)
            ->willReturn('resolved');

        $this->mockContainer->expects($this->once())
            ->method('add')
            ->with(Foo::class, 'resolved')
            ->willReturn($definition);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with(Bar::class);

        $this->container->get(Foo::class);
    }

    public function test_it_sets_the_binding_as_shared_in_the_container(): void
    {
        $definition = $this->createMock(Definition::class);
        $definition->expects($this->once())
            ->method('setShared')
            ->with(true);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with(Bar::class)
            ->willReturn('resolved');

        $this->mockContainer->expects($this->once())
            ->method('add')
            ->with(Singleton::class, 'resolved')
            ->willReturn($definition);

        $this->container->get(Singleton::class);
    }

    public function test_it_caches_the_resolved_bound_class(): void
    {
        $this->container->has(Foo::class);

        $this->assertCount(1, $this->getCacheValue());
    }

    public function test_it_clear_the_cache_once_resolved_class_is_bound_in_the_container(): void
    {
        $this->container->has(Foo::class);
        $this->assertCount(1, $this->getCacheValue());

        $this->container->get(Foo::class);
        $this->assertCount(0, $this->getCacheValue());
    }

    /** @return array<string, BindTo> */
    public function getCacheValue(): array
    {
        $reflector = new ReflectionClass(get_class($this->container));
        $property = $reflector->getProperty('cache');
        $property->setAccessible(true);

        $cache = $property->getValue($this->container);

        assert(is_array($cache));

        return $cache;
    }
}
