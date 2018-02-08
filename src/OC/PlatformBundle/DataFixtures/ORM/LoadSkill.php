<?php

namespace OC\PlatformBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;

class LoadSkill implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $skillNames = [
            'Symfony 2',  
            'Symfony 3',  
            'Symfony 4',  
            'Spring',  
            'Spring Boot',  
            'PHP',  
        ];
        
        foreach ($skillNames as $skillName){
            $skill = new Skill();
            $skill->setName($skillName);
            $manager->persist($skill);    
        }
        
        $manager->flush();
        
    }


}