<?php

declare(strict_types=1);

namespace BEAR\Resource;

use BEAR\Resource\Exception\ResourceDirException;
use Iterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

use function array_diff_key;
use function array_key_exists;
use function array_keys;
use function array_values;
use function assert;
use function class_exists;
use function file_exists;
use function get_declared_classes;
use function is_string;
use function strpos;

/**
 * @implements Iterator<string, Meta>
 */
final class AppIterator implements Iterator
{
    /** @var int */
    private $i = 0;

    /** @var array<string, Meta> */
    private $metaCollection = [];

    /** @var list<string> */
    private $keys = [];

    /**
     * @throws ResourceDirException
     */
    public function __construct(string $resourceDir)
    {
        if (! file_exists($resourceDir)) {
            throw new ResourceDirException($resourceDir);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($resourceDir),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $this->metaCollection = $this->getMetaCollection($iterator);
        $this->keys = array_keys($this->metaCollection);
    }

    public function current(): Meta
    {
        return $this->metaCollection[$this->keys[$this->i]];
    }

    public function next(): void
    {
        ++$this->i;
    }

    public function key(): string
    {
        return $this->keys[$this->i];
    }

    public function valid(): bool
    {
        return array_key_exists($this->i, $this->keys);
    }

    public function rewind(): void
    {
        $this->i = 0;
    }

    /**
     * @param Iterator<SplFileInfo> $iterator
     *
     * @return array<string, Meta>
     */
    private function getMetaCollection(Iterator $iterator): array
    {
        $metaCollection = [];
        foreach ($iterator as $item) {
            if ($this->isNotPhp($item)) {
                continue;
            }

            $resourceClass = $this->getResourceClassName($item);
            if ($resourceClass === '') {
                continue;
            }

            assert(class_exists($resourceClass));
            $meta = new Meta($resourceClass);
            $metaCollection[$meta->uri] = $meta;
        }

        return $metaCollection;
    }

    private function isNotPhp(SplFileInfo $item): bool
    {
        $isPhp = $item->isFile()
            && $item->getExtension() === 'php'
            && (strpos($item->getBasename('.php'), '.') === false);

        return ! $isPhp;
    }

    private function getResourceClassName(SplFileInfo $file): string
    {
        $pathName = $file->getPathname();
        $declaredClasses = get_declared_classes();
        assert(file_exists($pathName));
        include_once $pathName;
        $newClasses = array_values(array_diff_key(get_declared_classes(), $declaredClasses));
        $name = $this->getName($newClasses);
        assert(is_string($name));

        return $name;
    }

    /**
     * @param array<class-string> $newClasses
     *
     * @return class-string|string
     */
    private function getName(array $newClasses)
    {
        foreach ($newClasses as $newClass) {
            $parent = (new ReflectionClass($newClass))->getParentClass();
            if ($parent && $parent->name === ResourceObject::class) {
                return $newClass;
            }
        }

        return '';
    }
}
