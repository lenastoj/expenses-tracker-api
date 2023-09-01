<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function getAll(int $userId): array
    {
        return $this->createQueryBuilder('e')
        ->where('e.user = :userId')
        ->setParameter('userId', $userId)
        ->getQuery()
        ->getResult();
    }

    public function getExpensesQueryBuilderForUser($userId): QueryBuilder
    {
        return $this->createQueryBuilder('e')
            ->where('e.user = :userId')
            ->setParameter('userId', $userId);
    }

    public function delete(Expense $expense): void
    {
        $this->getEntityManager()->remove($expense);
        $this->getEntityManager()->flush();
    }
}
