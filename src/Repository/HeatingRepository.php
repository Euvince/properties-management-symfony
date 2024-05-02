<?php

namespace App\Repository;

use App\Entity\Heating;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Heating>
 *
 * @method Heating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Heating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Heating[]    findAll()
 * @method Heating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeatingRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, Heating::class);
    }

    /**
    * return Heating[]
     */
    function paginateHeaters (int $page, int $limit) : array | PaginationInterface {

        return $this->paginator->paginate(
            $this->createQueryBuilder('h'),
            $page, $limit
        );
    }

//    /**
//     * @return Heating[] Returns an array of Heating objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Heating
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
