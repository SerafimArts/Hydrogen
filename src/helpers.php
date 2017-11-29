<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace {
    /**
     * This pattern is used to specify the location of the delegate in
     * the function arguments in the high-order messaging.
     *
     * <code>
     *  $array = Collection::make(...)->map->intval(_, 10)->toArray();
     *
     *  // Is similar with:
     *
     *  $array = \array_map(function ($item): int {
     *       return \intval($item, 10);
     *                      ^^^^^ - pattern "_" will replaced to each delegated item value.
     *  }, ...);
     * </code>
     */
    if (! \defined('_')) {
        \define('_', \Serafim\Hydrogen\Collection\HigherOrderCollectionProxy::PATTERN);
    }
}

namespace Serafim\Hydrogen {

    use Serafim\Hydrogen\Query\Builder;

    if (! \function_exists('\\Serafim\\Hydrogen\\Collection')) {
        /**
         * @param iterable $items
         * @return Collection
         */
        function Collection($items): Collection
        {
            return new Collection($items);
        }
    }

    if (! \function_exists('\\Serafim\\Hydrogen\\Query')) {
        /**
         * @return Builder
         */
        function Query(): Builder
        {
            return Query::new();
        }
    }
}
