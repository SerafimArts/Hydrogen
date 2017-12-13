<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Heuristics;

use Serafim\Hydrogen\Criteria\Relation;
use Serafim\Hydrogen\Query\Builder;

/**
 * Class UniqueRelations
 */
class UniqueRelations extends BaseHeuristic
{
    /**
     * @param Builder $builder
     * @return Builder
     */
    public function before(Builder $builder): Builder
    {
        $relations = [];

        foreach ($builder->getCriteria() as $criterion) {
            if ($criterion instanceof Relation && !$criterion->hasContext()) {
                $relations = $this->simplifyRelation($relations, $builder, $criterion);
            }
        }

        return $builder;
    }

    /**
     * @param array $relations
     * @param Builder $builder
     * @param Relation $relation
     * @return array
     */
    private function simplifyRelation(array $relations, Builder $builder, Relation $relation): array
    {
        if (\in_array($relation->getRelation(), $relations, true)) {
            $builder->removeCriterion($relation);
            return $relations;
        }

        $relations[] = $relation->getRelation();

        return $relations;
    }
}
