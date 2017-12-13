<?php
/**
 * This file is part of railt.org package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Criteria;

/**
 * Class Select
 */
class Select extends BaseCriterion
{
    /**
     * @var string
     */
    private $field;

    /**
     * Select constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getSelection(): string
    {
        return $this->field;
    }
}
