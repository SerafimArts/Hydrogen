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
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ArrayRepository constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->meta = $meta;
        $this->em   = $em;
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

        return $this->process()->first($query);
    }

    /**
     * @return Processor
     */
    protected function process(): Processor
    {
        return new CollectionProcessor(new Collection($this->getData()), $this->em, $this->meta);
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
        return $this->process()->get(new Builder());
    }

    /**
     * @param Builder $query
     * @return null|object
     */
    public function findOneBy(Builder $query)
    {
        return $this->process()->first($query);
    }

    /**
     * @param Builder $query
     * @return Collection
     */
    public function findBy(Builder $query): Collection
    {
        return $this->process()->get($query);
    }

    /**
     * @param Builder $query
     * @return int
     */
    public function count(Builder $query): int
    {
        return $this->process()->count($query);
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return new Builder();
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->meta->getName();
    }
}
