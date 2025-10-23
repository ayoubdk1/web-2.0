<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function countRomanceBooks(): int
{
    return $this->createQueryBuilder('b')
        ->select('COUNT(b.ref)')
        ->where('b.category = :category')
        ->setParameter('category', 'Romance')
        ->getQuery()
        ->getSingleScalarResult();
}
public function findBooksByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
{
    return $this->createQueryBuilder('b')
        ->where('b.publicationDate BETWEEN :start AND :end')
        ->andWhere('b.published = :published')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->setParameter('published', true)
        ->orderBy('b.publicationDate', 'ASC')
        ->getQuery()
        ->getResult();
}


//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
