<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Heuristics;

use Serafim\Hydrogen\Criteria\Where;
use Serafim\Hydrogen\Query\Builder;

/**
 * Class WhereIn
 */
class WhereIn extends BaseHeuristic
{
    /**
     * @param Builder $builder
     * @return Builder
     */
    public function before(Builder $builder): Builder
    {
        return $this->match($builder, Where::class, function (Where $where) use ($builder) {
            if ($where->getOperator() === Where::OPERATOR_IN) {
                $this->optimiseSingleValueWhereIn($builder, $where);
            } elseif ($where->getOperator() === Where::OPERATOR_NOT_IN) {
                $this->optimiseSingleValueWhereNotIn($builder, $where);
            }
        });
    }

    /**
     * Replaces "WHERE IN" with "EQUAL" if the sample contains a length
     * that does not exceed only 1 (ONE) value.
     *
     * @param Builder $builder
     * @param Where $where
     * @return void
     */
    private function optimiseSingleValueWhereIn(Builder $builder, Where $where): void
    {
        $value = $where->getValue();

        if (\count($value) === 1) {
            $singleSelection = new Where(
                $where->getField(),
                \reset($value)
            );

            $builder->replaceCriterion($where, $singleSelection);
        }
    }

    /**
     * Replaces "WHERE NOT IN" with "EQUAL" if the sample contains a length
     * that does not exceed only 1 (ONE) value.
     *
     * @param Builder $builder
     * @param Where $where
     * @return void
     */
    private function optimiseSingleValueWhereNotIn(Builder $builder, Where $where): void
    {
        $value = $where->getValue();

        if (\count($value) === 1) {
            $singleSelection = new Where(
                $where->getField(),
                Where::OPERATOR_NEQ,
                \reset($value)
            );

            $builder->replaceCriterion($where, $singleSelection);
        }
    }
}
