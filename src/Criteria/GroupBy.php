<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Criteria;

use Doctrine\ORM\Mapping\ClassMetadata;
use Serafim\Hydrogen\Collection;

/**
 * Class GroupBy
 */
class GroupBy extends BaseCriterion
{
    /**
     * @var string
     */
    private $groupBy;

    /**
     * GroupBy constructor.
     * @param string $groupBy
     */
    public function __construct(string $groupBy)
    {
        $this->groupBy = $groupBy;
    }

    /**
     * @return string
     */
    public function getGroupBy(): string
    {
        return $this->groupBy;
    }

    /**
     * @param ClassMetadata $meta
     * @param Collection $collection
     * @return Collection
     */
    public function getCollection(ClassMetadata $meta, Collection $collection): Collection
    {
        $column = $meta->getColumnName($this->groupBy);

        $values = $collection->groupBy($column)->map->first()->values();

        return new Collection($values);
    }
}
