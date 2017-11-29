<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Criterion;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Str;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Query\Builder;

/**
 * Class BaseCriterion
 */
abstract class BaseCriterion implements Criterion
{
    /**
     * @var array
     */
    private $patterns = [];

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return false;
    }

    /**
     * @param string $alias
     * @param ClassMetadata $meta
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws \LogicException
     */
    public function getExpression(string $alias, ClassMetadata $meta, QueryBuilder $builder): QueryBuilder
    {
        $error = 'Criterion "%s" does not support an operations over SQL queries.';
        throw new \LogicException(\sprintf($error, $this));
    }

    /**
     * @param ClassMetadata $metadata
     * @param Collection $collection
     * @return Collection
     * @throws \LogicException
     */
    public function getCollection(ClassMetadata $metadata, Collection $collection): Collection
    {
        $error = 'Criterion "%s" does not support an operations over data collection';
        throw new \LogicException(\sprintf($error, $this));
    }

    /**
     * @param string $field
     * @param mixed $value
     * @return string
     * @throws \Exception
     */
    protected function pattern(string $field, $value): string
    {
        try {
            $pattern = \sprintf('f%s', Str::random(8));
        } catch (\Exception $e) {
            $pattern = \md5(\random_int(\PHP_INT_MIN, \PHP_INT_MAX) . $field);
        }

        $this->patterns[$pattern] = $value;

        return ':' . $pattern;
    }

    /**
     * @return array
     */
    protected function getParameters(): array
    {
        return $this->patterns;
    }

    /**
     * @param QueryBuilder $builder
     * @return \Doctrine\ORM\Query\Expr
     */
    protected function expression(QueryBuilder $builder): Expr
    {
        return $builder->expr();
    }

    /**
     * @param string $field
     * @param string|null $alias
     * @return string
     */
    protected function fieldName(string $field, string $alias = null): string
    {
        return Builder::fieldName($field, $alias);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \class_basename(static::class);
    }
}
