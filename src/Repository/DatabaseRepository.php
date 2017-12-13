<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Processors\DatabaseProcessor;
use Serafim\Hydrogen\Processors\Processor;

/**
 * Class DatabaseRepository
 * @property-read DatabaseProcessor $processor
 */
abstract class DatabaseRepository extends Repository
{
    /**
     * @param Builder $query
     * @return string
     * @throws \InvalidArgumentException
     */
    public function dql(Builder $query): string
    {
        return $this->processor->toDql($query);
    }

    /**
     * @param Builder $query
     * @return string
     * @throws \InvalidArgumentException
     */
    public function sql(Builder $query): string
    {
        return $this->processor->toSql($query);
    }

    /**
     * @param EntityManagerInterface $em
     * @return Processor
     */
    protected function getProcessor(EntityManagerInterface $em): Processor
    {
        return new DatabaseProcessor($em, $this->meta);
    }
}
