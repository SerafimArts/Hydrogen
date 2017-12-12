<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query;

use Serafim\Hydrogen\Collection;
use Serafim\Hydrogen\Repository\ObjectRepository;

/**
 * Class Proxy
 */
class Proxy extends Builder
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * Proxy constructor.
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->repository->findBy($this);
    }

    /**
     * @return null|object
     */
    public function first()
    {
        return $this->repository->findOneBy($this);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->repository->count($this);
    }
}
