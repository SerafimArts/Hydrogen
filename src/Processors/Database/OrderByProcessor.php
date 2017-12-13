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
use Serafim\Hydrogen\Criteria\OrderBy;
use Serafim\Hydrogen\Criteria\Criterion;

/**
 * Class OrderByProcessor
 */
class OrderByProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|OrderBy $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    public function process(Criterion $criterion, $builder): QueryBuilder
    {
        $field = $this->fieldName($criterion->getField());

        if ($criterion->isAsc()) {
            return $builder->orderBy($field, 'ASC');
        }

        if ($criterion->isDesc()) {
            return $builder->orderBy($field, 'DESC');
        }

        $error = \sprintf('Invalid order type "%s"', $criterion->getOrder());
        throw new \InvalidArgumentException($error);
    }
}
