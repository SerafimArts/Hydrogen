<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors\Database;

use Doctrine\ORM\QueryBuilder;
use Serafim\Hydrogen\Query\Criterion\Criterion;
use Serafim\Hydrogen\Query\Criterion\Select;

/**
 * Class SelectionProcessor
 */
class SelectionProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|Select $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    public function process(Criterion $criterion, $builder): QueryBuilder
    {
        return $builder->addSelect($criterion->getSelection());
    }
}
