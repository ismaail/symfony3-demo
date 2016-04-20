<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Language;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\LanguageType;

/**
 * Class LanguageController
 * @package AdminBundle\Controller
 */
class LanguageController extends Controller
{
    /**
     * @var \AppBundle\Repository\LanguageRepository
     */
    protected $languageRepository;

    /**
     * List Languages.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $languages = $this->getLanguageRepository()->findAll();

        return $this->render('admin/language.html.twig', compact($languages));
    }

    /**
     * Create new Language.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $form = $this->createLanguageForm()->createView();

        return $this->render('admin/create.html.twig', compact('form'));
    }

    /**
     * Store the new Language.
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function storeAction(Request $request)
    {
        $language = new Language();
        $form = $this->createLanguageForm($language);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getLanguageRepository()->create($language);
            /** @todo Create 'translations/messages.%code%.yml' file for the new created Language. */

            $this->get('session')->getFlashBag()->add('success', 'Language is succesfully created.');

            return $this->redirectToRoute('admin.language');
        }

        return $this->render('admin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Language $language
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createLanguageForm(Language $language = null)
    {
        return $this->createForm(LanguageType::class, $language, [
            'action' => $this->generateUrl('admin.language.store'),
            'method' => 'POST',
        ]);
    }

    /**
     * @return \AppBundle\Repository\LanguageRepository
     */
    protected function getLanguageRepository()
    {
        if (! $this->languageRepository) {
            $this->languageRepository =
                $this->get('doctrine.orm.default_entity_manager')->getRepository('AppBundle:Language');
        }

        return $this->languageRepository;
    }
}
