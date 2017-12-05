<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Serafim\Hydrogen\Collection;

/**
 * Class ArrayHydrator
 */
class ArrayHydrator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ClassMetadata
     */
    private $meta;

    /**
     * ArrayHydrator constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $meta
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $meta)
    {
        $this->em = $em;
        $this->meta = $meta;
    }

    /**
     * @param array $data
     * @param string $prefix
     * @return object
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \LogicException
     */
    public function hydrate(array $data, string $prefix = '')
    {
        $instance = $this->meta->newInstance();

        return $this->hydrateObject($instance, $this->meta, $data, $prefix);
    }

    /**
     * @param object $object
     * @param ClassMetadata $meta
     * @param array $data
     * @param string $prefix
     * @return object
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \LogicException
     */
    private function hydrateObject($object, ClassMetadata $meta, array $data, string $prefix)
    {
        $this->loadFields($object, $meta, $data, $prefix);
        $this->loadRelations($object, $meta, $data, $prefix);
        $this->loadEmbeddable($object, $meta, $data, $prefix);

        return $object;
    }

    /**
     * @param object $entity
     * @param ClassMetadata $meta
     * @param array $data
     * @param string $prefix
     * @return void
     */
    private function loadFields($entity, ClassMetadata $meta, array $data, string $prefix): void
    {
        foreach ($meta->getFieldNames() as $field) {
            $column = $meta->getColumnName($field);

            if (\array_key_exists($prefix . $column, $data)) {
                $meta->setFieldValue($entity, $field, $data[$prefix . $column]);
            }
        }
    }

    /**
     * TODO Make sure that the relationships inside embeddable (with $prefix) works correctly.
     *
     * @param object $entity
     * @param ClassMetadata $meta
     * @param array $data
     * @param string $prefix
     * @return void
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \LogicException
     */
    private function loadRelations($entity, ClassMetadata $meta, array $data, string $prefix): void
    {
        foreach ($meta->getAssociationMappings() as $property => $mappings) {
            $relation = $prefix . $this->getJoinColumnOf($property, $mappings);

            if ($this->isToOne($mappings)) {
                $this->loadSingleRelation($entity, $mappings, $data[$relation] ?? null);
            } elseif ($this->isToMany($mappings)) {
                $this->loadCollectionRelation($entity, $mappings, $data[$relation] ?? null);
            }
        }
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
            $this->meta->setFieldValue($entity, $mappings['fieldName'], new ArrayCollection([]));
            return;
        }

        throw new \LogicException(__METHOD__ . ' not implemented yet');
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
     * @param object $entity
     * @param ClassMetadata $meta
     * @param array $data
     * @param string $prefix
     * @return void
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \LogicException
     */
    private function loadEmbeddable($entity, ClassMetadata $meta, array $data, string $prefix = ''): void
    {
        foreach ($meta->embeddedClasses as $field => $mappings) {
            $sub = $this->em->getClassMetadata($mappings['class']);

            $columnPrefix = $prefix . (string)$mappings['columnPrefix'];
            $instance = $this->hydrateObject($sub->newInstance(), $sub, $data, $columnPrefix);

            $meta->setFieldValue($entity, $field, $instance);
        }
    }
}
