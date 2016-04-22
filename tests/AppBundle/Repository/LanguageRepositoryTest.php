<?php

namespace Tests\AppBundle\Repository;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Repository\LanguageRepositoryException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use AppBundle\Entity\Language;
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
     * Create & persist Language entity in the database.
     *
     * @param array $data
     */
    private function createLanguage(array $data)
    {
        $language = new Language();
        $language
            ->setCode($data['code'])
            ->setName($data['name'])
            ->setIsDefault($data['isDefault'])
        ;

        $this->getEntityManager()->persist($language);
        $this->getEntityManager()->flush();
    }

    /**
     * @test
     */
    public function languages_table_is_initialy_empty()
    {
        $languages = $this->languageRepository->findAll();

        $this->assertCount(0, $languages);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_no_default_language_is_found()
    {
        $this->expectException(NoResultException::class);

        $this->languageRepository->findDefault();
    }

    /**
     * @test
     */
    public function it_throws_exception_if_more_than_one_default_language_is_found()
    {
        $this->createLanguage(['code' => 'en', 'name' => 'English', 'isDefault' => true]);
        $this->createLanguage(['code' => 'fr', 'name' => 'Français', 'isDefault' => true]);

        $this->expectException(NonUniqueResultException::class);

        $this->languageRepository->findDefault();
    }

    /**
     * @test
     */
    public function it_returns_the_correct_default_language()
    {
        $this->createLanguage(['code' => 'en', 'name' => 'English', 'isDefault' => false]);
        $this->createLanguage(['code' => 'fr', 'name' => 'Français', 'isDefault' => true]);
        $this->createLanguage(['code' => 'de', 'name' => 'Deutsch', 'isDefault' => false]);

        $language = $this->languageRepository->findDefault();

        $this->assertInstanceOf(Language::class, $language);
        $this->assertSame('fr', $language->getCode());
        $this->assertSame('Français', $language->getName());
        $this->assertSame(true, $language->getIsDefault());
    }

    /**
     * @test
     */
    public function it_sets_language_isDefault_value_to_false()
    {
        $language = new Language();
        $language
            ->setCode('en')
            ->setName('English')
        ;

        $this->getEntityManager()->persist($language);
        $this->getEntityManager()->flush();

        $languages = $this->languageRepository->findAll();

        $this->assertCount(1, $languages);
        $this->assertFalse($languages[0]->getIsDefault(), 'Language "isDefault" value is not "false"');
    }

    /**
     * @test
     */
    public function it_create_new_language()
    {
        $this->assertCount(0, $this->languageRepository->findAll(), 'Language Table has not 0 entries.');

        // Create default language
        $this->createLanguage([
            'code' => 'en',
            'name' => 'English',
            'isDefault' => true,
        ]);

        $this->assertCount(1, $this->languageRepository->findAll(), 'Language Table has not 1 entry.');

        $language = new Language();
        $language
            ->setCode('xy')
            ->setName('X Y')
        ;

        $this->languageRepository->create($language);

        $languages =$this->languageRepository->findAll();

        $this->assertCount(2, $languages, 'Language Table has not 2 entries.');
        $this->assertEquals('xy', $languages[1]->getCode());
        $this->assertEquals('X Y', $languages[1]->getName());
        $this->assertFalse($languages[1]->getIsDefault());
    }

    /**
     * @test
     */
    public function it_create_new_language_and_set_it_as_new_default_language()
    {
        $this->createLanguage([
            'code' => 'en',
            'name' => 'English',
            'isDefault' => true,
        ]);

        $this->assertCount(1, $this->languageRepository->findAll(), 'Language Table has not 0 entries.');

        $language = new Language();
        $language
            ->setCode('xyz')
            ->setName('X Y Z')
            ->setIsDefault(true)
        ;

        // Repository creates the new Language.
        $this->languageRepository->create($language);

        $languages =$this->languageRepository->findAll();

        $this->assertCount(2, $this->languageRepository->findAll(), 'Language Table has not 0 entries.');

        // Assert old Language
        $this->assertEquals('en', $languages[0]->getCode());
        $this->assertFalse($languages[0]->getIsDefault(), 'The Language is set to be the Default Language.');

        // Assert the created default Language
        $this->assertEquals('xyz', $languages[1]->getCode());
        $this->assertTrue($languages[1]->getIsDefault(), 'The Language is not set to be the Default Language.');

        // Assert Default Language
        $defaultLanguage = $this->languageRepository->findDefault();
        $this->assertEquals('xyz', $defaultLanguage->getCode(), 'Wrong Default Language');
    }

    /**
     * @test
     */
    public function it_throws_exception_if_language_is_not_found()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('Language not found');

        $this->languageRepository->findOrFail(0);
    }

    /**
     * @test
     */
    public function it_updates_language()
    {
        // Create Default Language
        $this->createLanguage([
            'code' => 'en',
            'name' => 'English',
            'isDefault' => true,
        ]);

        // Create Language to update
        $this->createLanguage([
            'code' => 'xx',
            'name' => 'X X',
            'isDefault' => false,
        ]);

        $this->assertCount(2, $this->languageRepository->findAll(), 'Language Table has not 2 entries');

        $language = $this->languageRepository->findOrFail(2);

        $this->assertEquals('xx', $language->getCode());
        $this->assertEquals('X X', $language->getName());
        $this->assertFalse($language->getIsDefault(), '"xx" Language is Default');

        // Update the Language
        $language
            ->setCode('yy')
            ->setName('Y Y')
            ->setIsDefault(true)
        ;
        $this->languageRepository->update($language);

        $updatedLanguage = $this->languageRepository->findOrFail(2);

        // Assert the updates
        $this->assertEquals('yy', $updatedLanguage->getCode());
        $this->assertEquals('Y Y', $language->getName());
        $this->assertTrue($language->getIsDefault(), '"yy" Language is not Default');

        // Assert old DefaultLanguage
        $oldDefault = $this->languageRepository->findOrFail(1);
        $this->assertEquals('en', $oldDefault->getCode());
        $this->assertFalse($oldDefault->getIsDefault(), '"en" Language is Default');

        $newDefault = $this->languageRepository->findDefault();
        $this->assertEquals('yy', $newDefault->getCode(), '"yy" Language is not Default');
    }

    /**
     * @test
     */
    public function it_keeps_default_language_default()
    {
        $this->createLanguage([
            'code' => 'en',
            'name' => 'English',
            'isDefault' => true,
        ]);

        $defaultLanguage = $this->languageRepository->findDefault();

        $this->assertEquals('en', $defaultLanguage->getCode());

        $language = $this->languageRepository->findOrFail(1);
        // Update the Language
        $language
            ->setCode('fr')
            ->setName('Français')
            ->setIsDefault(false)
        ;
        $this->languageRepository->update($language);

        $updatedLanguage = $this->languageRepository->findOrFail(1);

        // Assert update Language
        $this->assertEquals('fr', $updatedLanguage->getCode());
        $this->assertEquals('Français', $updatedLanguage->getName());
        $this->assertTrue($updatedLanguage->getIsDefault(), '"fr" Language is not Default.');
    }

    /**
     * @test
     */
    public function it_trows_exception_if_deleting_the_default_language()
    {
        $this->createLanguage([
            'code' => 'en',
            'name' => 'English',
            'isDefault' => true,
        ]);

        $this->assertCount(1, $this->languageRepository->findAll());

        $this->expectException(LanguageRepositoryException::class);
        $this->expectExceptionMessage('Cannot delete the Default Language');

        $language = $this->languageRepository->findDefault();

        $this->languageRepository->delete($language);
    }

    /**
     * @test
     */
    public function it_deletes_language()
    {
        $this->createLanguage([
            'code' => 'en',
            'name' => 'English',
            'isDefault' => true,
        ]);

        $this->createLanguage([
            'code' => 'fr',
            'name' => 'Français',
            'isDefault' => false,
        ]);

        // Assert the number items in Language table.
        $this->assertCount(2, $this->languageRepository->findAll());

        // Get the Non-Default Language.
        $language = $this->languageRepository->findOrFail(2);
        $this->languageRepository->delete($language);

        // Assert the number items in Language table.
        $this->assertCount(1, $this->languageRepository->findAll());

        // Assert 'fr' Language cannot be found.
        $this->expectException(NotFoundHttpException::class);

        $this->languageRepository->findOrFail(2);
    }
}
