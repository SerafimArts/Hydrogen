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
use Serafim\Hydrogen\Query\Criterion\Criterion;
use Serafim\Hydrogen\Query\Processors\Database\CriterionProcessor as DatabaseCriterionProcessor;
use Serafim\Hydrogen\Query\Processors\Collection\CriterionProcessor as CollectionCriterionProcessor;

/**
 * Class BaseProcessor
 */
abstract class BaseProcessor implements Processor
{
    /**
     * @var array|DatabaseCriterionProcessor[]|CollectionCriterionProcessor[]
     */
    private $processors = [];

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
    }

    /**
     * @return void
     */
    private function bootProcessors(): void
    {
        foreach ($this->getProcessorMappings() as $name => $class) {
            $this->processors[$name] = $this->createProcessor($class);
        }
    }

    /**
     * @param string $processor
     * @return DatabaseCriterionProcessor|CollectionCriterionProcessor|object
     */
    abstract protected function createProcessor(string $processor);

    /**
     * @return iterable
     */
    abstract protected function getProcessorMappings(): iterable;

    /**
     * @param Criterion $criterion
     * @return DatabaseCriterionProcessor|CollectionCriterionProcessor
     * @throws \InvalidArgumentException
     */
    protected function getProcessor(Criterion $criterion)
    {
        $class = \get_class($criterion);

        if (! \array_key_exists($class, $this->processors)) {
            $error = \sprintf('Unprocessable criterion "%s"', $class);
            throw new \InvalidArgumentException($error);
        }

        return $this->processors[$class];
    }
}
