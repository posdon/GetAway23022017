<?php

namespace Mb\UniversityBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UniversityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UniversityRepository extends EntityRepository
{
	public function getAllUniv($department)
	{
		$qb=$this->createQueryBuilder('u');

		$qb->join('u.places', 'p')
			->where('p.places != :places')
			->setParameter('places',0)
			->andWhere('p.department = :department')
			->setParameter('department',$department)
			->join('u.country', 'c')
			->orderBy('c.name', 'ASC');


		
		
		
		$query = $qb->getQuery();
		
		$results = $query->getResult();
		
		return $results;
	}
	

	public function getFirstOrCreate($name,$country,$em = null){

		if(!$em)
			$em =  $this->getEntityManager();
		
		$qb = $em->createQueryBuilder();
		$qb->select("u")
			->from('MbUniversityBundle:University','u')
			->where('u.name = :u_name')
			->andWhere('u.country = :country')
			->setParameters(array('u_name'=>$name,'country' => $country));

		$university = $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();

		if(!$university || ($university->getCountry() != $country)){
			$university = new University();
			$university-> setName($name);
			$university->setCountry($country);
			$em->persist($university);
		}

		
		return $university;

	}
}
