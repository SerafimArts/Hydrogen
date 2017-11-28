<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query;

/**
 * Interface QueryInterface
 */
interface QueryInterface
{
    public const ORDER_ASC  = 'ASC';
    public const ORDER_DESC = 'DESC';

    /**
     * @param string $field
     * @param mixed $value
     * @return QueryInterface
     */
    public function where(string $field, $value): QueryInterface;

    /**
     * @param string $field
     * @param string $order
     * @return QueryInterface
     */
    public function orderBy(string $field, string $order = self::ORDER_ASC): QueryInterface;

    /**
     * Alias of "limit(...)" method.
     *
     * @param int|null $limit
     * @return QueryInterface
     */
    public function take(?int $limit): QueryInterface;

    /**
     * Alias of "offset(...)" method.
     *
     * @param int|null $offset
     * @return QueryInterface
     */
    public function skip(?int $offset): QueryInterface;

    /**
     * @return array
     */
    public function getCriteria(): array;

    /**
     * @return int|null
     */
    public function getLimit(): ?int;

    /**
     * @return bool
     */
    public function hasLimit(): bool;
    /**
     * @return int|null
     */
    public function getOffset(): ?int;

    /**
     * @return bool
     */
    public function hasOffset(): bool;

    /**
     * @return array|string[]
     */
    public function getOrderBy(): array;
}
