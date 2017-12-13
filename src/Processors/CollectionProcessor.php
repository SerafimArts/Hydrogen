<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Processors;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Criteria\GroupBy;
use Serafim\Hydrogen\Criteria\Limit;
use Serafim\Hydrogen\Criteria\Offset;
use Serafim\Hydrogen\Criteria\OrderBy;
use Serafim\Hydrogen\Criteria\Relation;
use Serafim\Hydrogen\Criteria\Select;
use Serafim\Hydrogen\Criteria\Where;
use Serafim\Hydrogen\Processors\Collection\CriterionProcessor;
use Serafim\Hydrogen\Processors\Collection\GroupByProcessor;
use Serafim\Hydrogen\Processors\Collection\LimitProcessor;
use Serafim\Hydrogen\Processors\Collection\OffsetProcessor;
use Serafim\Hydrogen\Processors\Collection\OrderByProcessor;
use Serafim\Hydrogen\Processors\Collection\RelationProcessor;
use Serafim\Hydrogen\Processors\Collection\SelectionProcessor;
use Serafim\Hydrogen\Processors\Collection\WhereProcessor;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Heuristics\Heuristic;
use Serafim\Hydrogen\Heuristics\WhereIn;
use Serafim\Hydrogen\Query\Hydrator\ArrayHydrator;

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
     * @param ClassMetadata $meta
     * @param string $alias
     * @return CriterionProcessor
     */
    final protected function createProcessor(string $processor, ClassMetadata $meta, string $alias): CriterionProcessor
    {
        return new $processor($this, $this->em, $meta);
    }

    /**
     * @return iterable|string[]|Heuristic[]
     */
    final protected function getHeuristics(): iterable
    {
        return \array_merge([
            //
        ], parent::getHeuristics());
    }
}
