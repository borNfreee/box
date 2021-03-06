<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box;

use Error;
use KevinGH\Box\Compactor\PhpScoper;
use KevinGH\Box\PhpScoper\Scoper;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @covers \KevinGH\Box\Compactor\PhpScoper
 */
class PhpScoperTest extends TestCase
{
    public function test_it_scopes_the_file_content(): void
    {
        $file = 'foo';
        $contents = <<<'JSON'
{
    "foo": "bar"
    
}
JSON;

        /** @var ObjectProphecy|Scoper $scoper */
        $scoperProphecy = $this->prophesize(Scoper::class);
        $scoperProphecy->scope($file, $contents)->willReturn($expected = 'Scoped contents');
        /** @var Scoper $scoper */
        $scoper = $scoperProphecy->reveal();

        $compactor = new PhpScoper($scoper);

        $actual = $compactor->compact($file, $contents);

        $this->assertSame($expected, $actual);

        $scoperProphecy->scope(Argument::cetera())->shouldHaveBeenCalledTimes(1);
    }

    public function test_it_returns_the_content_unchanged_if_the_scoping_failed(): void
    {
        $file = 'foo';
        $contents = <<<'JSON'
{
    "foo": "bar"
    
}
JSON;

        /** @var ObjectProphecy|Scoper $scoper */
        $scoperProphecy = $this->prophesize(Scoper::class);
        $scoperProphecy->scope($file, $contents)->willThrow(new Error());
        /** @var Scoper $scoper */
        $scoper = $scoperProphecy->reveal();

        $compactor = new PhpScoper($scoper);

        $actual = $compactor->compact($file, $contents);

        $this->assertSame($contents, $actual);
    }
}
