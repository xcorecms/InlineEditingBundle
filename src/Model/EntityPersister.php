<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class EntityPersister
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var PropertyAccessor */
    private $propertyAccessor;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
        $this->propertyAccessor = new PropertyAccessor;
    }

    /**
     * @param string $className
     * @param string|int $id
     * @param string $property
     * @param string $value
     */
    public function update(string $className, $id, string $property, string $value): void
    {
        $entity = $this->objectManager->find($className, $id);
        $this->propertyAccessor->setValue($entity, $property, $value);
    }

    /**
     *
     */
    public function flush(): void
    {
        $this->objectManager->flush();
    }
}
