<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Criteria;

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
     * @var \Closure
     */
    private $context;

    /**
     * @var bool
     */
    private $hasContext;

    /**
     * Relation constructor.
     * @param $relation
     * @param \Closure|null $context
     */
    public function __construct($relation, \Closure $context = null)
    {
        $this->relation = $relation;
        $this->context  = $context ?? function () {};
        $this->hasContext = $context !== null;
    }

    /**
     * @return bool
     */
    public function hasContext(): bool
    {
        return $this->hasContext;
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * @return \Closure
     */
    public function getContext(): \Closure
    {
        return $this->context;
    }
}
