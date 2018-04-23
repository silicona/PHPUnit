<?php

//namespace App\Fixtures\clases;

class User {

    protected $username;
    protected $email;
    protected $reviews = Array();

    public function __construct($username, $email) {
        $this -> username = $username;
        $this -> email = $email;
    }

    public function addReview(Review $review) {
        $this -> reviews[] = $review;
    }

    public function countReviews() {
        return count($this -> reviews);
    }

    public function seeReviews() {
        return $this -> reviews;
    }
}

class Review {

    protected $title;
    protected $description;

    public function __construct($title, $description) {
        $this -> title = $title;
        $this -> description = $description;
    }
}

?>