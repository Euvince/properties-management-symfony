<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Property>
 *
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly PaginatorInterface $paginator
    )
    {
        parent::__construct($registry, Property::class);
    }

    /**
    * return Property[]
     */
    function paginateVisibleProperties (int $page, int $limit, ?int $userId = null, ?PropertySearch $propertySearch = null) : array | PaginationInterface {

        $builder = $this->findVisibleQuery();

        if ($minPrice = $propertySearch->getMaxPrice()) {
            $builder = $builder
                ->andWhere('p.price <= :maxPrice')
                ->setParameter('maxPrice', $minPrice)
            ;
        }

        if ($maxSurface = $propertySearch->getMinSurface()) {
            $builder = $builder
                ->andWhere('p.surface >= :minSurface')
                ->setParameter('minSurface', $maxSurface)
            ;
        }

        $specificities = $propertySearch->getSpecificities();
        if ($specificities->count() > 0) {
            $k = 0;
            foreach ($specificities as $k => $specificity) {
                $k++;
                $builder = $builder
                    ->andWhere(":specificity$k MEMBER OF p.specificities")
                    ->setParameter(":specificity$k", $specificity)
                ;
            }
        }

        if ($userId)
        $builder = $builder->andWhere('p.user = :userId')->setParameter('userId', $userId);

        return $this->paginator->paginate(
            $builder->getQuery()->getResult(),
            $page, $limit, /* [
                'distinct' => true,
                'sortAllowFiledList' => ['p.id', 'p.title']
            ] */
        );
    }

    /**
    * return Property[]
     */
    function findLatestVisibleProperties () : array {
        return $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult()
        ;
    }

    private function findVisibleQuery () : QueryBuilder {
        return
            $this->createQueryBuilder('p')->where('p.sold = 0')
        ;
    }

//    /**
//     * @return Property[] Returns an array of Property objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Property
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
