<?php

namespace Iziscar\Component\Doctrine;

use Doctrine\ORM\EntityManager;

/**
 * Class Manager
 */
class Manager
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var string
     */
    private $repositoryName;

    /**
     * @var string
     */
    private $entityName;

    /**
     * __construct
     *
     * @param EntityManager $entityManager  Doctrine entity manager
     * @param string        $repositoryName Repository name
     */
    public function __construct(EntityManager $entityManager, $repositoryName)
    {
        $this->entityManager  = $entityManager;
        $this->repositoryName = $repositoryName;
        $this->entityName     = '';
    }

    /**
     * createEntity
     *
     * @return mixed
     * @throws \Exception
     */
    public function createEntity()
    {
        $className  = $this->getRepository()->getClassname();
        $reflection = new \ReflectionClass($className);

        if (!$reflection->isInstantiable()) {
            throw new \Exception(sprintf('Entity "%s" is not instantiable', $className));
        }

        return $reflection->newInstance();
    }

    /**
     * getEntityName
     *
     * @return string
     * @throws \Exception
     */
    public function getEntityName()
    {
        if ($this->entityName == '') {
            $info = explode(':', $this->repositoryName);

            if (count($info) != 2) {
                throw new \Exception(
                    sprintf(
                        'Malformed repository name : "%s". Attempt BundleName:EntityName',
                        $this->repositoryName
                    )
                );
            }

            $this->entityName = $info[1];
        }

        return $this->entityName;
    }

    /**
     * getRepository
     *
     * @return \Doctrine\ORM\EntityRepository
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->getRepositoryName());
    }

    /**
     * getRepositoryName
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return $this->repositoryName;
    }

    /**
     * save
     *
     * @param mixed   $entity Entity
     * @param boolean $flush  Flush entity
     *
     * @return boolean
     */
    public function save($entity, $flush = true)
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * remove
     *
     * @param mixed $entity
     *
     * @return boolean
     */
    public function remove($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return true;
    }

    /**
     * find
     *
     * @param integer $id Id entity
     *
     * @return mixed
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Finds all entities in the repository.
     *
     * @return array The entities.
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Finds entities by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array The objects.
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Finds a single entity by a set of criteria.
     *
     * @param array      $criteria
     * @param array|null $orderBy
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

}
