<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors\Collection;

use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Criterion\Criterion;

/**
 * Class RelationProcessor
 */
class RelationProcessor extends CriterionProcessor
{
    public function process(Criterion $criterion, Collection $collection): Collection
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }
}
