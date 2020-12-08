<?php

namespace App\Tests\Service;

use App\Entity\Sensor;
use App\Service\Sensors;
use App\Tests\TestEnv\EntityManagerTestCase;

class SensorsTest extends EntityManagerTestCase
{
    public function testMeasurementStatus()
    {
        $entityManager = $this->entityManager;
        $sensorRepository = $entityManager->getRepository(Sensor::class);
        $sensor = new Sensors($sensorRepository);

        $request = ['co2' => '2000', 'time' => '2019-02-01T18:10:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('ALERT', $sensor->getStatus(), 'Sensor status set to be OK');

        $request = ['co2' => '2001', 'time' => '2019-02-01T18:11:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('ALERT', $sensor->getStatus(), 'Sensor status set to be WARN');

        $request = ['co2' => '2002', 'time' => '2019-02-01T18:12:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('ALERT', $sensor->getStatus(), 'Sensor status set to be WARN');

        $request = ['co2' => '2003', 'time' => '2019-02-01T18:13:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('ALERT', $sensor->getStatus(), 'Sensor status set to be ALERT');

        $request = ['co2' => '2004', 'time' => '2019-02-01T18:14:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('ALERT', $sensor->getStatus(), 'Sensor status set to be ALERT');

        $request = ['co2' => '1999', 'time' => '2019-02-01T18:15:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('ALERT', $sensor->getStatus(), 'Sensor status set to be ALERT');

        $request = ['co2' => '1988', 'time' => '2019-02-01T18:16:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('ALERT', $sensor->getStatus(), 'Sensor status set to be ALERT');

        $request = ['co2' => '1977', 'time' => '2019-02-01T18:17:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('OK', $sensor->getStatus(), 'Sensor status set to be OK');

        $request = ['co2' => '1966', 'time' => '2019-02-01T18:18:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('OK', $sensor->getStatus(), 'Sensor status set to be OK');

        $request = ['co2' => '2100', 'time' => '2019-02-01T18:19:47+00:00'];
        $sensor->collectMeasurements($request);
        $this->assertEquals('WARN', $sensor->getStatus(), 'Sensor status set to be WARN');
    }

    public function testMetrics()
    {
        $entityManager = $this->entityManager;
        $sensorRepository = $entityManager->getRepository(Sensor::class);
        $sensor = new Sensors($sensorRepository);
        $response = $sensor->getMetrics();
        if (!empty($response)) {
            $this->assertArrayHasKey('maxLast30Days', $response, 'Has one month max sensor value');
            $this->assertArrayHasKey('avgLast30Days', $response, 'Contains a month average sensor value');
        } else {
            $this->assertEmpty($response);
        }
    }

    public function testAlerts()
    {
        $entityManager = $this->entityManager;
        $sensorRepository = $entityManager->getRepository(Sensor::class);
        $sensor = new Sensors($sensorRepository);
        $response = $sensor->getAlerts();
        if (!empty($response)) {
            $this->assertArrayHasKey('startTime', $response);
        } else {
            $this->assertEmpty($response);
        }
    }
}

