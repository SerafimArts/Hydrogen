<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen;

use Serafim\Hydrogen\Query\Criterion;

/**
 * Class Query
 *
 * @method static Criterion where(string $field, $value)
 * @method static Criterion orderBy(string $field, string $order = Criterion::ORDER_ASC)
 * @method static Criterion take(?int $limit)
 * @method static Criterion skip(?int $offset)
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
     * @return Criterion
     */
    public static function new(): Criterion
    {
        return new Criterion();
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
