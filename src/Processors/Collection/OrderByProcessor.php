<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Processors\Collection;

use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Criteria\Criterion;
use Serafim\Hydrogen\Criteria\OrderBy;

/**
 * Class OrderByProcessor
 */
class OrderByProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|OrderBy $criterion
     * @param Collection $collection
     * @return Collection
     */
    public function process(Criterion $criterion, $collection): Collection
    {
        $column = $this->meta->getColumnName($criterion->getField());

        return $collection->sortBy($column, $criterion->isAsc() ? \SORT_ASC : \SORT_DESC);
    }
}
