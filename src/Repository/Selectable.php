<?php
/**
 * This file is part of railt.org package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Repository;

use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Query\Proxy;

/**
 * @property-read Proxy|Builder|$this|Repository $query
 * @property-read Proxy|Builder|$this|Repository $clearQuery
 */
interface Selectable
{
    /**
     * @return Proxy|Builder|$this|Repository
     */
    public function query(): Builder;

    /**
     * @return Proxy|Builder|$this|Repository
     */
    public function clearQuery(): Builder;
}
