<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Traits\ServicesMocker;

/**
 * Class DefaultControllerTest
 * @package Tests\AppBundle\Controller
 *
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
        $requestMock = $this->mockLanguageListenerService(['onKernelRequest']);

        $requestMock
            ->expects($this->atLeastOnce())
            ->method('onKernelRequest')
            ->willReturn([])
        ;

        $crawler = $this->client->request('GET', '/en/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('welcome', $crawler->filter('h1')->text());
    }

    /**
     * @test
     */
    public function it_redirect_base_route_to_home_page_with_default_language()
    {
        $requestMock = $this->mockLanguageListenerService();

        $requestMock->setEntityManager($this->mockDefaultLanguage('ru', 'Russian'));

        $this->client->request('GET', '/');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();

        $this->assertEquals('http://localhost/ru/', $this->client->getHistory()->current()->getUri());
    }
}
