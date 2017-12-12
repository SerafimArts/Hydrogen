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
use Serafim\Hydrogen\Query\Criterion\Select;
use Serafim\Hydrogen\Query\Criterion\Where;
use Serafim\Hydrogen\Query\Heuristics\Heuristic;
use Serafim\Hydrogen\Query\Heuristics\WhereIn;
use Serafim\Hydrogen\Query\Processors\Database\CriterionProcessor;
use Serafim\Hydrogen\Query\Processors\Database\GroupByProcessor;
use Serafim\Hydrogen\Query\Processors\Database\LimitProcessor;
use Serafim\Hydrogen\Query\Processors\Database\OffsetProcessor;
use Serafim\Hydrogen\Query\Processors\Database\OrderByProcessor;
use Serafim\Hydrogen\Query\Processors\Database\RelationProcessor;
use Serafim\Hydrogen\Query\Processors\Database\SelectionProcessor;
use Serafim\Hydrogen\Query\Processors\Database\WhereProcessor;

/**
 * Class DatabaseBuilder
 */
class DatabaseProcessor extends BaseProcessor
{
    /**
     * @var int
     */
    private static $lastSelectionId = 0;

    /**
     * @var string
     */
    private $alias;

    /**
     * DatabaseBuilder constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     * @param string|null $alias
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta, string $alias = null)
    {
        $this->alias = $alias ?? $this->createAlias($meta);

        parent::__construct($em, $meta);
    }

    /**
     * @param ClassMetadata $meta
     * @param string|null $seed
     * @return string
     */
    private function createAlias(ClassMetadata $meta, string $seed = null): string
    {
        return \vsprintf('%s_%s', [
            Str::snake(\class_basename($seed ?? $meta->getName())),
            ++self::$lastSelectionId,
        ]);
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
        $query = $this->createQueryBuilder($this->alias);

        return $this->applyQueryBuilder($query, $builder);
    }

    /**
     * @param string $alias
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    protected function createQueryBuilder(string $alias): QueryBuilder
    {
        $builder = new QueryBuilder($this->em);
        $builder->select($alias);
        $builder->from($this->meta->getName(), $alias);

        return $builder;
    }

    /**
     * @param QueryBuilder|Collection $query
     * @param Builder $builder
     * @param string|null $alias
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    public function applyQueryBuilder(QueryBuilder $query, Builder $builder, string $alias = null): QueryBuilder
    {
        //
        // Make sure that the Builder is immutable. Do not touch him.
        //
        $builder = clone $builder;

        //
        // Apply global query optimisations.
        //
        $builder = $this->optimiseQuery($builder);

        //
        // Build query
        //
        foreach ($builder->getCriteria() as $criterion) {
            $query = $this->getCriterionProcessor($criterion, $alias ?? $this->alias)
                ->process($criterion, $query);
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
            $query = $this->query($builder);
            $query->setMaxResults(1);

            return $query->getQuery()->getOneOrNullResult();
        } catch (\InvalidArgumentException $empty) {
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
     * @param Builder $builder
     * @return string
     * @throws \InvalidArgumentException
     */
    public function toSql(Builder $builder): string
    {
        return $this->query($builder)->getQuery()->getSQL();
    }

    /**
     * @param string $processor
     * @param string $alias
     * @return CriterionProcessor
     */
    final protected function createProcessor(string $processor, string $alias): CriterionProcessor
    {
        return new $processor($this, $alias, $this->em, $this->meta);
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
            Select::class   => SelectionProcessor::class,
        ];
    }

    /**
     * @return iterable|string[]|Heuristic[]
     */
    final protected function getHeuristics(): iterable
    {
        return [
            WhereIn::class,
        ];
    }
}
