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
use Serafim\Hydrogen\Criteria\GroupBy;
use Serafim\Hydrogen\Criteria\Criterion;

/**
 * Class GroupByProcessor
 */
class GroupByProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|GroupBy $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    public function process(Criterion $criterion, $builder): QueryBuilder
    {
        $field = $this->fieldName($criterion->getGroupBy());

        return $builder->groupBy($field);
    }
}
