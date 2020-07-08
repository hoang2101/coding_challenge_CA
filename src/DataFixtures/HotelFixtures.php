<?php

namespace App\DataFixtures;

use App\Entity\Hotel;
use Doctrine\Persistence\ObjectManager;

class HotelFixtures extends AppFixtures
{
    public function loadData(ObjectManager $manager)
    {	
    	$randomNameSet = array("Marina Bay Sands",
            "The Beverly Hills Hotel",
            "Copacabana Palace",
            "La Mamounia",
            "Ballyfin",
            "La Bastide de Gordes",
            "D-Maris Bay",
            "The Peninsula Shanghai",
            "Nihi Sumba"
        );
    	
        $this->createMany(Hotel::class, 10, function(Hotel $hotel, array $randomNames) {
			$hotel->setName($randomNames[array_rand($randomNames)]);
        }, $randomNameSet);

        $manager->flush();
    }
}
