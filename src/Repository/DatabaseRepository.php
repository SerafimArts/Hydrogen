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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Processors\DatabaseProcessor;
use Serafim\Hydrogen\Query\Processors\Processor;
use Serafim\Hydrogen\Query\Proxy;

/**
 * Class DatabaseRepository
 */
abstract class DatabaseRepository implements ObjectRepository
{
    /**
     * @var ClassMetadataInterface|ClassMetadata
     */
    private $meta;

    /**
     * @var Processor|DatabaseProcessor
     */
    private $processor;

    /**
     * DatabaseRepository constructor.
     * @param EntityManagerInterface|EntityManager $em
     * @param ClassMetadata|ClassMetadataInterface $meta
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->meta = $meta;
        $this->processor = new DatabaseProcessor($em, $this->meta);
    }

    /**
     * @param int|string $id
     * @return null|object
     * @throws \Exception
     */
    public function find($id)
    {
        $primary = \array_first($this->meta->getIdentifierFieldNames());

        $query = (new Builder())->where($primary, $id);

        return $this->processor->first($query);
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function findAll(): Collection
    {
        return new Collection($this->processor->get(new Builder()));
    }

    /**
     * @param Builder $query
     * @return string
     * @throws \InvalidArgumentException
     */
    public function dql(Builder $query): string
    {
        return $this->processor->toDql($query);
    }

    /**
     * @param Builder $query
     * @return string
     */
    public function sql(Builder $query): string
    {
        return $this->processor->toSql($query);
    }

    /**
     * @param Builder $query
     * @return null|object
     * @throws \Exception
     */
    public function findOneBy(Builder $query)
    {
        return $this->processor->first($query);
    }

    /**
     * @param Builder $query
     * @return Collection
     * @throws \Exception
     */
    public function findBy(Builder $query): Collection
    {
        return $this->processor->get($query);
    }

    /**
     * @param Builder $query
     * @return int
     * @throws \Exception
     */
    public function count(Builder $query): int
    {
        return $this->processor->count($query);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->meta->getName();
    }

    /**
     * @return Builder|Proxy|$this
     */
    public function query(): Builder
    {
        return (new Proxy($this))->scope($this);
    }
}
