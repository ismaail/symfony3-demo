<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\LanguageType;
use AppBundle\Entity\Language;

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
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        $language = $this->getLanguageRepository()->findOrFail($id);

        $form = $this->createLanguageForm($language, true)->createView();

        return $this->render('admin/edit.html.twig', compact('language', 'form'));
    }

    /**
     * @param int $id
     * @param Request $request
     *
     * @return \\Symfony\Component\HttpFoundation\Response
     *
     * @throws \AppBundle\Repository\LanguageRepositoryException
     */
    public function updateAction($id, Request $request)
    {
        $language = $this->getLanguageRepository()->findOrFail($id);
        $form = $this->createLanguageForm($language, true);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getLanguageRepository()->update($language);
            $this->get('session')->getFlashBag()->add('success', 'Language successfully updated.');

            return $this->redirectToRoute('admin.language');
        }

        return $this->render('admin/edit.html.twig', [
            'language' => $language,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Language $language
     * @param bool $isUpdate
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function createLanguageForm(Language $language = null, $isUpdate = false)
    {
        $options = $this->getLanguageFormOptions($isUpdate, $language);

        return $this->createForm(LanguageType::class, $language, [
            'action' => $options['action'],
            'method' => $options['method'],
        ]);
    }

    /**
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $language = $this->getLanguageRepository()->findOrFail($id);

        $this->getLanguageRepository()->delete($language);

        $this->get('session')->getFlashBag()->add('success', 'Language deleted successfully');

        return $this->redirectToRoute('admin.language');
    }

    /**
     * @param bool $isUpdate
     * @param Language $language
     *
     * @return array
     */
    protected function getLanguageFormOptions($isUpdate = false, Language $language = null)
    {
        $options = [];

        $options['action'] = $isUpdate
            ? $this->generateUrl('admin.language.update', ['id' => $language->getId()])
            : $this->generateUrl('admin.language.store');

        $options['method'] = $isUpdate
            ? 'PUT'
            : 'POST';

        return $options;
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
