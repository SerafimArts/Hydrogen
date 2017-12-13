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
use Serafim\Hydrogen\Criteria\GroupBy;

/**
 * Class GroupByProcessor
 */
class GroupByProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|GroupBy $criterion
     * @param Collection $collection
     * @return Collection
     */
    public function process(Criterion $criterion, $collection): Collection
    {
        $column = $this->meta->getColumnName($criterion->getGroupBy());

        return $collection->groupBy($column)
            ->map->first()
            ->values();
    }
}
