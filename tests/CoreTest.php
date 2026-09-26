<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use Rasim\PhpMvcCore\{
    Core,
    Database,
    Controller
};

class CoreTest extends TestCase
{
    public function testCoreInstantiation(): void
    {
        $core = new Core();
        $this->assertInstanceOf(Core::class, $core);
    }

    public function testCoreGetUrlReturnsNullWithoutParams(): void
    {
        $core = new Core();
        $this->assertNull($core->getUrl());
    }

    public function testControllerModel(): void
    {
        $controller = new Controller();
        $this->assertInstanceOf(Controller::class, $controller);
    }
}
