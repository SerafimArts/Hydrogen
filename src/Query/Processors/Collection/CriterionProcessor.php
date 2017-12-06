<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors\Collection;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Criterion\Criterion;
use Serafim\Hydrogen\Query\Processors\CollectionProcessor;
use Serafim\Hydrogen\Query\Processors\CriterionProcessor as CriterionProcessorInterface;
use Serafim\Hydrogen\Query\Processors\Processor;

/**
 * Class CriterionProcessor
 */
abstract class CriterionProcessor implements CriterionProcessorInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ClassMetadata
     */
    protected $meta;

    /**
     * @var CollectionProcessor
     */
    private $processor;

    /**
     * CriterionProcessor constructor.
     * @param CollectionProcessor $processor
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(CollectionProcessor $processor, EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->em   = $em;
        $this->meta = $meta;
        $this->processor = $processor;
    }

    /**
     * @return Processor|CollectionProcessor
     */
    public function getProcessor(): Processor
    {
        return $this->processor;
    }

    /**
     * @param Criterion $criterion
     * @param Collection $collection
     * @return Collection
     */
    abstract public function process(Criterion $criterion, $collection): Collection;
}
