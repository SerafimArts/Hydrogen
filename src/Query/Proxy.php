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
class Proxy
{
    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * @var Builder
     */
    private $builder;

    /**
     * Proxy constructor.
     * @param ObjectRepository $repository
     */
    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
        $this->builder = new Builder();
    }

    /**
     * @return Builder
     */
    public function toBuilder(): Builder
    {
        return $this->builder;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return $this|Builder|Proxy
     */
    public function __call(string $method, array $arguments = []): Proxy
    {
        $this->builder = $this->builder->$method(...$arguments);

        return $this;
    }

    /**
     * @param string $property
     * @return $this|Builder|Proxy
     */
    public function __get(string $property): Proxy
    {
        $this->builder = $this->builder->$property;

        return $this;
    }

    /**
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->repository->findBy($this->builder);
    }

    /**
     * @return null|object
     */
    public function first()
    {
        return $this->repository->findOneBy($this->builder);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->repository->count($this->builder);
    }
}
