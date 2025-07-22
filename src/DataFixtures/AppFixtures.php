<?php
/**
 * This file is part of the App package.
 *
 * (c) Your Name <your.email@example.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class AppFixtures.
 *
 * Fixture class for loading initial data into the database.
 */
class AppFixtures extends Fixture
{
    /**
     * Load data fixtures with the passed EntityManager.
     *
     * @param ObjectManager $manager The ObjectManager instance
     */
    public function load(ObjectManager $manager): void
    {
        // Implement data loading logic here
        // Example:
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
