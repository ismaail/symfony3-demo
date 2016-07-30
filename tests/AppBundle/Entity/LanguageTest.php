<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Language;

/**
 * Class Language
 * @package Tests\AppBundle\Entity
 *
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @codingStandardsIgnoreFile
 */
class LanguageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function to_string_method_should_return_class_title()
    {
        $language = new Language();

        self::assertEquals('Language', $language);
    }
}
