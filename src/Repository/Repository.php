<?php
/**
 * This file is part of railt.org package.
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
use Serafim\Hydrogen\Query\Processors\Processor;
use Serafim\Hydrogen\Query\Proxy;

/**
 * Class Repository
 */
abstract class Repository implements ObjectRepository
{
    /**
     * @var ClassMetadata|ClassMetadataInterface
     */
    protected $meta;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * ArrayRepository constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->meta      = $meta;
        $this->processor = $this->getProcessor($em);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    protected function scope(Builder $query): Builder
    {
        return $query;
    }

    /**
     * @param EntityManagerInterface $em
     * @return Processor
     */
    abstract protected function getProcessor(EntityManagerInterface $em): Processor;

    /**
     * @param int|string $id
     * @return null|object
     * @throws \LogicException
     */
    public function find($id)
    {
        $primary = \array_first($this->meta->getIdentifierFieldNames());

        $query = $this->query()->where($primary, $id);

        return $this->processor->first($query);
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->findBy($this->query());
    }

    /**
     * @param Builder $query
     * @return null|object
     */
    public function findOneBy(Builder $query)
    {
        return $this->processor->first($this->scope($query));
    }

    /**
     * @param Builder $query
     * @return Collection
     */
    public function findBy(Builder $query): Collection
    {
        return $this->processor->get($this->scope($query));
    }

    /**
     * @param Builder $query
     * @return int
     */
    public function count(Builder $query): int
    {
        return $this->processor->count($this->scope($query));
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
        return $this->scope($this->clearQuery());
    }

    /**
     * @return Builder
     */
    public function clearQuery(): Builder
    {
        return (new Proxy($this))->scope($this);
    }

    /**
     * @param string $name
     * @return Builder|Proxy|Repository|$this
     * @throws \InvalidArgumentException
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'query':
                return $this->query();
            case 'clearQuery':
                return $this->clearQuery();
        }

        throw new \InvalidArgumentException('Property ' . $name . ' does not exists');
    }
}
