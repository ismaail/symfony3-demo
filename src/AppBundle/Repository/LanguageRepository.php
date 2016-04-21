<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Language;

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
     * @throws \Doctrine\ORM\NoResultException          If not default language is found.
     * @throws \Doctrine\ORM\NonUniqueResultException   If more than one default language is found.
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
     * @throws \Exception
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

            throw new \Exception("Error creating new Language.", 0, $e);
        }
    }

    /**
     * Set Default Language to false
     *
     * @param Language $language
     */
    protected function setDefaultLanguage(Language $language)
    {
        if (! $language->getIsDefault()) {
            return;
        }

        $defaultLanguage = $this->findDefault();

        // Same Language
        if ($defaultLanguage->getId() === $language->getId()) {
            return;
        }

        $defaultLanguage->setIsDefault(false);
    }
}
