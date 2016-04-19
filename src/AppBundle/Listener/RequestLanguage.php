<?php

namespace AppBundle\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class RequestLanguage
 * @package AppBundle\Listener
 */
class RequestLanguage
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var \AppBundle\Entity\Language[]
     */
    protected $languages;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param \Twig_Environment $twig
     */
    public function setTwig($twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function onKernelRequest($event)
    {
        $request =  $event->getRequest();

        // Cancel if special route.
        if ($this->isSpecialRoute($request)) {
            return;
        }

        $locale = $this->setUpLocale($request);

        if ($this->isBaseRoute($request, $event, $locale)) {
            return;
        }

        $this->twig->addGlobal('languages', $this->getLanguages());
        $this->twig->addGlobal('default_locale', $locale);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    protected function setUpLocale($request)
    {
        $locale = $request->get('_locale');

        if (! is_null($locale) && $locale === $request->getSession()->get('locale')) {
            return $locale;
        }

        if (! $this->isValidLocale($locale)) {
            $locale = ($request->getSession()->has('locale'))
                ? $request->getSession()->get('locale')
                : $this->getDefaultLanguage()->getCode()
            ;
        }

        $request->getSession()->set('locale', $locale);

        return $locale;
    }

    /**
     * @param string $locale
     *
     * @return bool
     */
    protected function isValidLocale($locale)
    {
        $langauges = $this->getLanguages();

        $locales = [];

        foreach ($langauges as $langauge) {
            $locales[] = $langauge->getCode();
        }

        return in_array($locale, $locales);
    }

    /**
     * @return \AppBundle\Entity\Language[]
     */
    protected function getLanguages()
    {
        if (is_null($this->languages)) {
            $this->languages = $this->entityManager->getRepository('AppBundle:Language')->findAll();
        }

        return $this->languages;
    }

    /**
     * @return \AppBundle\Entity\Language
     */
    protected function getDefaultLanguage()
    {
        return $this->entityManager->getRepository('AppBundle:Language')->findDefault();
    }

    /**
     * Check if the request route starts with '_'.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool
     */
    private function isSpecialRoute($request)
    {
        return (0 === strpos($request->get('_route'), '_')) || is_null($request->get('_route'));
    }

    /**
     * If Base route, redirect to Home page.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     * @param string $locale
     *
     * @return bool
     */
    private function isBaseRoute($request, $event, $locale)
    {
        if ('base' === $request->get('_route')) {
            $event->setResponse(new RedirectResponse(sprintf('/%s/', $locale)));

            return true;
        }

        return false;
    }
}
