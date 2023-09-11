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

    public function getExpensesQueryBuilderForUser(
        $userId,
        $word,
        $month,
        $amount,
        $date,
        $result = false
    ) {
        $qb = $this->createQueryBuilder('e')
            ->where('e.user = :userId')
            ->setParameter('userId', $userId);
        if ($word) {
            $qb->andWhere('e.description LIKE :word OR e.comment LIKE :word')->setParameter('word', '%' . $word . '%');
        }
        if (!empty($month)) {
//            $qb->andWhere('MONTH(e.date) = :month')->setParameter('month', $month);
            $qb->andWhere($qb->expr()->in('MONTH(e.date)', $month));
        }
        if ($amount) {
            $qb->orderBy('e.amount', $amount);
        }
        if ($date) {
            $qb->orderBy('e.date', $date);
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
