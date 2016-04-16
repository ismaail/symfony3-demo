<?php

namespace Tests\Traits;

/**
 * Class ServicesMocker
 * @package Tests\Traits
 * @property \Symfony\Bundle\FrameworkBundle\Client $client
 * @mixin \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
 */
trait ServicesMocker
{
    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\AppBundle\Listener\RequestLanguage
     */
    public function mockLanguageListenerService()
    {
        $mock = $this
            ->getMockBuilder(\AppBundle\Listener\RequestLanguage::class)
            ->disableOriginalConstructor()
            ->setMethods(['onKernelRequest'])
            ->getMock()
        ;

        $mock
            ->expects($this->atLeastOnce())
            ->method('onKernelRequest')
            ->willReturn([])
        ;

        $this->client->getContainer()->set('language_listener', $mock);

        return $mock;
    }
}
