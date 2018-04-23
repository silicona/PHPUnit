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

class Cosa {

    public $id;
    public $nombre;
    public $email;
    public $medallas;
    public $anexo;
    public $usuario;
    public $favoriteNumber;
    public $fullname;
    public $birthDate;
    public $friends;

    // public function __construct($nombre){
    //     $this -> nombre = $nombre;
    // }

    public function getEmail($email) {

        $this -> email = $email;
    }
}

?>