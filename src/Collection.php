<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen;

use Illuminate\Support\Collection as BaseCollection;
use Serafim\Hydrogen\Collection\HigherOrderCollectionProxy;

/**
 * Proxies autocomplete.
 *
 * @property-read HigherOrderCollectionProxy $average
 * @property-read HigherOrderCollectionProxy $avg
 * @property-read HigherOrderCollectionProxy $contains
 * @property-read HigherOrderCollectionProxy $each
 * @property-read HigherOrderCollectionProxy $every
 * @property-read HigherOrderCollectionProxy $filter
 * @property-read HigherOrderCollectionProxy $first
 * @property-read HigherOrderCollectionProxy $flatMap
 * @property-read HigherOrderCollectionProxy $keyBy
 * @property-read HigherOrderCollectionProxy $map
 * @property-read HigherOrderCollectionProxy $partition
 * @property-read HigherOrderCollectionProxy $reject
 * @property-read HigherOrderCollectionProxy $sortBy
 * @property-read HigherOrderCollectionProxy $sortByDesc
 * @property-read HigherOrderCollectionProxy $sum
 */
class Collection extends BaseCollection
{
    /**
     * @param string $key
     * @return HigherOrderCollectionProxy
     * @throws \InvalidArgumentException
     */
    final public function __get($key)
    {
        if (! \in_array($key, static::$proxies, true)) {
            $error = \sprintf('Property [%s] does not exist on this collection instance.', $key);
            throw new \InvalidArgumentException($error);
        }

        return new HigherOrderCollectionProxy($this, $key);
    }
}
