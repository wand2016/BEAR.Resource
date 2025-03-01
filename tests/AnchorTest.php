<?php

declare(strict_types=1);

namespace BEAR\Resource;

use BEAR\Resource\Exception\LinkException;
use FakeVendor\Sandbox\Resource\App\Author;
use PHPUnit\Framework\TestCase;
use Ray\ServiceLocator\ServiceLocator;

class AnchorTest extends TestCase
{
    private Anchor $anchor;
    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $invoker = (new InvokerFactory())();
        $author = new Author();
        $author->onGet(1);
        $this->request = new Request($invoker, $author, Request::GET, ['id' => 1]);
        $this->anchor = new Anchor(ServiceLocator::getReader());
    }

    public function testHref(): void
    {
        [$method, $uri] = $this->anchor->href('blog', $this->request, []);
        $this->assertSame(Request::GET, $method);
        $this->assertSame('app://self/blog?id=12', $uri);
    }

    public function testInvalid(): void
    {
        $this->expectException(LinkException::class);
        $this->anchor->href('invalid', $this->request, []);
    }
}
