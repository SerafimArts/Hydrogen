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
use Serafim\Hydrogen\Query\Criterion\GroupBy;
use Serafim\Hydrogen\Query\Criterion\Criterion;

/**
 * Class GroupByProcessor
 */
class GroupByProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|GroupBy $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    public function process(Criterion $criterion, QueryBuilder $builder): QueryBuilder
    {
        $field = $this->fieldName($criterion->getGroupBy());

        return $builder->groupBy($field);
    }
}
