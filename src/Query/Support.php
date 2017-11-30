<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Query;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Support
 */
class Support
{
    /**
     * @param EntityManagerInterface $em
     * @param array $mappings
     * @return array
     * @throws \Doctrine\ORM\Mapping\MappingException
     */
    public static function getJoinColumns(EntityManagerInterface $em, array $mappings): array
    {
        if ($mappings['isOwningSide'] ?? false) {
            return \iterator_to_array(self::getOwnerJoinColumn($mappings));
        }

        $meta = $em->getClassMetadata($mappings['targetEntity']);
        $mappedBy = $meta->getAssociationMapping($mappings['mappedBy']);

        return \iterator_to_array(self::getReversedJoinColumn($mappedBy));
    }

    /**
     * @param array $mapping
     * @return \Traversable
     */
    private static function getOwnerJoinColumn(array $mapping): \Traversable
    {
        foreach ((array)($mapping['joinColumns'] ?? []) as $column) {
            yield $column['name'] => $column['referencedColumnName'];
        }
    }

    /**
     * @param array $mapping
     * @return \Traversable
     */
    private static function getReversedJoinColumn(array $mapping): \Traversable
    {
        foreach ((array)($mapping['joinColumns'] ?? []) as $column) {
            yield $column['referencedColumnName'] => $column['name'];
        }
    }
}
