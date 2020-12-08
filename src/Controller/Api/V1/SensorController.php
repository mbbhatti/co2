<?php

namespace App\Controller\Api\V1;

use App\Service\Sensors;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SensorController extends AbstractController
{
    /**
     * @Route("/api/v1/sensors/{uuid}/measurements", name="SensorMeasurement", methods="POST")
     *
     * @param Request $request
     * @param Sensors $sensors
     * @return JsonResponse
     */
    public function measurements(Request $request, Sensors $sensors)
    {
        $params = json_decode($request->getContent(), true);
        $response = $sensors->collectMeasurements($params);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @Route("/api/v1/sensors/{uuid}", name="SensorStatus", methods="GET")
     *
     * @param Sensors $sensors
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function getStatus(Sensors $sensors)
    {
        return new JsonResponse(['status' => $sensors->getStatus()], Response::HTTP_OK);
    }

    /**
     * @Route("/api/v1/sensors/{uuid}/metrics", name="SensorMetrics", methods="GET")
     *
     * @param Sensors $sensors
     * @return JsonResponse
     */
    public function getMetrics(Sensors $sensors)
    {
        return new JsonResponse($sensors->getMetrics(), Response::HTTP_OK);
    }

    /**
     * @Route("/api/v1/sensors/{uuid}/alerts", name="SensorAlerts", methods="GET")
     *
     * @param Sensors $sensors
     * @return JsonResponse
     */
    public function getAlerts(Sensors $sensors)
    {
        return new JsonResponse($sensors->getAlerts(), Response::HTTP_OK);
    }
}

