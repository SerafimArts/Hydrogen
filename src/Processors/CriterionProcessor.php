<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Processors;

use Serafim\Hydrogen\Criteria\Criterion;

/**
 * Interface CriterionProcessor
 */
interface CriterionProcessor
{
    /**
     * @param Criterion $criterion
     * @param $operand
     * @return mixed
     */
    public function process(Criterion $criterion, $operand);

    /**
     * @return Processor
     */
    public function getProcessor(): Processor;
}
