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
 * Class OrderBy
 */
class OrderBy extends BaseCriterion
{
    public const ORDER_ASC = 'asc';
    public const ORDER_DESC = 'desc';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $order;

    /**
     * OrderBy constructor.
     * @param string $field
     * @param string|null $order
     */
    public function __construct(string $field, string $order = null)
    {
        $this->field = $field;
        $this->order = Str::lower($order ?? self::ORDER_ASC);
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
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function isAsc(): bool
    {
        return $this->order === self::ORDER_ASC;
    }

    /**
     * @return bool
     */
    public function isDesc(): bool
    {
        return $this->order === self::ORDER_DESC;
    }
}
