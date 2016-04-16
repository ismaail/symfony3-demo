<?php

namespace Tests\AppBundle\Repository;

use Tests\DoctrineTestCase;

/**
 * Class LanguageRepositoryTest
 * @package AppBundle\Repository
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @codingStandardsIgnoreFile
 */
class LanguageRepositoryTest extends DoctrineTestCase
{
    /**
     * @var \AppBundle\Repository\LanguageRepository
     */
    protected $languageRepository;

    protected function setUp()
    {
        parent::setUp();

        $this->languageRepository = $this->getEntityManager()->getRepository('AppBundle:Language');
    }

    /**
     * @test
     */
    public function languages_table_is_initialy_empty()
    {
        $languages = $this->languageRepository->findAll();

        $this->assertCount(0, $languages);
    }
}
