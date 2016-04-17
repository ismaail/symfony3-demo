<?php

namespace Tests\Traits;

use AppBundle\Repository\LanguageRepository;
use AppBundle\Listener\RequestLanguage;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Language;

/**
 * Class ServicesMocker
 * @package Tests\Traits
 * @property \Symfony\Bundle\FrameworkBundle\Client $client
 * @mixin \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
 */
trait ServicesMocker
{
    /**
     * @param array|null $methods
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|RequestLanguage
     */
    public function mockLanguageListenerService(array $methods = null)
    {
        $mock = $this
            ->getMockBuilder(RequestLanguage::class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock()
        ;

        $this->client->getContainer()->set('language_listener', $mock);

        return $mock;
    }

    /**
     * @param string $code
     * @param string $name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|EntityManager
     */
    public function mockDefaultLanguage($code, $name)
    {
        $entityManagerMock = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRepository'])
            ->getMock()
        ;

        $repositoryMock = $this
            ->getMockBuilder(LanguageRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findDefault'])
            ->getMock()
        ;

        $language = new Language();
        $language
            ->setCode($code)
            ->setName($name)
            ->setIsDefault(true)
        ;

        $repositoryMock
            ->expects($this->once())
            ->method('findDefault')
            ->willReturn($language)
        ;

        $entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock)
        ;

        return $entityManagerMock;
    }
}
