<?php

namespace App\Repository;

use App\Entity\Livre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Livre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livre[]    findAll()
 * @method Livre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livre::class);
    }

    // /**
    //  * @return Livre[] Returns an array of Livre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */



    public function findByTitreField($value)
    {
        /*
        return $this->createQueryBuilder('l')
            ->Where('l.titre LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
        */

        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT l
            FROM App\Entity\Livre l
            WHERE l.titre LIKE :val
            ORDER BY l.date_de_parution ASC"
        )->setParameter('val', "%".$value."%");

        // returns an array of Product objects
        return $query->getResult();

    }

    public function findByNoteField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.note = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findByDateField($date1, $date2) //takes date
    {
        return $this->createQueryBuilder('l')
            ->where('l.date_de_parution >= :date1')
            ->andWhere('l.date_de_parution <= :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->orderBy('l.date_de_parution', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }



    /*
    public function findOneBySomeField($value): ?Livre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
