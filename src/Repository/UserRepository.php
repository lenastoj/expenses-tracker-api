<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function getGuests(
        $userId,
        $searchQuery,
        $sort,
        $sortDirection,
        $result = false,
    ) {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.myGuests', 'g')
            ->select('g.id', 'g.firstName', 'g.lastName', 'g.email')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId);

        if ($searchQuery) {
            $qb->andWhere(
                'g.firstName LIKE :searchQuery OR g.lastName LIKE :searchQuery OR g.email LIKE :searchQuery'
            )->setParameter(
                'searchQuery',
                '%' . $searchQuery . '%'
            );
        }
        if ($sort && $sortDirection) {
            $qb->orderBy("g.$sort", $sortDirection);
        }
        if ($result) {
            return $qb->getQuery()->getResult();
        }
        return $qb;
    }
}
