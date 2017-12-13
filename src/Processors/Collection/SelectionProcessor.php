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
use Serafim\Hydrogen\Criteria\Select;

/**
 * Class SelectionProcessor
 */
class SelectionProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|Select $criterion
     * @param Collection $collection
     * @return Collection
     * @throws \LogicException
     */
    public function process(Criterion $criterion, $collection): Collection
    {
        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }
}
