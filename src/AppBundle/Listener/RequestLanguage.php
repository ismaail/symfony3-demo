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

        $defaultLanguage = $this->getDefaultLanguage();

        if ($this->isBaseRoute($request, $event, $defaultLanguage->getCode())) {
            return;
        }

        $languages = $this->getLanguages();

        $this->twig->addGlobal('languages', $languages);
        $this->twig->addGlobal('default_language', $request->get('_locale'));
    }

    /**
     * @return \AppBundle\Entity\Language[]
     */
    protected function getLanguages()
    {
        return $this->entityManager->getRepository('AppBundle:Language')->findAll();
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
