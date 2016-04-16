<?php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * Class DoctrineTestCase
 * @package Bookkeeper\ApplicationBundle\Tests
 */
abstract class DoctrineTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var array
     */
    private $metadata;

    /**
     * {@inheritdoc}
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (! static::$kernel) {
            static::$kernel = self::createKernel([
                'environment' => 'test',
                'debug' => true,
            ]);

            static::$kernel->boot();
        }

        $this->container = static::$kernel->getContainer();
    }

    /**
     * Create Doctrine Schemas.
     */
    protected function setUp()
    {
        parent::setUp();

        // Entities metadata.
        $this->metadata = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();

        $this->generateSchema();

        // Clear EntityManager from previous tests data.
        $this->getEntityManager()->clear();
    }

    /**
     * Create Schema from Entities Metadata.
     *
     * @throws SchemaException
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function generateSchema()
    {
        if (! empty($this->metadata)) {
            $tool = new SchemaTool($this->getEntityManager());
            $tool->createSchema($this->metadata);
        } else {
            throw new SchemaException('No Metadata Classes to process.');
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Container
     */
    public function getContainer()
    {
        if (null !== $this->client) {
            return $this->client->getContainer();
        }

        return $this->container;
    }

    /**
     * Returns the doctrine orm entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Drop Doctrine Schemas.
     */
    protected function tearDown()
    {
        if (! empty($this->metadata)) {
            $tool = new SchemaTool($this->getEntityManager());
            $tool->dropSchema($this->metadata);
        } else {
            throw new SchemaException('No Metadata Classes to process.');
        }

        parent::tearDown();
    }
}
