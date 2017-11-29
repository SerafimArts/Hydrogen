<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Criterion;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;

/**
 * Class Relation
 */
class Relation extends BaseCriterion
{
    /**
     * @var string
     */
    private $relation;

    /**
     * Relation constructor.
     * @param string $relation
     */
    public function __construct(string $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * @param string $alias
     * @param ClassMetadata $meta
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function getExpression(string $alias, ClassMetadata $meta, QueryBuilder $builder): QueryBuilder
    {
        $assoc = $meta->getAssociationMapping($this->relation);
        dd($assoc);

        return $builder;
    }
}
