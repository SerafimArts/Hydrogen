<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Criteria;

use Illuminate\Support\Str;

/**
 * Class Where
 */
class Where extends BaseCriterion
{
    public const OPERATOR_EQ = '=';
    public const OPERATOR_NEQ = '<>';
    public const OPERATOR_GT = '>';
    public const OPERATOR_GTE = '>=';
    public const OPERATOR_LT = '<';
    public const OPERATOR_LTE = '<=';
    // X IN (...)
    public const OPERATOR_IN = 'in';
    public const OPERATOR_NOT_IN = 'not in';
    // LIKE
    public const OPERATOR_LIKE = 'like';
    public const OPERATOR_NOT_LIKE = 'not like';
    // BETWEEN
    public const OPERATOR_BTW = 'between';
    public const OPERATOR_NOT_BTW = 'not between';

    /**
     * Mappings
     */
    private const OPERATOR_MAPPINGS = [
        '=='         => self::OPERATOR_EQ,
        '==='        => self::OPERATOR_EQ,
        ':='         => self::OPERATOR_EQ,
        '!='         => self::OPERATOR_NEQ,
        '!=='        => self::OPERATOR_NEQ,
        'notin'      => self::OPERATOR_NOT_IN,
        'notlike'    => self::OPERATOR_NOT_LIKE,
        '..'         => self::OPERATOR_BTW,
        '...'        => self::OPERATOR_BTW,
        'notbetween' => self::OPERATOR_NOT_BTW,
    ];

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator = '=';

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $and;

    /**
     * Where constructor.
     * @param string $field
     * @param $operatorOrValue
     * @param null $value
     * @param bool $and
     */
    public function __construct(string $field, $operatorOrValue, $value = null, bool $and = true)
    {
        [$this->field, $this->operator, $this->value, $this->and] = [$field, $operatorOrValue, $value, $and];

        $this->normalizeParameters();
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isAnd(): bool
    {
        return $this->and;
    }

    /**
     * @return bool
     */
    public function isOr(): bool
    {
        return ! $this->isAnd();
    }

    /**
     * @param string $field
     * @param $operatorOrValue
     * @param null $value
     * @return array
     */
    public static function normalize(string $field, $operatorOrValue, $value = null): array
    {
        if ($value === null) {
            [$value, $operatorOrValue] = [$operatorOrValue, '='];
        }

        $operator = Str::lower($operatorOrValue);
        $operator = self::OPERATOR_MAPPINGS[$operator] ?? $operator;

        return [$field, $operator, $value];
    }

    /**
     * @return void
     */
    private function normalizeParameters(): void
    {
        /**
         * Transforms "equal" to "where in" if selection is array.
         */
        if (\is_iterable($this->value)) {
            switch ($this->operator) {
                case self::OPERATOR_EQ:
                    $this->operator = self::OPERATOR_IN;
                    break;
                case self::OPERATOR_NEQ:
                    $this->operator = self::OPERATOR_NOT_IN;
                    break;
            }
        }
    }
}
