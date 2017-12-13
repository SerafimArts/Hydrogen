<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Processors\Collection;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Criteria\Criterion;
use Serafim\Hydrogen\Criteria\Where;

/**
 * Class WhereProcessor
 */
class WhereProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|Where $criterion
     * @param Collection $collection
     * @return Collection
     * @throws \InvalidArgumentException
     */
    public function process(Criterion $criterion, $collection): Collection
    {
        $column = $this->meta->getColumnName($criterion->getField());

        switch ($criterion->getOperator()) {
            case Where::OPERATOR_EQ:
                return $collection->where($column, $criterion->getValue());

            case Where::OPERATOR_NEQ:
                return $collection->where($column, '!=', $criterion->getValue());

            case Where::OPERATOR_GT:
                return $collection->where($column, '>', $criterion->getValue());

            case Where::OPERATOR_LT:
                return $collection->where($column, '<', $criterion->getValue());

            case Where::OPERATOR_GTE:
                return $collection->where($column, '>=', $criterion->getValue());

            case Where::OPERATOR_LTE:
                return $collection->where($column, '<=', $criterion->getValue());

            case Where::OPERATOR_IN:
                return $collection->whereIn($column, $criterion->getValue());

            case Where::OPERATOR_NOT_IN:
                return $collection->whereNotIn($column, $criterion->getValue());

            case Where::OPERATOR_LIKE:
                return $collection->filter($this->like($criterion, $column));

            case Where::OPERATOR_NOT_LIKE:
                return $collection->filter(function(array $data) use ($criterion, $column): bool {
                    return ! $this->like($criterion, $column)($data);
                });

            case Where::OPERATOR_BTW:
                [$from, $to] = $criterion->getValue();

                return $collection
                    ->where($column, '>=', $from)
                    ->where($column, '<=', $to);

            case Where::OPERATOR_NOT_BTW:
                return $collection->filter(function(array $data) use ($criterion, $column): bool {
                    $haystack = Arr::get($data, $column);
                    [$from, $to] = $criterion->getValue();

                    return $haystack < $from || $haystack > $to;
                });
        }

        $error = \sprintf('Unexpected "%s" operator type', $criterion->getOperator());
        throw new \InvalidArgumentException($error);
    }

    /**
     * @param Where $where
     * @param string $field
     * @return \Closure
     */
    private function like(Where $where, string $field): \Closure
    {
        return function (array $data) use ($field, $where): bool {
            $haystack = (string)Arr::get($data, $field);
            $needle   = (string)$where->getValue();

            // Starts required
            if ($haystack{0} !== '%' && ! Str::startsWith($haystack, $needle)) {
                return false;
            }

            // Ends required
            if (! Str::endsWith($needle, '%') && ! Str::endsWith($haystack, $needle)) {
                return false;
            }

            // In any position
            if ($haystack{0} === '%' && Str::endsWith($needle, '%')) {
                return Str::contains(Str::substr($haystack, 1, -1), $needle);
            }

            return $needle === Str::substr($haystack, 1, -1);
        };
    }
}
