<?php

namespace App\Test\Controller;

use App\Tests\TestEnv\TestUtils;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testMeasurements()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/v1/sensors/test123456789/measurements',
            TestUtils::getHeader(),
            [],
            [],
            TestUtils::postData()
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(true, $content);
    }

    public function testStatus()
    {
        $sensors = [
            [
                'request' => ['co2' => '2001', 'time' => '2019-02-01T18:55:47+00:00'],
                'status' => 'WARN'
            ], [
                'request' => ['co2' => '2100', 'time' => '2020-11-26T18:20:47+00:00'],
                'status' => 'WARN'
            ], [
                'request' => ['co2' => '2200', 'time' => '2020-08-09T18:21:47+00:00'],
                'status' => 'ALERT'
            ], [
                'request' => ['co2' => '2000', 'time' => '2020-02-21T18:22:47+00:00'],
                'status' => 'ALERT'
            ], [
                'request' => ['co2' => '2300', 'time' => '2020-10-15T18:23:47+00:00'],
                'status' => 'ALERT'
            ], [
                'request' => ['co2' => '1988', 'time' => '2020-11-10T18:24:47+00:00'],
                'status' => 'ALERT'
            ], [
                'request' => ['co2' => '1650', 'time' => '2020-12-03T18:25:47+00:00'],
                'status' => 'ALERT'
            ], [
                'request' => ['co2' => '2500', 'time' => '2020-11-11T18:26:47+00:00'],
                'status' => 'ALERT'
            ]
        ];

        $client = static::createClient();

        foreach ($sensors as $sensor) {
            $client->request(
                'POST',
                '/api/v1/sensors/test123456789/measurements',
                TestUtils::getHeader(),
                [],
                [],
                json_encode($sensor['request'])
            );

            $response = $client->getResponse();
            $content = json_decode($response->getContent(), true);
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals(true, $content);

            $client->request(
                'GET',
                '/api/v1/sensors/test123456789',
                TestUtils::getHeader()
            );

            $response = $client->getResponse();
            $content = json_decode($response->getContent(), true);
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals($sensor['status'], $content['status']);
        }
    }

    public function testMetrics()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/v1/sensors/test123456789/metrics',
            TestUtils::getHeader()
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        if (!empty($content)) {
            $this->assertArrayHasKey('maxLast30Days', $content, 'Has one month max sensor value');
            $this->assertArrayHasKey('avgLast30Days', $content, 'Contains a month average sensor value');
        }
    }

    public function testAlerts()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/v1/sensors/test123456789/alerts',
            TestUtils::getHeader()
        );

        $response = $client->getResponse();
        $content = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        if (!empty($content)) {
            $this->assertArrayHasKey('startTime', $content);
            $this->assertArrayHasKey('endTime', $content);
        }
    }
}

