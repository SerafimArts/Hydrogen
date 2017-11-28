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
use Doctrine\Common\Persistence\ObjectRepository as OriginalRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection\Collection;
use Serafim\Hydrogen\Query;

/**
 * Class DatabaseRepository
 */
abstract class DatabaseRepository implements ObjectRepository
{
    /**
     * @var OriginalRepository
     */
    private $original;

    /**
     * DatabaseRepository constructor.
     * @param EntityManagerInterface|EntityManager $em
     * @param ClassMetadata|ClassMetadataInterface $class
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        $this->original = new EntityRepository($em, $class);
    }

    /**
     * @param int|string $id
     * @return null|object
     */
    public function find($id)
    {
        return $this->original->find($id);
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return new Collection($this->original->findAll());
    }

    /**
     * @param Query $query
     * @return null|object
     */
    public function findOneBy(Query $query)
    {
        return $this->original->findOneBy(...$query->toArray());
    }

    /**
     * @param Query $query
     * @return Collection
     */
    public function findBy(Query $query): Collection
    {
        $result = $this->original->findBy(...$query->toArray());

        return new Collection($result);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->original->getClassName();
    }
}
