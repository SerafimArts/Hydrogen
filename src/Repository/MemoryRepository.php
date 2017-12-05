<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Repository;

use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Processors\CollectionProcessor;
use Serafim\Hydrogen\Query\Processors\Processor;
use Serafim\Hydrogen\Query\Proxy;

/**
 * Class MemoryRepository
 */
abstract class MemoryRepository implements ObjectRepository
{
    /**
     * @var ClassMetadata|ClassMetadataInterface
     */
    private $meta;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * ArrayRepository constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->meta = $meta;
        $this->processor = $this->getProcessor($em);
    }

    /**
     * @param EntityManagerInterface $em
     * @return Processor
     */
    private function getProcessor(EntityManagerInterface $em): Processor
    {
        $data = new Collection($this->getData());

        return new CollectionProcessor($data, $em, $this->meta);
    }

    /**
     * @param int|string $id
     * @return null|object
     * @throws \LogicException
     */
    public function find($id)
    {
        $primary = \array_first($this->meta->getIdentifierFieldNames());

        $query = (new Builder())->where($primary, $id);

        return $this->processor->first($query);
    }

    /**
     * @return iterable|object[]
     */
    abstract protected function getData(): iterable;

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->processor->get(new Builder());
    }

    /**
     * @param Builder $query
     * @return null|object
     */
    public function findOneBy(Builder $query)
    {
        return $this->processor->first($query);
    }

    /**
     * @param Builder $query
     * @return Collection
     */
    public function findBy(Builder $query): Collection
    {
        return $this->processor->get($query);
    }

    /**
     * @param Builder $query
     * @return int
     */
    public function count(Builder $query): int
    {
        return $this->processor->count($query);
    }

    /**
     * @return Builder|Proxy|$this
     */
    public function query(): Builder
    {
        return (new Proxy($this))->scope($this);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->meta->getName();
    }
}
