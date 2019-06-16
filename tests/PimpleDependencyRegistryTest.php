<?php declare(strict_types=1);

namespace Qlimix\Tests\DependencyContainer;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Qlimix\DependencyContainer\PimpleDependencyRegistry;
use stdClass;

final class PimpleDependencyRegistryTest extends TestCase
{
    /** @var Container */
    private $container;

    /** @var PimpleDependencyRegistry */
    private $registry;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->registry = new PimpleDependencyRegistry($this->container);
    }

    /**
     * @test
     */
    public function shouldSetService(): void
    {
        $id = 'id';
        $return = new stdClass();

        $this->registry->set($id, static function () use ($return) {
            return $return;
        });

        $this->assertSame($return, $this->registry->get($id));
    }

    /**
     * @test
     */
    public function shouldSetValue(): void
    {
        $id = 'id';
        $return = 'value';

        $this->registry->setValue($id, static function () use ($return) {
            return $return;
        });

        $this->assertSame($return, $this->registry->get($id));
    }

    /**
     * @test
     */
    public function shouldSetMaker(): void
    {
        $id = 'id';
        $value = 0;

        $this->registry->setMaker($id, static function () use (&$value) {
            return ++$value;
        });

        $this->assertSame($value+1, $this->registry->make($id));
    }

    /**
     * @test
     */
    public function shouldMakeMultipleDifferent(): void
    {
        $id = 'id';
        $value = 0;

        $this->registry->setMaker($id, static function () use (&$value) {
            return ++$value;
        });

        $this->registry->make($id);
        $this->registry->make($id);
        $this->registry->make($id);

        $this->assertSame($value+1, $this->registry->make($id));
    }

    /**
     * @test
     */
    public function shouldSetOnSetId(): void
    {
        $id = 'id';
        $setId = 'foo';
        $value = 0;

        $this->registry->setMaker($id, static function () use (&$value) {
            return $value;
        });

        $make = $this->registry->make($id, $setId);
        $makeAnother = $this->registry->make($id, $setId);

        $this->assertSame($make, $makeAnother);
    }

    /**
     * @test
     */
    public function shouldMerge(): void
    {
        $id = 'id';

        $value = [
            'foo' => 'foo',
            'bar' => 'bar',
        ];

        $this->registry->setValue($id, $value);

        $moreValues = [
            'foo' => 'foo',
            'bar' => 'foo',
            'foobar' => 'barfoo',
        ];

        $this->registry->merge($id, $moreValues);

        $values = $this->registry->get($id);

        $this->assertSame('foo' ,$values['foo']);
        $this->assertSame('foo' ,$values['bar']);
        $this->assertSame('barfoo' ,$values['foobar']);
    }

    /**
     * @test
     */
    public function shouldMergeNoneSetId(): void
    {
        $id = 'id';

        $moreValues = [
            'foo' => 'foo',
            'bar' => 'foo',
            'foobar' => 'barfoo',
        ];

        $this->registry->merge($id, $moreValues);

        $values = $this->registry->get($id);

        $this->assertSame('foo' ,$values['foo']);
        $this->assertSame('foo' ,$values['bar']);
        $this->assertSame('barfoo' ,$values['foobar']);
    }

    /**
     * @test
     */
    public function shouldHaveValueId(): void
    {
        $id = 'id';
        $value = 1;

        $this->registry->setValue($id, $value);

        $this->assertTrue($this->registry->has($id));
    }

    /**
     * @test
     */
    public function shouldHaveServiceId(): void
    {
        $id = 'id';
        $value = 1;

        $this->registry->set($id, static function () use ($value) {
            return $value;
        });

        $this->assertTrue($this->registry->has($id));
    }

    /**
     * @test
     */
    public function shouldNotHaveValueId(): void
    {
        $id = 'id';

        $this->assertFalse($this->registry->has($id));
    }

    /**
     * @test
     */
    public function shouldNotHaveServiceId(): void
    {
        $id = 'id';

        $this->assertFalse($this->registry->has($id));
    }
}
