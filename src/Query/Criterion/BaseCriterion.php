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
 * Class BaseCriterion
 */
abstract class BaseCriterion implements Criterion
{
    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return false;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \class_basename(static::class);
    }
}
