<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Repository;

use Serafim\Hydrogen\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Serafim\Hydrogen\Query\Processors\Processor;
use Serafim\Hydrogen\Query\Processors\CollectionProcessor;

/**
 * Class MemoryRepository
 */
abstract class MemoryRepository extends Repository
{
    /**
     * @param EntityManagerInterface $em
     * @return Processor
     */
    protected function getProcessor(EntityManagerInterface $em): Processor
    {
        $data = new Collection($this->getData());

        return new CollectionProcessor($data, $em, $this->meta);
    }

    /**
     * @return iterable|object[]
     */
    abstract protected function getData(): iterable;
}
