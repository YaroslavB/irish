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
        $admin->setZipcode('12345');
        $manager->persist($admin);

        // Create Regular Users
        $userNames = [
            ['John Doe', 'john@example.com', '+353111111111', '10 O\'Connell Street, Dublin', '11111'],
            ['Jane Smith', 'jane@example.com', '+353222222222', '20 Grafton Street, Cork', '22222'],
            ['Mike Johnson', 'mike@example.com', '+353333333333', '30 High Street, Galway', '33333'],
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
            ['Jameson Irish Whiskey', 'Smooth and versatile Irish whiskey with hints of vanilla and sweet sherry.', '35.99', 50],
            ['Bushmills Original', 'Light and smooth with a fresh fruit and honey aroma.', '32.50', 45],
            ['Tullamore Dew', 'Triple distilled with citrus fruit and malt characteristics.', '30.00', 60],
            ['Redbreast 12 Year Old', 'Full-bodied pot still Irish whiskey with spice and fruit notes.', '65.00', 30],
            ['Teeling Small Batch', 'Crafted using both grain and malt whiskey with rum cask finish.', '38.50', 40],
            ['Green Spot Single Pot Still', 'Elegant single pot still whiskey with orchard fruit and toasted oak.', '54.00', 25],
            ['Powers Gold Label', 'Spicy, honeyed pot still whiskey and a classic Irish pub staple.', '31.00', 55],
            ['Connemara Peated Single Malt', 'Rare peated Irish single malt with gentle smoke and sweet malt.', '48.00', 35],
            ['Writers Tears Copper Pot', 'A blend of single malt and single pot still with a soft, honeyed finish.', '42.00', 38],
            ['Midleton Very Rare', 'Exceptionally smooth vintage blend from the Midleton distillery.', '210.00', 10],
        ];

        $this->createProducts($manager, $whiskeyProducts, $categories[0]);

        // Create Products for Irish Beer
        $beerProducts = [
            ['Guinness Draught', 'The iconic Irish stout with a rich, creamy head and distinctive taste.', '4.50', 200],
            ['Smithwick\'s Red Ale', 'Classic Irish red ale with a sweet taste and a hint of hops.', '3.99', 150],
            ['Murphy\'s Irish Stout', 'Cork\'s legendary stout with a smooth finish.', '4.20', 120],
            ['Kilkenny Irish Cream Ale', 'Smooth and creamy with a distinctive red color.', '4.30', 100],
            ['Beamish Stout', 'Full-bodied Irish stout with a smooth malty taste.', '3.85', 80],
            ['Harp Lager', 'Crisp and refreshing Irish lager brewed in Dundalk.', '3.75', 130],
            ['O\'Hara\'s Irish Stout', 'Award-winning craft stout with rich coffee and chocolate notes.', '4.60', 90],
            ['Galway Hooker Irish Pale Ale', 'Hoppy, aromatic pale ale from one of Ireland\'s pioneering craft brewers.', '4.75', 70],
            ['Franciscan Well Rebel Red', 'Cork craft red ale with caramel malt and a balanced bitterness.', '4.55', 65],
        ];

        $this->createProducts($manager, $beerProducts, $categories[1]);

        // Create Products for Irish Spirits
        $spiritsProducts = [
            ['Baileys Irish Cream', 'The original Irish cream liqueur with hints of vanilla and chocolate.', '24.99', 75],
            ['Irish Mist', 'Whiskey-based liqueur with honey and herbs.', '28.50', 50],
            ['Sheridan\'s Coffee Layered Liqueur', 'Unique two-bottle liqueur combining coffee and cream.', '32.00', 40],
            ['Coole Swan Irish Cream', 'Premium blend of single malt whiskey, white chocolate and cream.', '29.50', 45],
            ['Dingle Original Gin', 'Small-batch Irish gin with botanicals foraged from the Dingle peninsula.', '39.00', 55],
            ['Gunpowder Irish Gin', 'Distinctive gin infused with slow-dried gunpowder tea.', '36.50', 60],
        ];

        $this->createProducts($manager, $spiritsProducts, $categories[2]);

        // Create Products for Irish Wine
        $wineProducts = [
            ['Thomas Walk Cabernet', 'Barrel-aged red from one of Ireland\'s few vineyards in West Cork.', '24.00', 30],
            ['Lusca Chardonnay', 'Crisp cool-climate white wine grown in North County Dublin.', '22.50', 25],
            ['Mead of Kinsale', 'Traditional honey wine crafted in the medieval Irish style.', '19.99', 40],
            ['Bunratty Meade', 'Classic Irish honey wine flavoured with herbs, served at medieval banquets.', '17.50', 50],
        ];

        $this->createProducts($manager, $wineProducts, $categories[3]);

        // Create Products for Irish Cider
        $ciderProducts = [
            ['Bulmers Original', 'Ireland\'s favorite cider made from 17 varieties of apples.', '5.50', 180],
            ['Magners Original', 'Crisp and refreshing Irish cider served over ice.', '5.25', 160],
            ['Orchard Thieves', 'Light and crisp apple cider with a hint of sweetness.', '5.00', 140],
            ['Stonewell Medium Dry Cider', 'Craft Irish cider from Kinsale made with 100% fresh apple juice.', '6.20', 90],
            ['Longueville House Cider', 'Estate-grown cider from County Cork with a dry, refined character.', '7.50', 60],
        ];

        $this->createProducts($manager, $ciderProducts, $categories[4]);

        // Create Gift Sets
        $giftSetProducts = [
            ['Irish Whiskey Tasting Set', 'A collection of 5 premium Irish whiskeys in miniature bottles.', '45.00', 25],
            ['Irish Pub Collection', 'Includes Guinness, Smithwick\'s and Kilkenny for the ultimate Irish pub experience.', '15.99', 50],
            ['Premium Irish Collection', 'Jameson, Baileys and Guinness gift pack.', '55.00', 30],
            ['Craft Gin Discovery Box', 'Trio of Irish craft gins with a copa glass and botanical guide.', '65.00', 20],
            ['Stout Lovers Hamper', 'Guinness, Murphy\'s, Beamish and O\'Hara\'s with two branded glasses.', '29.99', 35],
        ];

        $this->createProducts($manager, $giftSetProducts, $categories[5]);

        // Create Accessories
        $accessoriesProducts = [
            ['Guinness Pint Glass Set', 'Set of 4 official Guinness embossed pint glasses.', '22.50', 60],
            ['Irish Whiskey Tumbler Set', 'Crystal whiskey glasses with Celtic design.', '35.00', 45],
            ['Cork Coaster Set', 'Set of 6 Irish-themed cork coasters.', '12.99', 80],
            ['Stainless Steel Hip Flask', '7oz brushed steel hip flask engraved with a Celtic knot.', '18.50', 70],
            ['Whiskey Stones Gift Set', 'Set of 9 granite chilling stones with a storage pouch.', '15.00', 90],
            ['Guinness Bottle Opener', 'Cast iron wall-mounted opener with the classic Guinness harp.', '9.99', 110],
        ];

        $this->createProducts($manager, $accessoriesProducts, $categories[6]);

        $manager->flush();
    }

    /**
     * @param array<array{0: string, 1: string, 2: string, 3: int}> $rows
     */
    private function createProducts(ObjectManager $manager, array $rows, Category $category): void
    {
        foreach ($rows as $productData) {
            $product = new Product();
            $product->setTitle($productData[0]);
            $product->setDescription($productData[1]);
            $product->setPrice($productData[2]);
            $product->setQuantity($productData[3]);
            $product->setCategory($category);
            $product->setIsPublished(true);
            $manager->persist($product);
        }
    }
}
