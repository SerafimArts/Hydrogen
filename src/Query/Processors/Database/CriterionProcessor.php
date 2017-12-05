<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors\Database;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Str;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Criterion\Criterion;

/**
 * Interface CriterionProcessor
 */
abstract class CriterionProcessor
{
    /**
     * @var int
     */
    private static $lastAliasId = 0;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var string
     */
    private $alias;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ClassMetadata
     */
    protected $meta;

    /**
     * BaseCriterion constructor.
     * @param string $alias
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(string $alias, EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->alias = $alias;
        $this->em = $em;
        $this->meta = $meta;
    }

    /**
     * @return string
     */
    protected function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @param Criterion $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     */
    abstract public function process(Criterion $criterion, QueryBuilder $builder): QueryBuilder;

    /**
     * @param string $field
     * @param mixed $value
     * @return string
     * @throws \Exception
     */
    protected function pattern(string $field, $value): string
    {
        $pattern = $this->createAlias('field_', $field);

        $this->parameters[$pattern] = $value;

        return ':' . $pattern;
    }

    /**
     * @param string $prefix
     * @param string|null $seed
     * @return string
     */
    protected function createAlias(string $prefix = 'field_', string $seed = null): string
    {
        $alias = Str::snake(\class_basename($seed ?? $this->meta->getName()));
        $alias = \str_replace('.', '_', $alias);

        if (self::$lastAliasId === \PHP_INT_MAX) {
            self::$lastAliasId = 0;
        }

        return \sprintf('%s%s_%d', $prefix, $alias, ++self::$lastAliasId);
    }

    /**
     * @param string $field
     * @param string|null $alias
     * @return string
     */
    protected function fieldName(string $field, string $alias = null): string
    {
        return Builder::fieldName($field, $alias ?? $this->alias);
    }

    /**
     * @return array
     */
    protected function getParameters(): array
    {
        $parameters = $this->parameters;
        $this->parameters = [];

        return $parameters;
    }

    /**
     * @param QueryBuilder $builder
     * @return \Doctrine\ORM\Query\Expr
     */
    protected function expression(QueryBuilder $builder): Expr
    {
        return $builder->expr();
    }
}
