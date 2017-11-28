<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection\Collection;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Serafim\Hydrogen\Query;

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
     * ArrayRepository constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->meta = $meta;
    }

    /**
     * @param mixed $id
     * @return object|null
     * @throws \LogicException
     */
    public function find($id)
    {
        $criterion = Query::new()->where(\array_first($this->meta->getIdentifier()), $id);

        return $this->findOneBy($criterion);
    }

    /**
     * @param Query $query
     * @return object|null
     */
    public function findOneBy(Query $query)
    {
        return $this->findBy($query)->first();
    }

    /**
     * @param Query $query
     * @return Collection
     */
    public function findBy(Query $query): Collection
    {
        $collection = $this->findAll();

        foreach ($query->getCriteria() as $key => $value) {
            $collection = $collection->filter(function ($entity) use ($key, $value): bool {
                return $this->meta->getFieldValue($entity, $key) === $value;
            });
        }

        foreach ($query->getOrderBy() as $field => $order) {
            $sort = $order === Query::ORDER_ASC ? \SORT_ASC : \SORT_DESC;

            $collection = $collection->sortBy(function ($a, $b) use ($field): int {
                return $this->meta->getFieldValue($a, $field) <=> $this->meta->getFieldValue($b, $field);
            }, $sort);
        }

        switch (true) {
            case $query->hasOffset():
                return $collection->slice($query->getOffset(), $query->getLimit());

            case $query->hasLimit():
                return $collection->take($query->getLimit());
        }

        return $collection;
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return new Collection($this->getData());
    }

    /**
     * @return iterable|object[]
     */
    abstract protected function getData(): iterable;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->meta->getName();
    }
}
