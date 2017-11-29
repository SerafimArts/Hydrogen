<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Collection;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Serafim\Hydrogen\Collection;

/**
 * Class ArrayHydrator
 */
class ArrayHydrator
{
    /**
     * @var ClassMetadata
     */
    private $meta;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Hydrator constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->meta = $meta;
        $this->em = $em;
    }

    /**
     * @param array $data
     * @return object
     * @throws \LogicException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public function hydrate(array $data)
    {
        $instance = $this->meta->newInstance();

        $this->loadFieldValues($instance, $data);
        $this->loadRelations($instance, $data);

        return $instance;
    }

    /**
     * @param object $entity
     * @param array $data
     * @return void
     */
    private function loadFieldValues($entity, array $data): void
    {
        foreach ($this->meta->getFieldNames() as $field) {
            $column = $this->meta->getColumnName($field);

            if (\array_key_exists($column, $data)) {
                $this->meta->setFieldValue($entity, $field, $data[$column]);
            }
        }
    }

    /**
     * @param object $entity
     * @param array $data
     * @return void
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \LogicException
     */
    private function loadRelations($entity, array $data): void
    {
        foreach ($this->meta->getAssociationMappings() as $property => $mappings) {
            if ($this->meta->isInheritedAssociation($property)) {
                $this->loadEmbeddable($entity, $mappings, $data);
                continue;
            }

            $relation = $this->getJoinColumnOf($property, $mappings);

            if ($this->isToOne($mappings)) {
                $this->loadSingleRelation($entity, $mappings, $data[$relation] ?? null);
            } elseif ($this->isToMany($mappings)) {
                $this->loadCollectionRelation($entity, $mappings, $data[$relation] ?? null);
            }
        }
    }

    /**
     * @param $entity
     * @param array $mappings
     * @param array $values
     * @return void
     * @throws \LogicException
     */
    private function loadEmbeddable($entity, array $mappings, array $values): void
    {
        throw new \LogicException('Embeddable did not support yet');
    }

    /**
     * @param string $property
     * @param array $mappings
     * @return string
     * @throws \LogicException
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    private function getJoinColumnOf(string $property, array $mappings): string
    {
        // Direct relation
        if (\array_key_exists('joinColumns', $mappings)) {
            return \array_first($mappings['joinColumns'])['name'];
        }

        // Inverse relation
        $target = $this->em->getClassMetadata($mappings['targetEntity']);
        $association = $target->getAssociationMapping($mappings['mappedBy']);

        $column = \array_first($association['joinColumns'])['referencedColumnName'];

        if ($column === null) {
            $error = \sprintf('Relation %s must contain join column definition.', $property);
            throw new \LogicException($error);
        }

        return $column;
    }

    /**
     * @param array $mapping
     * @return bool
     */
    private function isToOne(array $mapping): bool
    {
        // TODO: Change to bit mask usage
        return \in_array($mapping['type'] ?? null, [
            ClassMetadataInfo::ONE_TO_ONE,
            ClassMetadataInfo::MANY_TO_ONE,
        ], true);
    }

    /**
     * @param object $entity
     * @param array $mappings
     * @param string|int $value
     * @return void
     */
    private function loadSingleRelation($entity, array $mappings, $value): void
    {
        if ($value === null) {
            // Empty relation
            return;
        }

        $relation = $this->em->find($mappings['targetEntity'], $value);

        $this->meta->setFieldValue($entity, $mappings['fieldName'], $relation);
    }

    /**
     * @param array $mapping
     * @return bool
     */
    private function isToMany(array $mapping): bool
    {
        // TODO: Change to bit mask usage
        return \in_array($mapping['type'] ?? null, [
            ClassMetadataInfo::ONE_TO_MANY,
            ClassMetadataInfo::MANY_TO_MANY,
        ], true);
    }

    /**
     * @param object $entity
     * @param array $mappings
     * @param string|int $value
     * @return void
     * @throws \LogicException
     */
    private function loadCollectionRelation($entity, array $mappings, $value): void
    {
        if ($value === null) {
            // Empty collection
            $this->meta->setFieldValue($entity, $mappings['fieldName'], $this->getCollection([]));

            return;
        }

        throw new \LogicException(__METHOD__ . ' not implemented yet');
    }

    /**
     * @param array $items
     * @return iterable
     */
    public function getCollection(array $items): iterable
    {
        return new Collection($items);
    }
}
