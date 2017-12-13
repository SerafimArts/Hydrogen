<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Criteria;

/**
 * Class Offset
 */
class Offset extends BaseCriterion
{
    /**
     * @var int
     */
    private $offset;

    /**
     * Offset constructor.
     * @param int $offset
     */
    public function __construct(int $offset)
    {
        $this->offset = $offset;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
