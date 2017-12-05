<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query;

use Illuminate\Support\Str;
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
     * @var array
     */
    private $scopes = [];

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
     * @param object|string $context
     * @return Proxy
     */
    public function scope($context): self
    {
        $this->scopes[] = $context;

        if (\is_object($context)) {
            $this->scopes[] = \get_class($context);
        }

        return $this;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return $this|Builder|Proxy
     */
    public function __call(string $method, array $arguments = []): Proxy
    {
        $scope = $this->getScopeMethod($method);

        if ($scope !== null) {
            $scope($this, ...$arguments);
        }

        return $this;
    }

    /**
     * @param string $method
     * @return |null
     */
    private function getScopeMethod(string $method): ?\Closure
    {
        $action = 'scope' . Str::studly($method);

        foreach ($this->scopes as $context) {
            if (\method_exists($context, $action)) {
                return \Closure::fromCallable([$context, $action]);
            }
        }
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
