<?php

namespace App\Utils;

use Doctrine\ORM\QueryBuilder;

class Pagination
{
    public function paginate(QueryBuilder $queryBuilder, int $page, int $perPage)
    {
        $offset = ($page - 1) * $perPage;

        return $queryBuilder
            ->setMaxResults($perPage)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }
}
