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
 * Class RawQuery
 */
class RawQuery implements QueryInterface
{
    /**
     * @var array
     */
    private $criteria = [];

    /**
     * @var null|int
     */
    private $limit;

    /**
     * @var null|int
     */
    private $offset;

    /**
     * @var array|string[]
     */
    private $orderBy = [];

    /**
     * @param string $field
     * @param mixed $value
     * @return RawQuery|QueryInterface|$this
     */
    public function where(string $field, $value): QueryInterface
    {
        $this->criteria[$field] = $value;

        return $this;
    }

    /**
     * @param string $field
     * @param string $order
     * @return RawQuery|QueryInterface|$this
     */
    public function orderBy(string $field, string $order = self::ORDER_ASC): QueryInterface
    {
        $this->orderBy[$field] = $order;

        return $this;
    }

    /**
     * Alias of "limit(...)" method.
     *
     * @param int|null $limit
     * @return RawQuery|QueryInterface|$this
     */
    public function take(?int $limit): QueryInterface
    {
        return $this->limit($limit);
    }

    /**
     * Alias of "offset(...)" method.
     *
     * @param int|null $offset
     * @return RawQuery|QueryInterface|$this
     */
    public function skip(?int $offset): QueryInterface
    {
        return $this->offset($offset);
    }

    /**
     * @param int|null $limit
     * @return RawQuery|QueryInterface|$this
     */
    public function limit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int|null $offset
     * @return RawQuery|QueryInterface|$this
     */
    public function offset(?int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return array
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return bool
     */
    public function hasLimit(): bool
    {
        return $this->limit !== null;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return bool
     */
    public function hasOffset(): bool
    {
        return $this->offset !== null;
    }

    /**
     * @return array|string[]
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }
}
