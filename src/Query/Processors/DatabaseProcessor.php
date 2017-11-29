<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Str;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Criterion\GroupBy;
use Serafim\Hydrogen\Query\Criterion\Limit;
use Serafim\Hydrogen\Query\Criterion\Offset;
use Serafim\Hydrogen\Query\Criterion\OrderBy;
use Serafim\Hydrogen\Query\Criterion\Relation;
use Serafim\Hydrogen\Query\Criterion\Where;
use Serafim\Hydrogen\Query\Processors\Database\CriterionProcessor;
use Serafim\Hydrogen\Query\Processors\Database\GroupByProcessor;
use Serafim\Hydrogen\Query\Processors\Database\LimitProcessor;
use Serafim\Hydrogen\Query\Processors\Database\OffsetProcessor;
use Serafim\Hydrogen\Query\Processors\Database\OrderByProcessor;
use Serafim\Hydrogen\Query\Processors\Database\RelationProcessor;
use Serafim\Hydrogen\Query\Processors\Database\WhereProcessor;

/**
 * Class DatabaseBuilder
 */
class DatabaseProcessor extends BaseProcessor
{
    /**
     * @var string
     */
    private $alias;

    /**
     * @var QueryBuilder
     */
    private $query;

    /**
     * DatabaseBuilder constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     * @param string|null $alias
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta, string $alias = null)
    {
        $this->alias = $alias ?? $this->createAlias();
        $this->query = $this->bootQuery($this->alias, $em, $meta);

        parent::__construct($em, $meta);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function createAlias(): string
    {
        return 'q' . Str::lower(Str::random(4));
    }

    /**
     * @param string $alias
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    private function bootQuery(string $alias, EntityManagerInterface $em, ClassMetadata $meta): QueryBuilder
    {
        return $em->createQueryBuilder()
            ->select($alias)
            ->from($meta->getReflectionClass()->getName(), $alias);
    }

    /**
     * @param Builder $builder
     * @return Collection
     * @throws \InvalidArgumentException
     */
    public function get(Builder $builder): Collection
    {
        $result = $this->query($builder)->getQuery()->getResult();

        return new Collection($result);
    }

    /**
     * @param Builder $builder
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    private function query(Builder $builder): QueryBuilder
    {
        $query = clone $this->query;

        foreach ($builder->getCriteria() as $criterion) {
            $query = $this->getProcessor($criterion)->process($criterion, clone $query);
        }

        return $query;
    }

    /**
     * @param Builder $builder
     * @return null|object
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function first(Builder $builder)
    {
        try {
            return $this->query($builder)->getQuery()->getSingleResult();
        } catch (NoResultException | \InvalidArgumentException $empty) {
            return null;
        }
    }

    /**
     * @param Builder $builder
     * @return int
     * @throws \InvalidArgumentException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function count(Builder $builder): int
    {
        $query = $this->query($builder);

        $expr = $query->expr()->count(Builder::fieldName($this->getPrimaryKey(), $this->alias));

        try {
            return (int)$query->select($expr)->getQuery()->getSingleScalarResult();
        } catch (NoResultException | \InvalidArgumentException $empty) {
            return 0;
        }
    }

    /**
     * @return string
     */
    private function getPrimaryKey(): string
    {
        return \array_first($this->meta->getIdentifierFieldNames());
    }

    /**
     * @param Builder $builder
     * @return string
     * @throws \InvalidArgumentException
     */
    public function toDql(Builder $builder): string
    {
        return $this->query($builder)->getDQL();
    }

    /**
     * @param string $processor
     * @return CriterionProcessor
     */
    final protected function createProcessor(string $processor): CriterionProcessor
    {
        return new $processor($this->alias, $this->em, $this->meta);
    }

    /**
     * @return iterable
     */
    final protected function getProcessorMappings(): iterable
    {
        return [
            GroupBy::class  => GroupByProcessor::class,
            Limit::class    => LimitProcessor::class,
            Offset::class   => OffsetProcessor::class,
            OrderBy::class  => OrderByProcessor::class,
            Relation::class => RelationProcessor::class,
            Where::class    => WhereProcessor::class,
        ];
    }
}
