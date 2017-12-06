<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Processors\Database;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\QueryBuilder;
use Serafim\Hydrogen\Query\Criterion\Criterion;
use Serafim\Hydrogen\Query\Criterion\Relation;

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
        $mapping = $this->meta->getAssociationMapping($criterion->getRelation());

        $relationAlias = $this->addSelection($mapping['targetEntity'], $builder);
        $relationField = $this->fieldName($mapping['fieldName']);

        $builder = $builder->leftJoin($relationField, $relationAlias);

        return $builder;
    }

    /**
     * @param string $entity
     * @param QueryBuilder $builder
     * @return string
     * @throws \InvalidArgumentException
     */
    private function addSelection(string $entity, QueryBuilder $builder): string
    {
        $relationAlias = $this->createAlias('ref_', $entity);

        $builder->addSelect($relationAlias);

        return $relationAlias;
    }
}
