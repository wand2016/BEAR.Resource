<?php

declare(strict_types=1);

namespace BEAR\Resource;

use BEAR\Resource\Annotation\ResourceParam;
use Ray\Di\InjectorInterface;

use function assert;
use function parse_url;
use function uri_template;

use const PHP_URL_FRAGMENT;

final class AssistedResourceParam implements ParamInterface
{
    /** @var ResourceParam */
    private $resourceParam;

    public function __construct(ResourceParam $resourceParam)
    {
        $this->resourceParam = $resourceParam;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(string $varName, array $query, InjectorInterface $injector)
    {
        unset($varName);
        $resource = $injector->getInstance(ResourceInterface::class);
        assert($resource instanceof ResourceInterface);
        $uri = $this->resourceParam->templated === true ? uri_template($this->resourceParam->uri, $query) : $this->resourceParam->uri;
        $resourceResult = $resource->uri($uri)();
        $fragment = parse_url($uri, PHP_URL_FRAGMENT);

        return $resourceResult[$fragment];
    }
}
