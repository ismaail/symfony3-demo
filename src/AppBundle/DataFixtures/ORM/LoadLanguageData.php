<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Yaml\Parser;
use AppBundle\Entity\Language;

/**
 * Class LoadLanguageData
 * @package AppBundle\DataFixtures\ORM
 */
class LoadLanguageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @const int
     */
    const ORDER = 0;

    /**
     * @return int
     */
    public function getOrder()
    {
        return self::ORDER;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->createLanguages($this->getList()) as $language) {
            $manager->persist($language);
        }

        $manager->flush();
    }

    /**
     * @param array $list
     *
     * @return \Generator|Language
     */
    protected function createLanguages($list)
    {
        foreach ($list as $code => $name) {
            $language = new Language();
            $language
                ->setCode($code)
                ->setName($name)
            ;

            yield $language;
        }
    }

    /**
     * Load Data from external file.
     *
     * @return array
     */
    protected function getList()
    {
        $parser = new Parser();
        $data = $parser->parse(file_get_contents(__DIR__ . '/../../../../app/config/languages.yml'));

        return $data['languages'];
    }
}
