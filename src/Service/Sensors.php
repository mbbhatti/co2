<?php

namespace App\Service;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\NonUniqueResultException;
use App\Repository\SensorRepository;
use Doctrine\ORM\EntityManagerInterface;

class Sensors
{
    /**
     * @var SensorRepository
     */
    protected $sensorRepository;

    /**
     * Sensors constructor.
     * @param SensorRepository $sensorRepository
     */
    public function __construct(SensorRepository $sensorRepository)
    {
        $this->sensorRepository = $sensorRepository;
    }

    /**
     * @param array $request
     * @return bool
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function collectMeasurements(array $request): bool
    {
        if ($request['co2'] > 2000) {
            $consecutive = $this->sensorRepository->isHighLevel();
            if ($consecutive > 1) {
                $status = 'ALERT';
            } else {
                $status = 'WARN';
            }
        } elseif ($request['co2'] < 2000) {
            $consecutive = $this->sensorRepository->isLowLevel();
            $lastEntry = $this->sensorRepository->getLast();
            if ($lastEntry !== null && $lastEntry['status'] !== 'ALERT') {
                $status = 'OK';
            } else {
                if ($consecutive > 1) {
                    $status = 'OK';
                } else {
                    $status = 'ALERT';
                }
            }
        } else {
            $lastEntry = $this->sensorRepository->getLast();
            if ($lastEntry !== null && $lastEntry['status'] === 'ALERT') {
                $status = 'ALERT';
            } else {
                $status = 'OK';
            }
        }

        $request['status'] = $status;
        $this->sensorRepository->add($request);

        return true;
    }

    /**
     * @return string
     * @throws NonUniqueResultException
     */
    public function getStatus(): string
    {
        $status = $this->sensorRepository->getLast();

        return $status['status'] ?? 'OK';
    }

    /**
     * @return array
     */
    public function getMetrics(): array
    {
        $rows = $this->sensorRepository->getMonthData();
        if (empty($rows)) {
            return $rows;
        }

        $metrics = [];
        $metricsSum = 0;
        foreach ($rows as $row) {
            $metrics[] = $row;
            $metricsSum += $row['level'];
        }

        return [
            'maxLast30Days' => max(array_column($metrics, 'level')),
            'avgLast30Days' => round($metricsSum / count($metrics))
        ];
    }

    /**
     * @return array
     */
    public function getAlerts(): array
    {
        $rows = $this->sensorRepository->getAll();
        if (empty($rows)) {
            return $rows;
        }

        $startTime = min(array_column($rows, 'time'));
        $endTime = max(array_column($rows, 'time'));
        $response = [
            'startTime' => date('c', strtotime($startTime->format('Y-m-d H:i:s'))),
            'endTime' => date('c', strtotime($endTime->format('Y-m-d H:i:s')))
        ];

        foreach ($rows as $key => $row) {
            $response['measurement' . ++$key] = $row['level'];
        }

        return $response;
    }
}

