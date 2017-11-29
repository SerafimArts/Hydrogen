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
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Processors\DatabaseProcessor;
use Serafim\Hydrogen\Query\Processors\Processor;

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
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var EntityManager|EntityManagerInterface
     */
    private $em;

    /**
     * DatabaseRepository constructor.
     * @param EntityManagerInterface|EntityManager $em
     * @param ClassMetadata|ClassMetadataInterface $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->em = $em;
        $this->meta = $meta;
        $this->repository = new EntityRepository($em, $meta);
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

        return $this->process()->first($query);
    }

    /**
     * @param string|null $alias
     * @return Processor
     * @throws \Exception
     */
    protected function process(string $alias = null): Processor
    {
        return new DatabaseProcessor($this->em, $this->meta, $alias);
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function findAll(): Collection
    {
        return new Collection($this->process()->get(new Builder()));
    }

    /**
     * @param Builder $query
     * @return null|object
     * @throws \Exception
     */
    public function findOneBy(Builder $query)
    {
        return $this->process()->first($query);
    }

    /**
     * @param Builder $query
     * @return Collection
     * @throws \Exception
     */
    public function findBy(Builder $query): Collection
    {
        return $this->process()->get($query);
    }

    /**
     * @param Builder $query
     * @return int
     * @throws \Exception
     */
    public function count(Builder $query): int
    {
        return $this->process()->count($query);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->repository->getClassName();
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return new Builder();
    }
}
