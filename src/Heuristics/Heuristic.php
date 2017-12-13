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
 * Interface Heuristic
 */
interface Heuristic
{
    /**
     * @param Builder $builder
     * @return Builder
     */
    public function before(Builder $builder): Builder;
}
