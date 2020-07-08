<?php

declare(strict_types=1);

namespace BEAR\Resource;

use OutOfRangeException;

use function array_merge;
use function in_array;

/**
 * @property $this $lazy
 * @property $this $eager
 * @psalm-suppress PropertyNotSetInConstructor for DSL
 */
final class Request extends AbstractRequest
{
    public const GET = 'get';

    public const POST = 'post';

    public const PUT = 'put';

    public const PATCH = 'patch';

    public const DELETE = 'delete';

    public const HEAD = 'head';

    public const OPTIONS = 'options';

    /**
     * @return array<string, string>|int|mixed|self
     *
     * @psalm-suppress ImplementedReturnTypeMismatch
     */
    public function __get(string $name)
    {
        if ($name === 'eager' || $name === 'lazy') {
            $this->in = $name;

            return $this;
        }

        if (in_array($name, ['code', 'headers', 'body'], true)) {
            return parent::__get($name);
        }

        throw new OutOfRangeException($name);
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $query
     */
    public function withQuery(array $query): RequestInterface
    {
        $this->query = $query;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addQuery(array $query): RequestInterface
    {
        $this->query = array_merge($this->query, $query);

        return $this;
    }

    public function toUriWithMethod(): string
    {
        $uri = $this->toUri();

        return "{$this->method} {$uri}";
    }

    public function toUri(): string
    {
        $this->resourceObject->uri->query = $this->query;

        return (string) $this->resourceObject->uri;
    }

    public function linkSelf(string $linkKey): RequestInterface
    {
        $this->links[] = new LinkType($linkKey, LinkType::SELF_LINK);

        return $this;
    }

    public function linkNew(string $linkKey): RequestInterface
    {
        $this->links[] = new LinkType($linkKey, LinkType::NEW_LINK);

        return $this;
    }

    public function linkCrawl(string $linkKey): RequestInterface
    {
        $this->links[] = new LinkType($linkKey, LinkType::CRAWL_LINK);

        return $this;
    }
}
