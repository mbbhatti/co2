<?php

namespace App\Tests\TestEnv;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class EntityManagerTestCase
 * @package App\Tests\TestEnv
 */
class EntityManagerTestCase extends KernelTestCase
{
    public $entityManager;
    public $registry;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->registry = $kernel->getContainer()->get('doctrine');
        $this->entityManager = $this->registry->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
        $this->registry = null;
    }
}

