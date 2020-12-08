<?php

namespace App\Repository;

use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use App\Entity\Sensor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SensorRepository extends ServiceEntityRepository
{
    /**
     * SensorRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sensor::class);
    }

    /**
     * @param array $data
     * @return int
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(array $data): int
    {
        $em = $this->getEntityManager();

        $sensor = new Sensor();
        $sensor->setLevel($data['co2']);
        $sensor->setStatus($data['status']);
        $sensor->setTime(new DateTime($data['time']));
        $em->persist($sensor);
        $em->flush();

        return $sensor->getId();
    }

    /**
     * @return int
     */
    public function isHighLevel(): int
    {
        $rows = $this->createQueryBuilder('s')
            ->select('s.level, s.status')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();

        $response = 0;
        foreach ($rows as $row) {
            if ($row['level'] > 2000 || $row['status'] === 'ALERT') {
                $response++;
            }
        }

        return $response;
    }

    /**
     * @return int
     */
    public function isLowLevel(): int
    {
        $rows = $this->createQueryBuilder('s')
            ->select('s.level, s.status')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();

        $response = 0;
        foreach ($rows as $row) {
            if ($row['level'] < 2000 || $row['status'] === 'WARN') {
                $response++;
            }
        }

        return $response;
    }

    /**
     * @return array|null
     * @throws NonUniqueResultException
     */
    public function getLast(): ?array
    {
        return $this->createQueryBuilder('s')
            ->select('s.status')
            ->orderBy('s.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array|null
     */
    public function getMonthData(): ?array
    {
        $date = date('Y-m-d h:i:s', strtotime("-30 days"));

        return $this->createQueryBuilder('s')
            ->select('s.level')
            ->where('s.time BETWEEN :n30days AND :today')
            ->setParameter('today', date('Y-m-d h:i:s'))
            ->setParameter('n30days', $date)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array|null
     */
    public function getAll(): ?array
    {
        return $this->createQueryBuilder('s')
            ->select('s.level, s.time')
            ->getQuery()
            ->getResult();
    }
}

