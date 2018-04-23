<?php

require_once '/var/www/html/phpunit_josegitbook/src/Fixtures/clases.php';

class UserTest extends \PHPUnit\Framework\TestCase{

    public function setUp(){

        $this -> faker = Faker\Factory::create();
        $this -> faker -> addProvider(new Faker\Provider\Internet($this -> faker));

        $this -> user = new User($this -> faker->userName, $this -> faker->email);
        //print_r($this -> user);

    }

    public function testAddReview() {

        $this -> faker -> addProvider(new Faker\Provider\Lorem($this -> faker));

        $this -> assertEquals( 0, $this -> user -> countReviews() );

        for( $i = 0; $i < 10; $i++ ){

            $this->user->addReview(new Review($this -> faker->sentence, $this -> faker->paragraph));

        }

        $this -> assertEquals( 10, $this -> user -> countReviews() );

    }

    public function testAddReviewAlice() {

        $loader = new Nelmio\Alice\Loader\NativeLoader();
        $reviews = $loader -> loadFile(__DIR__.'/review.yml');

        foreach($reviews -> getObjects() as $review) {

            $this -> user -> addReview($review);

        }

        $this->assertEquals(10, $this->user->countReviews());
    }
}

?>