<?php declare(strict_types=1);

namespace Qlimix\DependencyContainer;

use Pimple\Container;
use function array_replace_recursive;

final class PimpleDependencyRegistry implements RegistryInterface
{
    /** @var Container */
    private $pimple;

    public function __construct(Container $pimple)
    {
        $this->pimple = $pimple;
    }

    /**
     * @inheritDoc
     */
    public function set(string $id, callable $service): void
    {
        $this->pimple[$id] = function () use ($service) {
            return $service($this);
        };
    }

    public function setMaker(string $id, callable $maker): void
    {
        $this->pimple[$id] = $this->pimple->factory(function () use ($maker) {
            return $maker($this);
        });
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
    public function make(string $id, ?string $setId = null)
    {
        $object = $this->pimple->offsetGet($id);
        if ($setId !== null) {
            $this->setValue($setId, $object);
        }

        return $object;
    }

    /**
     * @inheritDoc
     */
    public function merge(string $id, array $value): void
    {
        if (!$this->has($id)) {
            $this->setValue($id, $value);
        }

        $this->setValue($id, array_replace_recursive($this->get($id), $value));
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
