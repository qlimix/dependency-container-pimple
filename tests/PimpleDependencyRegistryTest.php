<?php declare(strict_types=1);

namespace Qlimix\Tests\DependencyContainer;

use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Qlimix\DependencyContainer\PimpleDependencyRegistry;
use stdClass;

final class PimpleDependencyRegistryTest extends TestCase
{
    private Container $container;

    private PimpleDependencyRegistry $registry;

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->registry = new PimpleDependencyRegistry($this->container);
    }

    public function testShouldSetService(): void
    {
        $id = 'id';
        $return = new stdClass();

        $this->registry->set($id, static function () use ($return) {
            return $return;
        });

        $this->assertSame($return, $this->registry->get($id));
    }

    public function testShouldSetValue(): void
    {
        $id = 'id';
        $return = 'value';

        $this->registry->setValue($id, static function () use ($return) {
            return $return;
        });

        $this->assertSame($return, $this->registry->get($id));
    }

    public function testShouldSetMaker(): void
    {
        $id = 'id';
        $value = 0;

        $this->registry->setMaker($id, static function () use (&$value) {
            return ++$value;
        });

        $this->assertSame($value+1, $this->registry->make($id));
    }

    public function testShouldMakeMultipleDifferent(): void
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

    public function testShouldSetOnSetId(): void
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

    public function testShouldMerge(): void
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

    public function testShouldMergeNoneSetId(): void
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

    public function testShouldHaveValueId(): void
    {
        $id = 'id';
        $value = 1;

        $this->registry->setValue($id, $value);

        $this->assertTrue($this->registry->has($id));
    }

    public function testShouldHaveServiceId(): void
    {
        $id = 'id';
        $value = 1;

        $this->registry->set($id, static function () use ($value) {
            return $value;
        });

        $this->assertTrue($this->registry->has($id));
    }

    public function testShouldNotHaveValueId(): void
    {
        $id = 'id';

        $this->assertFalse($this->registry->has($id));
    }

    public function testShouldNotHaveServiceId(): void
    {
        $id = 'id';

        $this->assertFalse($this->registry->has($id));
    }
}
