<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors;

use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Builder;

/**
 * Interface Processor
 */
interface Processor
{
    /**
     * @param Builder $builder
     * @return Collection
     */
    public function get(Builder $builder): Collection;

    /**
     * @param Builder $builder
     * @return null|object
     */
    public function first(Builder $builder);

    /**
     * @param Builder $builder
     * @return int
     */
    public function count(Builder $builder): int;
}
