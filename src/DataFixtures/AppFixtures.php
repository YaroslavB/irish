<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create Admin User
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setIsVerified(true);
        $admin->setFullName('Admin User');
        $admin->setPhone('+353123456789');
        $admin->setAddress('123 Main Street, Dublin');
        $admin->setZipcode(12345);
        $manager->persist($admin);

        // Create Regular Users
        $users = [];
        $userNames = [
            ['John Doe', 'john@example.com', '+353111111111', '10 O\'Connell Street, Dublin', 11111],
            ['Jane Smith', 'jane@example.com', '+353222222222', '20 Grafton Street, Cork', 22222],
            ['Mike Johnson', 'mike@example.com', '+353333333333', '30 High Street, Galway', 33333],
        ];

        foreach ($userNames as $userData) {
            $user = new User();
            $user->setEmail($userData[1]);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password123'));
            $user->setIsVerified(true);
            $user->setFullName($userData[0]);
            $user->setPhone($userData[2]);
            $user->setAddress($userData[3]);
            $user->setZipcode($userData[4]);
            $manager->persist($user);
            $users[] = $user;
        }

        // Create Categories
        $categories = [];
        $categoryNames = [
            'Irish Whiskey',
            'Irish Beer',
            'Irish Spirits',
            'Irish Wine',
            'Irish Cider',
            'Gift Sets',
            'Accessories'
        ];

        foreach ($categoryNames as $categoryName) {
            $category = new Category();
            $category->setTitle($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create Products for Irish Whiskey
        $whiskeyProducts = [
            ['Jameson Irish Whiskey', 'Smooth and versatile Irish whiskey with hints of vanilla and sweet sherry.', 35.99, 50],
            ['Bushmills Original', 'Light and smooth with a fresh fruit and honey aroma.', 32.50, 45],
            ['Tullamore Dew', 'Triple distilled with citrus fruit and malt characteristics.', 30.00, 60],
            ['Redbreast 12 Year Old', 'Full-bodied pot still Irish whiskey with spice and fruit notes.', 65.00, 30],
            ['Teeling Small Batch', 'Crafted using both grain and malt whiskey with rum cask finish.', 38.50, 40],
        ];

        foreach ($whiskeyProducts as $productData) {
            $product = new Product();
            $product->setTitle($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setQuantity($productData[3]);
            $product->setCategory($categories[0]);
            $product->setIsPublished(true);
            $manager->persist($product);
        }

        // Create Products for Irish Beer
        $beerProducts = [
            ['Guinness Draught', 'The iconic Irish stout with a rich, creamy head and distinctive taste.', 4.50, 200],
            ['Smithwick\'s Red Ale', 'Classic Irish red ale with a sweet taste and a hint of hops.', 3.99, 150],
            ['Murphy\'s Irish Stout', 'Cork\'s legendary stout with a smooth finish.', 4.20, 120],
            ['Kilkenny Irish Cream Ale', 'Smooth and creamy with a distinctive red color.', 4.30, 100],
            ['Beamish Stout', 'Full-bodied Irish stout with a smooth malty taste.', 3.85, 80],
        ];

        foreach ($beerProducts as $productData) {
            $product = new Product();
            $product->setTitle($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setQuantity($productData[3]);
            $product->setCategory($categories[1]);
            $product->setIsPublished(true);
            $manager->persist($product);
        }

        // Create Products for Irish Spirits
        $spiritsProducts = [
            ['Baileys Irish Cream', 'The original Irish cream liqueur with hints of vanilla and chocolate.', 24.99, 75],
            ['Irish Mist', 'Whiskey-based liqueur with honey and herbs.', 28.50, 50],
            ['Sheridan\'s Coffee Layered Liqueur', 'Unique two-bottle liqueur combining coffee and cream.', 32.00, 40],
        ];

        foreach ($spiritsProducts as $productData) {
            $product = new Product();
            $product->setTitle($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setQuantity($productData[3]);
            $product->setCategory($categories[2]);
            $product->setIsPublished(true);
            $manager->persist($product);
        }

        // Create Products for Irish Cider
        $ciderProducts = [
            ['Bulmers Original', 'Ireland\'s favorite cider made from 17 varieties of apples.', 5.50, 180],
            ['Magners Original', 'Crisp and refreshing Irish cider served over ice.', 5.25, 160],
            ['Orchard Thieves', 'Light and crisp apple cider with a hint of sweetness.', 5.00, 140],
        ];

        foreach ($ciderProducts as $productData) {
            $product = new Product();
            $product->setTitle($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setQuantity($productData[3]);
            $product->setCategory($categories[4]);
            $product->setIsPublished(true);
            $manager->persist($product);
        }

        // Create Gift Sets
        $giftSetProducts = [
            ['Irish Whiskey Tasting Set', 'A collection of 5 premium Irish whiskeys in miniature bottles.', 45.00, 25],
            ['Irish Pub Collection', 'Includes Guinness, Smithwick\'s and Kilkenny for the ultimate Irish pub experience.', 15.99, 50],
            ['Premium Irish Collection', 'Jameson, Baileys and Guinness gift pack.', 55.00, 30],
        ];

        foreach ($giftSetProducts as $productData) {
            $product = new Product();
            $product->setTitle($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setQuantity($productData[3]);
            $product->setCategory($categories[5]);
            $product->setIsPublished(true);
            $manager->persist($product);
        }

        // Create Accessories
        $accessoriesProducts = [
            ['Guinness Pint Glass Set', 'Set of 4 official Guinness embossed pint glasses.', 22.50, 60],
            ['Irish Whiskey Tumbler Set', 'Crystal whiskey glasses with Celtic design.', 35.00, 45],
            ['Cork Coaster Set', 'Set of 6 Irish-themed cork coasters.', 12.99, 80],
        ];

        foreach ($accessoriesProducts as $productData) {
            $product = new Product();
            $product->setTitle($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setQuantity($productData[3]);
            $product->setCategory($categories[6]);
            $product->setIsPublished(true);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
