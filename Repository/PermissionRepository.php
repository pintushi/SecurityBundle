<?php

namespace Pintushi\Bundle\SecurityBundle\Repository;

use Doctrine\ORM\Query\Expr;
use Pintushi\Bundle\CoreBundle\Doctrine\ORM\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use Pintushi\Bundle\SecurityBundle\Entity\Permission;

class PermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }

    /**
     * @param string $class
     * @param array $ids
     * @return Permission[]
     */
    public function findByEntityClassAndIds($class, array $ids = null)
    {
        if (empty($class) || (null !== $ids && empty($ids))) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder
            ->leftJoin('p.applyToEntities', 'ae', Expr\Join::WITH, 'ae.name = :class')
            ->leftJoin('p.excludeEntities', 'ee', Expr\Join::WITH, 'ee.name = :class')
            ->groupBy('p.id')
            ->having(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('p.applyToAll', 'true'),
                        $queryBuilder->expr()->eq($queryBuilder->expr()->count('ee'), 0)
                    ),
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->eq('p.applyToAll', 'false'),
                        $queryBuilder->expr()->gt($queryBuilder->expr()->count('ae'), 0)
                    )
                )
            )
            ->orderBy($queryBuilder->expr()->asc('p.id'))
            ->setParameter('class', $class);

        if ($ids) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('p.id', $ids));
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
