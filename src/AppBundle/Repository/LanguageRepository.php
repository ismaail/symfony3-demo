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
}
