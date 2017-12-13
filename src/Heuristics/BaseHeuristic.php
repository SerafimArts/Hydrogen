<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Heuristics;

use Serafim\Hydrogen\Query\Builder;

/**
 * Class BaseHeuristic
 */
abstract class BaseHeuristic implements Heuristic
{
    /**
     * @param Builder $builder
     * @param string $class
     * @param \Closure $then
     * @return Builder
     */
    protected function match(Builder $builder, string $class, \Closure $then): Builder
    {
        foreach ($builder->getCriteria() as $criterion) {
            if ($criterion instanceof $class) {
                $then($criterion);
            }
        }

        return $builder;
    }
}
