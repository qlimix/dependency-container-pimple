<?php declare(strict_types=1);

namespace Qlimix\DependencyContainer;

use Pimple\Container;
use Psr\Container\ContainerInterface;

final class PimpleDependencyRegistry implements DependencyRegistryInterface
{
    /** @var Container */
    private $pimple;

    /** @var ContainerInterface */
    private $psrContainer;

    /**
     * @param Container $pimple
     * @param ContainerInterface $container
     */
    public function __construct(Container $pimple, ContainerInterface $container)
    {
        $this->pimple = $pimple;
        $this->psrContainer = $container;
    }

    /**
     * @inheritDoc
     */
    public function set(string $id, callable $service): void
    {
        $container = $this->psrContainer;

        $this->pimple[$id] = function () use ($container, $service) {
            return $service($container);
        };
    }

    public function setMaker(string $id, callable $maker): void
    {
        $this->pimple[$id] = $this->pimple->factory($maker);
    }

    /**
     * @inheritDoc
     */
    public function setValue(string $id, $value): void
    {
        $this->pimple[$id] = $value;
    }

    /**
     * @inheritDoc
     */
    public function make(string $id)
    {
        return $this->pimple->offsetGet($id);
    }

    public function merge(string $id, array $value): void
    {
        if (!$this->has($id)) {
            $this->setValue($id, $value);
        }

        $this->setValue($id, array_merge_recursive($this->get($id), $value));
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->pimple->offsetGet($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return $this->pimple->offsetExists($id);
    }
}
