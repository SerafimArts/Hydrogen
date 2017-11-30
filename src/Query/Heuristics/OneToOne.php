<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Heuristics;

use Serafim\Hydrogen\Query\Builder;

/**
 * Class OneToOne
 */
class OneToOne implements Heuristic
{
    public function optimiseQuery(Builder $builder): Builder
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }
}
