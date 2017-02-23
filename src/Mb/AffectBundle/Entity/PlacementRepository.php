<?php

namespace Mb\AffectBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PlacementRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlacementRepository extends EntityRepository
{
	/*Get all placement with temp value give at the department give*/
	public function getPlacementTempWithDepart($temp,$department){
		
		$qb= $this->createQueryBuilder('p');
			
		$qb
			->leftjoin('p.user', 'u')
			->addSelect('u')
			->leftjoin('u.department', 'd')
			->addSelect('d');


		$qb->where('u.active=:active')
			->setParameter('active',true)
			->andWhere('p.temp =:temp')
			->setParameter('temp', $temp)
			->andWhere('d.id =:id')
			->setParameter('id', $department->getId());


		$query = $qb->getQuery();

		$results = $query->getResult();

		return $results;
	}	
}
