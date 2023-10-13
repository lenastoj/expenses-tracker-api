<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 *
 * @method Expense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expense[]    findAll()
 * @method Expense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function save(Expense $expense): void
    {
        $this->getEntityManager()->persist($expense);
        $this->getEntityManager()->flush();
    }

//    public function getWeekExpensesQueryBuilder(int $userId, string $startDate, string $endDate)
//    {
//        $qb = $this->createQueryBuilder('e')
//            ->select('e.id', 'e.date', 'e.time', 'e.amount', 'e.description', 'e.comment')
//            ->where('e.user = :userId')
//            ->andWhere('e.date BETWEEN :startDate AND :endDate')
//            ->setParameter('userId', $userId)
//            ->setParameter('startDate', $startDate)
//            ->setParameter('endDate', $endDate)
//            ->orderBy('e.date', 'asc')
//            ->addOrderBy('e.time', 'asc');
//
//        return $qb->getQuery()->getResult();
//    }

    public function getExpenses(
        int $userId,
        bool $result,
        string | null $startDate = null,
        string | null $endDate = null,
        string | null $searchQuery = null,
        string | null $sort = null,
        string | null $sortDirection = null,
    ) {
        $qb = $this->createQueryBuilder('e')
            ->select('e.id', 'e.date', 'e.time', 'e.amount', 'e.description', 'e.comment')
            ->where('e.user = :userId')
            ->setParameter('userId', $userId);

        if ($startDate && $endDate) {
            $qb->andWhere('e.date BETWEEN :startDate AND :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }
        if ($searchQuery) {
            $qb->andWhere('e.description LIKE :searchQuery OR e.comment LIKE :searchQuery')->setParameter(
                'searchQuery',
                '%' . $searchQuery . '%'
            );
        }
        if ($sort && $sortDirection) {
            $qb->orderBy("e.$sort", $sortDirection);
        }
        if ($result) {
            return $qb->getQuery()->getResult();
        }
        return $qb;
    }

    public function delete(Expense $expense): void
    {
        $this->getEntityManager()->remove($expense);
        $this->getEntityManager()->flush();
    }
}
