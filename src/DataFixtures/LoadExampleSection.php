<?php

namespace App\DataFixtures;

use App\Entity\Section;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Utils\Urls;

class LoadExampleSection extends Fixture implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $administrator = $manager->find('App:Administrator', 1);

        $sectionTitle = 'General';

        $section = new Section();
        $section->setAuthor($administrator);
        $section->setCreationDate(new \DateTime());
        $section->setTitle($sectionTitle);
        $section->setShowOnMenu(true);
        $section->setStatus(true);
        $section->setSlug(Urls::slugify($sectionTitle));
        $manager->persist($section);

        $manager->flush();
    }
}
