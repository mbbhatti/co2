<?php
namespace App\Tests\TestEnv;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestUtils extends KernelTestCase
{
    /**
     * @return array
     */
    public static function getHeader(): array
    {
        return ['headers' => ['Content-Type' => 'application/json']];
    }

    /**
     * @return string
     */
    public static function postData(): string
    {
        $answer = ['co2' => '2000', 'time' => '2019-02-01T18:55:47+00:00'];

        return json_encode($answer);
    }
}

