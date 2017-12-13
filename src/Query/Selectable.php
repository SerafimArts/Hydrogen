<?php
/**
 * This file is part of railt.org package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query;

use Serafim\Hydrogen\Collection;

/**
 * Interface Selectable
 */
interface Selectable
{
    /**
     * @return Collection
     */
    public function get(): Collection;

    /**
     * @return null|object
     */
    public function first();

    /**
     * @return int
     */
    public function count(): int;
}
