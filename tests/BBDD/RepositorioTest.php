<?php

	require_once '/var/www/html/phpunit_josegitbook/src/BBDD/clases.php';
	require_once '/var/www/html/phpunit_josegitbook/src/BBDD/repositorios.php';

	//use App\BBDD\User;

	class UserRepositoryTest extends \PHPUnit\Framework\TestCase {

	    protected static $pdo;

	    public static function setUpBeforeClass() {

	       try {

	            $host = 'mysql:host=localhost;dbname=videoclub';
	            self::$pdo = new PDO($host, 'shilum', 'shilum');

	        } catch (\Exception $e) {

	            $this->markTestSkipped('MySQL conection is not working.');

	        }

	    }

	    public function setUp() {

	        $loader = new Nelmio\Alice\Loader\NativeLoader();
	        $this->data = $loader -> loadFile(__DIR__.'/fixtures.yml') -> getObjects();

	        $this->userRepository = new UserRepository(self::$pdo);

	    }

	    public function tearDown(){
	        self::$pdo->query("set foreign_key_checks=0");
	        self::$pdo->query("TRUNCATE User");
	        self::$pdo->query("TRUNCATE Photo");
	        self::$pdo->query("set foreign_key_checks=1");
	    }

	    public function testStoreUser() {
	        $user = $this->data['user1'];
	        $this->assertNull($user->id);
	        $user = $this->userRepository->storeUser($user);
	        $this->assertObjectHasAttribute('id', $user);
	    }

	    public function testStoreUserWithPhotos() {

	        $user = $this->data["user1"]; 
	        $this->assertNull($this->data["photo1"]->id);
	        $this->assertNull($this->data["photo2"]->id);
	        $this->assertNull($this->data["photo3"]->id);
	        $user->addPhoto($this->data["photo1"]);
	        $user->addPhoto($this->data["photo2"]);
	        $user->addPhoto($this->data["photo3"]);

	        $user = $this->userRepository->storeUser($this->data['user1']);
	        $photos = $user->getPhotos();
	        
	        foreach($photos as $photo) {
	            $this->assertGreaterThan(0, $photo->id);
	        }
	    }

	    public function testGetUserById(){
	        $user = $this->userRepository->storeUser($this->data['user1']);
	        unset($user);
	        $user = $this->userRepository->getById(1);
	        $this->assertEquals($this->data['user1']->username, $user->username);
	    }

	    public function testGetUserByIdWithPhotos() {
	        $user = $this->data["user1"]; 
	        $user->addPhoto($this->data["photo1"]);
	        $user->addPhoto($this->data["photo2"]);
	        $user->addPhoto($this->data["photo3"]);
	        $user = $this->userRepository->storeUser($this->data['user1']);
	        unset($user);
	        $user = $this->userRepository->getById(1);
	        $this->assertEquals($this->data['user1']->username, $user->username);
	        $this->assertEquals(3, count($user->getPhotos()));
	    }
	}

?>