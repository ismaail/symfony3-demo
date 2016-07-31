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
    public function it_says_welcome_in_english_for_home_page()
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
    public function it_says_welcome_in_french_for_home_page()
    {
        $requestMock = $this->mockLanguageListenerService(['onKernelRequest']);

        $requestMock
            ->expects($this->atLeastOnce())
            ->method('onKernelRequest')
            ->willReturn([])
        ;

        $crawler = $this->client->request('GET', '/fr/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('bienvenue', $crawler->filter('h1')->text());
    }

    /**
     * @test
     */
    public function it_sets_default_locale_in_session()
    {
        $requestMock = $this->mockLanguageListenerService();

        $requestMock->setEntityManager($this->mockDefaultLanguage('ru', 'Russian'));
        $requestMock->setTwig($this->mockTwigAddGlobal());

        // Invalid locale 'xx'
        $this->client->request('GET', '/xx/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Assert Session has the DefaultLocale value.
        $this->assertTrue($this->client->getRequest()->getSession()->has('locale'), 'Session has no locale value');
        $this->assertEquals('ru', $this->client->getRequest()->getSession()->get('locale'), 'Session has no locale value');
    }

    /**
     * @test
     */
    public function it_uses_session_locale_value()
    {
        $requestMock = $this->mockLanguageListenerService();

        $requestMock->setEntityManager($this->mockDefaultLanguage('ru', 'Russian'));
        $requestMock->setTwig($this->mockTwigAddGlobal());

        // Change the Session 'locale' value.
        $this->client->getContainer()->get('session')->set('locale', 'fr');

        // Invalid locale 'xx'
        $this->client->request('GET', '/xx/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Assert Session has the Session Locale value.
        $this->assertTrue($this->client->getRequest()->getSession()->has('locale'), 'Session has no locale value');
        $this->assertEquals('fr', $this->client->getRequest()->getSession()->get('locale'), 'Session has no locale value');
    }

    /**
     * @test
     */
    public function it_redirect_base_route_to_home_page_with_default_language()
    {
        $requestMock = $this->mockLanguageListenerService();

        $requestMock->setEntityManager($this->mockDefaultLanguage('ru', 'Russian'));

        $this->client->request('GET', '/');

        // Assert Session has the DefaultLocale value.
        $this->assertTrue($this->client->getRequest()->getSession()->has('locale'), 'Session has locale value');
        $this->assertEquals('ru', $this->client->getRequest()->getSession()->get('locale'), 'Session has no locale value');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();

        // Assert Session has the DefaultLocale value.
        $this->assertTrue($this->client->getRequest()->getSession()->has('locale'), 'Session has locale value');
        $this->assertEquals('ru', $this->client->getRequest()->getSession()->get('locale'), 'Session has no locale value');

        $this->assertEquals('http://localhost/ru/', $this->client->getHistory()->current()->getUri());
    }
}
