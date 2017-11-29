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

/**
 * Class CriterionProcessor
 */
abstract class CriterionProcessor
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
     * CriterionProcessor constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->em = $em;
        $this->meta = $meta;
    }

    /**
     * @param Criterion $criterion
     * @param Collection $collection
     * @return Collection
     */
    abstract public function process(Criterion $criterion, Collection $collection): Collection;
}
