<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Language;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class LanguageRepository
 * @package AppBundle\Repository
 *
 * @method Language[] findAll()
 */
class LanguageRepository extends EntityRepository
{
    /**
     * Fidn the deault language.
     *
     * @return Language
     *
     * @throws NoResultException                            If not default language is found.
     * @throws \Doctrine\ORM\NonUniqueResultException       If more than one default language is found.
     */
    public function findDefault()
    {
        $qb = $this->createQueryBuilder('l');
        $qb
            ->select('l')
            ->where('l.isDefault = true')
        ;

        return $qb->getQuery()->getSingleResult();
    }

    /**
     * @param $id
     *
     * @return Language
     *
     * @throws NotFoundHttpException        If Language is not found.
     */
    public function findOrFail($id)
    {
        try {
            $qb = $this->createQueryBuilder('l');
            $qb
                ->select('l')
                ->where('l.id = :id')
                ->setParameter('id', $id)
            ;

            return $qb->getQuery()->getSingleResult();

        } catch (NoResultException $e) {
            throw new NotFoundHttpException('Language not found', $e);
        }
    }

    /**
     * @param Language $language
     *
     * @return Language
     *
     * @throws LanguageRepositoryException
     */
    public function create(Language $language)
    {
        $entityManager = $this->getEntityManager();

        try {
            $entityManager->beginTransaction();

            $entityManager->persist($language);

            $this->setDefaultLanguage($language);

            $entityManager->flush();
            $entityManager->commit();

            return $language;

        } catch (\Exception $e) {
            $entityManager->rollback();
            throw new LanguageRepositoryException("Error creating new Language.", 0, $e);
        }
    }

    /**
     * @param Language $language
     *
     * @return Language
     *
     * @throws LanguageRepositoryException
     */
    public function update(Language $language)
    {
        $entityManager = $this->getEntityManager();

        try {
            $entityManager->beginTransaction();

            $this->setDefaultLanguage($language);

            $entityManager->flush();
            $entityManager->commit();

            return $language;

        } catch (\Exception $e) {
            $entityManager->rollback();
            throw new LanguageRepositoryException("Error updating the Language.", 0, $e);
        }
    }

    /**
     * @param Language $language
     *
     * @throws LanguageRepositoryException
     */
    public function delete(Language $language)
    {
        if ($language->getId() === $this->findDefault()->getId()) {
            throw new LanguageRepositoryException("Cannot delete the Default Language.");
        }

        try {
            $this->getEntityManager()->beginTransaction();

            $this->getEntityManager()->remove($language);

            $this->getEntityManager()->flush();
            $this->getEntityManager()->commit();

        } catch (\Exception $e) {
            $this->getEntityManager()->rollback();
            throw new LanguageRepositoryException("Error deleting the Language", 0, $e);
        }
    }

    /**
     * Set Default Language to false
     *
     * @param Language $language
     */
    protected function setDefaultLanguage(Language $language)
    {
        $defaultLanguage = $this->findDefault();

        if (! $language->getIsDefault()) {
            // Prevents leaving default language empty.
            if ($defaultLanguage->getId() === $language->getId()
                && false === $defaultLanguage->getIsDefault()
            ) {
                $language->setIsDefault(true);
            }

            return;
        }

        // Same Language
        if ($defaultLanguage->getId() === $language->getId()) {
            return;
        }

        $defaultLanguage->setIsDefault(false);
    }
}
