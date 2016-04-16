<?php

namespace AppBundle\Listener;

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

        $this->twig->addGlobal('languages', $this->findLanguages());
    }

    /**
     * @return \AppBundle\Entity\Language[]
     */
    protected function findLanguages()
    {
        return $this->entityManager->getRepository('AppBundle:Language')->findAll();
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
        return 0 === strpos($request->get('_route'), '_');
    }
}
