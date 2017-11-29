<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query;

use Serafim\Hydrogen\Query\Criterion\Criterion;
use Serafim\Hydrogen\Query\Criterion\GroupBy;
use Serafim\Hydrogen\Query\Criterion\Limit;
use Serafim\Hydrogen\Query\Criterion\Offset;
use Serafim\Hydrogen\Query\Criterion\OrderBy;
use Serafim\Hydrogen\Query\Criterion\Relation;
use Serafim\Hydrogen\Query\Criterion\Where;

/**
 * Class Builder
 *
 * @property-read Builder $or
 */
class Builder
{
    /**
     * @var array
     */
    private $invariants = [];

    /**
     * @var array|Criterion[]
     */
    protected $criteria = [];

    /**
     * @var bool
     */
    private $thenOr = false;

    /**
     * @return Builder|static|$this
     */
    public static function new(): Builder
    {
        return new static();
    }

    /**
     * @param string $field
     * @param $valueOrOperator
     * @param null $value
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function orWhere(string $field, $valueOrOperator, $value = null): Builder
    {
        return $this->or->where($field, $valueOrOperator, $value);
    }

    /**
     * @param string $field
     * @param $valueOrOperator
     * @param null $value
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function where(string $field, $valueOrOperator, $value = null): Builder
    {
        return $this->add(new Where($field, $valueOrOperator, $value, $this->mode()));
    }

    /**
     * @param Criterion $criterion
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function add(Criterion $criterion): Builder
    {
        if (\in_array(\get_class($criterion), $this->invariants, true)) {
            $error = \sprintf('%s criterion must be an unique.', \class_basename($criterion));
            throw new \LogicException($error);
        }

        if ($criterion->isUnique()) {
            $this->invariants[] = \get_class($criterion);
        }

        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * @return bool
     */
    private function mode(): bool
    {
        $result = ! $this->thenOr;

        $this->thenOr = false;

        return $result;
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function orWhereBetween(string $field, $from, $to): Builder
    {
        return $this->or->whereBetween($field, $from, $to);
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function whereBetween(string $field, $from, $to): Builder
    {
        return $this->add(new Where($field, 'BETWEEN', [$from, $to], $this->mode()));
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function orWhereNotBetween(string $field, $from, $to): Builder
    {
        return $this->or->whereNotBetween($field, $from, $to);
    }

    /**
     * @param string $field
     * @param mixed $from
     * @param mixed $to
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function whereNotBetween(string $field, $from, $to): Builder
    {
        return $this->add(new Where($field, 'NOT BETWEEN', [$from, $to], $this->mode()));
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function orWhereIn(string $field, iterable $value): Builder
    {
        return $this->or->whereIn($field, $value);
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function whereIn(string $field, iterable $value): Builder
    {
        return $this->add(new Where($field, 'IN', $value, $this->mode()));
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function orWhereNotIn(string $field, iterable $value): Builder
    {
        return $this->or->whereNotIn($field, $value);
    }

    /**
     * @param string $field
     * @param iterable $value
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function whereNotIn(string $field, iterable $value): Builder
    {
        return $this->add(new Where($field, 'NOT IN', $value, $this->mode()));
    }

    /**
     * @param string $field
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function asc(string $field): Builder
    {
        return $this->orderBy($field, OrderBy::ORDER_ASC);
    }

    /**
     * @param string $field
     * @param string|null $order
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function orderBy(string $field, string $order = null): Builder
    {
        return $this->add(new OrderBy($field, $order));
    }

    /**
     * @param string $field
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function desc(string $field): Builder
    {
        return $this->orderBy($field, OrderBy::ORDER_DESC);
    }

    /**
     * An alias of "limit(...)"
     *
     * @param int $count
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function take(int $count): Builder
    {
        return $this->limit($count);
    }

    /**
     * @param int $count
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function limit(int $count): Builder
    {
        return $this->add(new Limit($count));
    }

    /**
     * An alias of "offset(...)"
     *
     * @param int $count
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function skip(int $count): Builder
    {
        return $this->offset($count);
    }

    /**
     * @param int $count
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function offset(int $count): Builder
    {
        return $this->add(new Offset($count));
    }

    /**
     * @param int $from
     * @param int $to
     * @return Builder|static|$this
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function range(int $from, int $to): Builder
    {
        if ($from > $to) {
            throw new \InvalidArgumentException('From value must be less than To');
        }

        return $this->limit($from)->offset($to - $from);
    }

    /**
     * @param string[] ...$fields
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function groupBy(string ...$fields): Builder
    {
        foreach ($fields as $field) {
            $this->add(new GroupBy($field));
        }

        return $this;
    }

    /**
     * @param string $field
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function latest(string $field = 'createdAt'): Builder
    {
        return $this->desc($field);
    }

    /**
     * @param string $field
     * @return Builder|static|$this
     * @throws \LogicException
     */
    public function oldest(string $field = 'createdAt'): Builder
    {
        return $this->asc($field);
    }

    /**
     * @param string[] ...$relations
     * @return Builder
     * @throws \LogicException
     */
    public function with(string ...$relations): Builder
    {
        foreach ($relations as $relation) {
            $this->add(new Relation($relation));
        }

        return $this;
    }

    /**
     * @param string $modifier
     * @return null|mixed|Builder
     */
    public function __get(string $modifier)
    {
        switch ($modifier) {
            case 'or':
                $this->thenOr = true;

                return $this;
        }

        return null;
    }

    /**
     * @param string $field
     * @param string|null $alias
     * @return string
     */
    public static function fieldName(string $field, string $alias = null): string
    {
        return $alias === null ? $field : \sprintf('%s.%s', $alias, $field);
    }

    /**
     * @return array|Criterion[]
     */
    public function getCriteria(): array
    {
        return $this->criteria;
    }
}
