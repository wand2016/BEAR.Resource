<?php

declare(strict_types=1);

namespace BEAR\Resource;

use BEAR\Resource\Exception\SchemeException;

use function array_key_exists;

final class SchemeCollection implements SchemeCollectionInterface
{
    /**
     * Scheme
     *
     * @var string
     */
    private $scheme = '';

    /**
     * Application name
     *
     * @var string
     */
    private $app = '';

    /** @var AdapterInterface[] */
    private $collection = [];

    public function scheme(string $scheme): SchemeCollectionInterface
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function host(string $host): SchemeCollectionInterface
    {
        $this->app = $host;

        return $this;
    }

    public function toAdapter(AdapterInterface $adapter): SchemeCollectionInterface
    {
        $this->collection[$this->scheme . '://' . $this->app] = $adapter;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws SchemeException
     */
    public function getAdapter(AbstractUri $uri): AdapterInterface
    {
        $schemeIndex = $uri->scheme . '://' . $uri->host;
        if (! array_key_exists($schemeIndex, $this->collection)) {
            if ($uri->scheme === 'http') {
                return $this->collection['http://self'];
            }

            throw new SchemeException($uri->scheme . '://' . $uri->host);
        }

        return $this->collection[$schemeIndex];
    }
}
