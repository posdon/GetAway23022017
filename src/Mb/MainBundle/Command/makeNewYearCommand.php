<?php

namespace Mb\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Mb\MainBundle\Entity\Year;

/*crée une nouvelle année*/
class makeNewYearCommand extends ContainerAwareCommand
{
    protected function configure()
    {
	        $this
            ->setName('main:newYear')
            ->setDescription('Create a new year')          
        ;
    }
	
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $currentYear = $em->getRepository('MbMainBundle:Year')->getCurrentYear();
		$newYear = new Year();
		$newYear->setYear($currentYear->getYear() + 1);
		
		$em->persist($newYear);

		$repository = $em->getRepository('MbUserBundle:User');
		
		$users=$repository->findBy(
			  array('active' => true)
			);
		
		
		//Désactive les utilisateurs pas co depuis 5 ans
		foreach($users as $user){
			if($user->getLastLogin() == null){
				$user->setLastLogin(new \DateTime());
			}else{
				$lastCo = $user->getLastLogin()->format('U');
				$timeToDisable = 60*60*24*365.25*5 + $lastCo;
				$user->setActive((time() < $timeToDisable));
				if(!$user->getActive()){
					$files = $user->getFilesSend();
					foreach($files as $file){
						$em->remove($file);
						$em->flush();
					}
				}
			}
		}
		
		$em->flush();
    }
}