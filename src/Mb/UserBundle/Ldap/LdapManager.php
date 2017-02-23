<?php
namespace Mb\UserBundle\Ldap;
use FR3D\LdapBundle\Ldap\LdapManager as BaseLdapManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Doctrine\ORM\EntityManager as EntityManager;
use FR3D\LdapBundle\Driver\LdapDriverInterface as LdapDriverInterface;

class LdapManager extends BaseLdapManager
{
    // Attributes used to access the Doctrine Entity Manager --> not reachable when not present in a Controller
    private $em;


    public function __construct(LdapDriverInterface $driver, $userManager, array $params, EntityManager $entityManager)
    {
        parent::__construct($driver, $userManager, $params);

        // Get and store the Doctrine Entity Manager. Was given in the service declaration
        $this->em = $entityManager;
    }


    /**
     * {@inheritDoc}
     */    
    protected function hydrate(UserInterface $user, array $entry)
    {
        parent::hydrate($user, $entry);


        // Set all the attributes of the User depending on the response of the LDAP.
        // the attributes are only the one described in config.yml, fr3d_ldap.user.attributes

        /*
         * $entry contains the attributes described in config.yml. It is an HashMap :
         * the key is the ldap attribute name
         * the element is an array with the value corresponding to the Key for a Student
         */
        //'$entry['supannetuid']' returns an array, with the studentId as the first element
		
		
		//Permet de savoir si c'est un étudiant
		$population = $entry['insapopulation'][0];
		
		if($population == 'etudiant'){//Si étudiant
			$studentId = $entry['supannetuid'][0]; 

			// 'yearStudy' attribute
			$promotion = $entry['insaclasseetu'][0];    //get the complete promotion, like 4INFO, or 2STPI
			
			$yearStudy = $promotion[0] ; // get only the number at the beginning of the string --> the year;
			
			
			if(is_numeric($yearStudy)){
			
				$user->setYearStudy($yearStudy);

				// 'department' attribute
				// Remove the '$yearStudy' number at the beginning of the '$promotion' --> to get the department
				$departmentName = trim($promotion, $yearStudy);     
				

				// Attributes specific to our Project
				$department = $this->em->getRepository('MbMainBundle:Department')
									->findOneBy(array('name' => $departmentName));
				$user->setDepartment($department);
			}else{//si c'est pas un numéro, c'est un étudiant en césure, on met 4eme année et le département ALL, cela sera traité plus tard pour mettre un bon départ (lechamp = REPOR dans le ldap)
				$user->setYearStudy(4);
				$department = $this->em->getRepository('MbMainBundle:Department')
							->findOneBy(array('name' => 'ALL'));
				$user->setDepartment($department);
			}



			
		}else{//Si employé
			$studentId = $entry['supannempid'][0];
			$studentId *= -1;
			
			$user->setYearStudy(4);
			$department = $this->em->getRepository('MbMainBundle:Department')
						->findOneBy(array('name' => 'ALL'));
			$user->setDepartment($department);

			
		}
		
		
		
		
		$username = $entry['uid'][0];      
		
		//Si un étudiant à le même username, on check si son numéro d'étudiant est le même. Si oui on update celui de la bdd,
		//	sinon on désactive celui de la bdd et on ajoute le nouveau
		
		$userDatabase = $this->em->getRepository('MbUserBundle:User')->findOneBy(array('username' => $username));
		if($userDatabase){
			if($userDatabase->getStudentId() == $studentId){
				//$user = $userDatabase; //Pourrais parraitre juste, mais cela va pas renvoyer $userDatabase en sortie..
			}elseif($userDatabase->getUsername() == $username){
				$userDatabase->setUsername($userDatabase->getId());
				$userDatabase->setEmail($userDatabase->getId());
				$userDatabase->setActive(false);
				$this->em->flush();
			}
		}
		
        $user->setStudentId($studentId);
		

        // 'firstName' attribute
        $firstName = $entry['givenname'][0];        
        $user->setFirstName($firstName);

		//setmail
		$mail = strtolower($entry['mail'][0]);        
        $user->setEmail($mail);

        // 'active' attribute
        $user->setActive(true);

        // 'rank' attribute
        $user->setRank(-1);

        // 'mean' attribute
        $user->setMean(-1);
    }



    public function findUserByEmail($email)
    {
        // 4 correspond to the 5th element in the array attributes in config.yml --> mail
        // 0 would be the uid
        $res = $this->findUserBy(array($this->ldapAttributes[4] => $email));
		return $res;
		
		
    }   public function findUserByStudentId($studentId)
    {
        // 4 correspond to the 5th element in the array attributes in config.yml --> mail
        // 0 would be the uid
        $res = $this->findUserBy(array($this->ldapAttributes[3] => $studentId));
		return $res;
    }

}