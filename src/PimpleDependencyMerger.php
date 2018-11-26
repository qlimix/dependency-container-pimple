<?php declare(strict_types=1);

namespace Qlimix\DependencyContainer\Pimple;

use Pimple\Container;
use Psr\Container\ContainerInterface;
use Qlimix\DependencyContainer\DependencyRegistryInterface;

final class PimpleDependencyMerger implements DependencyRegistryInterface
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
