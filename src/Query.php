<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Query
 */
class Query implements Arrayable
{
    public const ORDER_ASC  = 'ASC';
    public const ORDER_DESC = 'DESC';

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
     * @return static
     */
    public static function new(): self
    {
        return new static();
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return Query
     */
    public function where(string $field, $value): self
    {
        $this->criteria[$field] = $value;

        return $this;
    }

    /**
     * @param string $field
     * @param string $order
     * @return Query
     */
    public function orderBy(string $field, string $order = self::ORDER_ASC): self
    {
        $this->orderBy[$field] = $order;

        return $this;
    }

    /**
     * Alias of "limit(...)" method.
     *
     * @param int|null $limit
     * @return Query
     */
    public function take(?int $limit): self
    {
        return $this->limit($limit);
    }

    /**
     * Alias of "offset(...)" method.
     *
     * @param int|null $offset
     * @return Query
     */
    public function skip(?int $offset): self
    {
        return $this->offset($offset);
    }

    /**
     * @param int|null $limit
     * @return Query
     */
    public function limit(?int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int|null $offset
     * @return Query
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

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            $this->getCriteria(),
            $this->getOrderBy(),
            $this->getLimit(),
            $this->getOffset()
        ];
    }
}
