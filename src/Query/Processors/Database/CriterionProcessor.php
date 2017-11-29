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
        try {
            $pattern = \sprintf('f%s', Str::random(7));
        } catch (\Exception $e) {
            $pattern = Str::substr(\md5(\random_int(\PHP_INT_MIN, \PHP_INT_MAX) . $field), 8);
        }

        $this->parameters[$pattern] = $value;

        return ':' . $pattern;
    }

    /**
     * @param string $field
     * @return string
     */
    protected function fieldName(string $field): string
    {
        return Builder::fieldName($field, $this->alias);
    }

    /**
     * @return array
     */
    protected function getParameters(): array
    {
        return $this->parameters;
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
