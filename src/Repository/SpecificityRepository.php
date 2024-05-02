<?php

namespace App\Repository;

use App\Entity\Specificity;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Specificity>
 *
 * @method Specificity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Specificity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Specificity[]    findAll()
 * @method Specificity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecificityRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, Specificity::class);
    }

    /**
    * return Specificity[]
     */
    function paginateSpecificities (int $page, int $limit) : array | PaginationInterface {

        return $this->paginator->paginate(
            $this->createQueryBuilder('s'),
            $page, $limit
        );
    }

//    /**
//     * @return Specificity[] Returns an array of Specificity objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Specificity
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
