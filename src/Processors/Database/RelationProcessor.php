<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Processors\Database;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Serafim\Hydrogen\Query\Builder;
use Serafim\Hydrogen\Criteria\Criterion;
use Serafim\Hydrogen\Criteria\Relation;

/**
 * Class RelationProcessor
 */
class RelationProcessor extends CriterionProcessor
{
    /**
     * @param Criterion|Relation $criterion
     * @param QueryBuilder $builder
     * @return QueryBuilder
     * @throws MappingException
     * @throws \InvalidArgumentException
     */
    public function process(Criterion $criterion, $builder): QueryBuilder
    {
        $relation = $this->with($criterion, $builder);

        return $this->withSubQuery($relation, $criterion, $builder);
    }

    /**
     * @param Relation $relation
     * @param QueryBuilder $builder
     * @return array
     * @throws MappingException
     * @throws \InvalidArgumentException
     */
    private function with(Relation $relation, QueryBuilder $builder): array
    {
        $mapping = $this->meta->getAssociationMapping($relation->getRelation());

        $alias = $this->addSelection($mapping['targetEntity'], $builder);
        $relationField = $this->fieldName($mapping['fieldName']);

        $builder->leftJoin($relationField, $alias);

        return [$this->em->getClassMetadata($mapping['targetEntity']), $alias];
    }

    /**
     * @param array $info
     * @param Relation $relation
     * @param QueryBuilder $query
     * @return QueryBuilder
     * @throws \InvalidArgumentException
     */
    public function withSubQuery(array $info, Relation $relation, QueryBuilder $query): QueryBuilder
    {
        $builder = new Builder();

        $callable = $relation->getContext();
        $callable($builder);

        return $this->getProcessor()->applyQueryBuilder($query, $builder, ...$info);
    }

    /**
     * @param string $entity
     * @param QueryBuilder $builder
     * @return string
     */
    private function addSelection(string $entity, QueryBuilder $builder): string
    {
        return \tap($this->createAlias('ref_', $entity), function(string $alias) use ($builder) {
            $builder->addSelect($alias);
        });
    }
}
