<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Criterion;

/**
 * Class Relation
 */
class Relation extends BaseCriterion
{
    /**
     * @var string
     */
    private $relation;

    /**
     * Relation constructor.
     * @param string $relation
     */
    public function __construct(string $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }
}
