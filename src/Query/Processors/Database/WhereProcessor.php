<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors\Database;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Serafim\Hydrogen\Query\Criterion\Criterion;
use Serafim\Hydrogen\Query\Criterion\Where;

/**
 * Class WhereProcessor
 */
class WhereProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|Where $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    public function process(Criterion $criterion, QueryBuilder $builder): QueryBuilder
    {
        $field = $this->fieldName($criterion->getField());

        $expr = $this->getDoctrineExpression($criterion, $this->expression($builder), $field);

        $query = $criterion->isAnd() ? $builder->andWhere($expr) : $builder->orWhere($expr);

        foreach ($this->getParameters() as $key => $value) {
            $query->setParameter($key, $value);
        }

        return $query;
    }

    /**
     * @param Where $where
     * @param Expr $expr
     * @param string $field
     * @return Expr\Comparison|Expr\Func|string
     * @throws \Exception
     * @throws \InvalidArgumentException
     */
    private function getDoctrineExpression(Where $where, Expr $expr, string $field)
    {
        $operator = $where->getOperator();

        /**
         * Expr:
         * - "X IS NULL"
         * - "X IS NOT NULL"
         */
        if ($where->getValue() === null) {
            switch ($operator) {
                case Where::OPERATOR_EQ:
                    return $expr->isNull($field);
                case Where::OPERATOR_NEQ:
                    return $expr->isNull($field);
            }
        }

        switch ($operator) {
            case Where::OPERATOR_EQ:
                return $expr->eq($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_NEQ:
                return $expr->neq($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_GT:
                return $expr->gt($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_LT:
                return $expr->lt($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_GTE:
                return $expr->gte($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_LTE:
                return $expr->lte($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_IN:
                return $expr->in($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_NOT_IN:
                return $expr->notIn($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_LIKE:
                return $expr->like($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_NOT_LIKE:
                return $expr->notLike($field, $this->pattern($field, $where->getValue()));

            case Where::OPERATOR_BTW:
                return $expr->between(
                    $field,
                    $this->pattern($field, $where->getValue()[0] ?? null),
                    $this->pattern($field, $where->getValue()[1] ?? null)
                );

            case Where::OPERATOR_NOT_BTW:
                return \vsprintf('%s NOT BETWEEN %s AND %s', [
                    $field,
                    $this->pattern($field, $where->getValue()[0] ?? null),
                    $this->pattern($field, $where->getValue()[1] ?? null),
                ]);
        }

        $error = \sprintf('Unexpected "%s" operator type', $operator);
        throw new \InvalidArgumentException($error);
    }
}
