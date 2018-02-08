<?php
namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;


class LoadCategories implements FixtureInterface{
    public function load(ObjectManager $manager)
    {
        $names = [
            'Web dev',
            'Front-end',
            'Back-end',
            'Full stack',
            'Python',
            'PHP',
            'Java'
        ];
        
        foreach ($names as $newCategoryName){
            $category = new Category();
            $category->setName($newCategoryName);
            
            $manager->persist($category);
        }
        $manager->flush();
        
        
        
        
    }
}
