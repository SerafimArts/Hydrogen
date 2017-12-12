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
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Collection\ArrayHydrator;
use Serafim\Hydrogen\Query\Criterion\GroupBy;
use Serafim\Hydrogen\Query\Criterion\Limit;
use Serafim\Hydrogen\Query\Criterion\Offset;
use Serafim\Hydrogen\Query\Criterion\OrderBy;
use Serafim\Hydrogen\Query\Criterion\Relation;
use Serafim\Hydrogen\Query\Criterion\Select;
use Serafim\Hydrogen\Query\Criterion\Where;
use Serafim\Hydrogen\Query\Heuristics\Heuristic;
use Serafim\Hydrogen\Query\Heuristics\WhereIn;
use Serafim\Hydrogen\Query\Processors\Collection\CriterionProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\GroupByProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\LimitProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\OffsetProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\OrderByProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\RelationProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\SelectionProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\WhereProcessor;

/**
 * Class CollectionBuilder
 */
class CollectionProcessor extends BaseProcessor
{
    /**
     * @var Collection
     */
    private $items;

    /**
     * @var ArrayHydrator
     */
    private $hydrator;

    /**
     * CollectionBuilder constructor.
     * @param Collection $items
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(Collection $items, EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->items    = $items;
        $this->hydrator = new ArrayHydrator($em, $meta);
        parent::__construct($em, $meta);
    }

    /**
     * @param Builder $builder
     * @return Collection
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function get(Builder $builder): Collection
    {
        return $this->query($builder);
    }

    /**
     * @param Builder $builder
     * @return Collection
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function query(Builder $builder): Collection
    {
        $collection = $this->items->map(function ($item): array {
            return (array)$item;
        });

        foreach ($builder->getCriteria() as $criterion) {
            $collection = $this->getCriterionProcessor($criterion, '')
                ->process($criterion, $collection);
        }

        return $this->applyMappings($collection);
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    private function applyMappings(Collection $collection): Collection
    {
        return $collection->map(function (array $data) {
            return $this->hydrator->hydrate($data);
        });
    }

    /**
     * @param Builder $builder
     * @return mixed
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function first(Builder $builder)
    {
        return $this->query($builder)->first();
    }

    /**
     * @param Builder $builder
     * @return int
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function count(Builder $builder): int
    {
        return $this->query($builder)->count();
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
     * @param string $processor
     * @param string $alias
     * @return CriterionProcessor
     */
    final protected function createProcessor(string $processor, string $alias): CriterionProcessor
    {
        return new $processor($this, $this->em, $this->meta);
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
