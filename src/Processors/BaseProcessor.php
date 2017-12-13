<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Processors;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Criteria\Criterion;
use Serafim\Hydrogen\Heuristics;
use Serafim\Hydrogen\Processors\Database\CriterionProcessor as DatabaseCriterionProcessor;
use Serafim\Hydrogen\Processors\Collection\CriterionProcessor as CollectionCriterionProcessor;

/**
 * Class BaseProcessor
 */
abstract class BaseProcessor implements Processor
{
    /**
     * @var array|string[]
     */
    private $processors = [];

    /**
     * @var array|Heuristics\Heuristic[]
     */
    private $heuristics = [];

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ClassMetadata
     */
    protected $meta;

    /**
     * BaseProcessor constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->em = $em;
        $this->meta = $meta;

        $this->bootProcessors();
        $this->bootHeuristics();
    }

    /**
     * @return void
     */
    private function bootProcessors(): void
    {
        foreach ($this->getProcessorMappings() as $name => $class) {
            $this->processors[$name] = $class;;
        }
    }

    /**
     * @return void
     */
    private function bootHeuristics(): void
    {
        foreach ($this->getHeuristics() as $heuristic) {
            $this->heuristics[] = new $heuristic($this->em, $this->meta);
        }
    }

    /**
     * @param string $processor
     * @param ClassMetadata $meta
     * @param string $alias
     * @return DatabaseCriterionProcessor|CollectionCriterionProcessor|object
     */
    abstract protected function createProcessor(string $processor, ClassMetadata $meta, string $alias);

    /**
     * @return iterable
     */
    abstract protected function getProcessorMappings(): iterable;

    /**
     * @return iterable|Heuristics\Heuristic[]|string[]
     */
    protected function getHeuristics(): iterable
    {
        return [
            Heuristics\WhereIn::class,
            Heuristics\UniqueRelations::class,
        ];
    }

    /**
     * @param Criterion $criterion
     * @param ClassMetadata $meta
     * @param string $alias
     * @return DatabaseCriterionProcessor|CollectionCriterionProcessor
     * @throws \InvalidArgumentException
     */
    protected function getCriterionProcessor(Criterion $criterion, ClassMetadata $meta, string $alias): CriterionProcessor
    {
        $class = \get_class($criterion);

        if (! \array_key_exists($class, $this->processors)) {
            $error = \sprintf('Unprocessable criterion "%s"', $class);
            throw new \InvalidArgumentException($error);
        }

        return $this->createProcessor($this->processors[$class], $meta, $alias);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function optimiseQuery(Builder $query): Builder
    {
        /** @var Heuristic $heuristic */
        foreach ($this->heuristics as $heuristic) {
            $query = $heuristic->before($query);
        }

        return $query;
    }
}
