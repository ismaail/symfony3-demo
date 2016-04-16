<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Traits\ServicesMocker;

/**
 * Class DefaultControllerTest
 * @package Tests\AppBundle\Controller
 *
 * @mixin \PHPUnit_Framework_TestCase
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @codingStandardsIgnoreFile
 */
class DefaultControllerTest extends WebTestCase
{
    use ServicesMocker;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    /**
     * @test
     */
    public function it_says_welcome_for_home_page()
    {
        $this->mockLanguageListenerService();

        $crawler = $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Welcome!', $crawler->filter('h1')->text());
    }
}
