<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen;

use Serafim\Hydrogen\Query\QueryInterface;
use Serafim\Hydrogen\Query\RawQuery;

/**
 * Class Query
 *
 * @method static RawQuery|QueryInterface where(string $field, $value)
 * @method static RawQuery|QueryInterface orderBy(string $field, string $order = QueryInterface::ORDER_ASC)
 * @method static RawQuery|QueryInterface take(?int $limit)
 * @method static RawQuery|QueryInterface skip(?int $offset)
 */
class Query
{
    /**
     * @return void
     * @throws \LogicException
     */
    private function __clone()
    {
        throw new \LogicException(__METHOD__ . ' is private');
    }

    /**
     * Criterion constructor.
     * @throws \LogicException
     */
    private function __construct()
    {
        throw new \LogicException(__METHOD__ . ' is private');
    }

    /**
     * @return RawQuery|QueryInterface
     */
    public static function new(): QueryInterface
    {
        return new RawQuery();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments = [])
    {
        return static::new()->$method(...$arguments);
    }
}
