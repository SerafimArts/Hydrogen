<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Processors\Database;

use Doctrine\ORM\QueryBuilder;
use Serafim\Hydrogen\Criteria\Criterion;
use Serafim\Hydrogen\Criteria\Limit;

/**
 * Class LimitProcessor
 */
class LimitProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|Limit $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    public function process(Criterion $criterion, $builder): QueryBuilder
    {
        return $builder->setMaxResults($criterion->getLimit());
    }
}
