<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen;

use Serafim\Hydrogen\Query\Proxy;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Criterion\Criterion;
use Serafim\Hydrogen\Repository\ObjectRepository;

/**
 * @method static Builder add(Criterion $criterion)
 * @method static Builder where(string $field, $valueOrOperator, $value = null)
 * @method static Builder orWhere(string $field, $valueOrOperator, $value = null)
 * @method static Builder whereBetween(string $field, $from, $to)
 * @method static Builder orWhereBetween(string $field, $from, $to)
 * @method static Builder whereNotBetween(string $field, $from, $to)
 * @method static Builder orWhereNotBetween(string $field, $from, $to)
 * @method static Builder whereIn(string $field, iterable $value)
 * @method static Builder orWhereIn(string $field, iterable $value)
 * @method static Builder whereNotIn(string $field, iterable $value)
 * @method static Builder orWhereNotIn(string $field, iterable $value)
 * @method static Builder orderBy(string $field, string $order = null)
 * @method static Builder asc(string $field)
 * @method static Builder desc(string $field)
 * @method static Builder take(int $count)
 * @method static Builder limit(int $count)
 * @method static Builder skip(int $count)
 * @method static Builder offset(int $count)
 * @method static Builder range(int $from, int $to)
 * @method static Builder groupBy(string ...$fields)
 * @method static Builder latest(string $field = 'createdAt')
 * @method static Builder oldest(string $field = 'createdAt')
 * @method static Builder with(string ...$relations)
 */
class Query
{
    /**
     * @return void
     * @throws \LogicException
     */
    private function __clone()
    {
        throw new \LogicException(__METHOD__ . ' not allowed');
    }

    /**
     * Query constructor.
     * @throws \LogicException
     */
    private function __construct()
    {
        throw new \LogicException(__METHOD__ . ' not allowed');
    }

    /**
     * @param ObjectRepository $repository
     * @return Proxy|Builder
     */
    public static function from(ObjectRepository $repository): Proxy
    {
        return new Proxy($repository);
    }

    /**
     * @return Builder
     */
    public static function new(): Builder
    {
        return Builder::new();
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return Builder
     */
    public static function __callStatic(string $method, array $arguments = []): Builder
    {
        return static::new()->$method(...$arguments);
    }
}
