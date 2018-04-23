<?php

    // Para gestionar el acceso a base de datos hemos creado las clases UserRepository y PhotoRepository, de forma que sean estas clases las responsables de ejecutar las consultas.

    class UserRepository{

        protected $db;

        public function __construct(PDO $db)
        {
            $this->db = $db;
        }

        public function storeUser(Usuario $user){

            //print_r($user);
            $sql = 'INSERT INTO User(username, email) VALUES (:username, :email)';
            $stm = $this->db->prepare($sql);
            $stm->execute(array(':username' => $user->username, ':email' => $user->email));

            $userId = $this -> db -> lastInsertId();
            $error = $stm -> errorInfo();
            //$error = mysqli_error($this -> db);

            if (!$userId) {
                throw new Exception('User not saved: ' . implode(', ',$error));
            }

            $user->id = $userId;

            if (count($user->getPhotos()>0)) {
                $photoRepository = new PhotoRepository($this->db);
                foreach($user->getPhotos() as $photo) {
                    $photoRepository->storePhoto($photo, $user);
                }

            }

            return $user;
        }

        public function removeUser(Usuario $user) {
            $sql = 'DELETE FROM User where id=:id'; 
            $stm = $this->db->prepare($sql);
            $stm->execute(Array(':id' => $user->id));

        }

        public function getById($id)
        {
            $sql = 'SELECT * FROM User where id = :user_id';
            $stm = $this->db->prepare($sql);
            $stm->execute(array(':user_id' => $id));
            $stm->setFetchMode(PDO::FETCH_CLASS, 'Usuario');
            $user = $stm->fetch();

            $sql = 'SELECT * FROM Photo where user_id = :user_id';
            $stm = $this->db->prepare($sql);
            $stm->execute(array(':user_id' => $id));
            $stm->setFetchMode(PDO::FETCH_CLASS, 'Photo');
            $photos = $stm->fetchAll();
            if(is_array($photos) && count($photos)>0) {
                $user->addPhotos($photos);
            }

            return $user;
        }
    }


    class PhotoRepository {

        protected $db;

        public function __construct($db) {
            $this->db = $db;
        }

        public function storePhoto(Photo $photo, Usuario $user)
        {
            $sql = 'INSERT INTO Photo(user_id, url_photo) VALUES (:user_id, :url_photo)';
            $stm = $this->db->prepare($sql);
            $stm->execute(array(':user_id' => $user->id, ':url_photo' => $photo->url_photo));
            $photoId = $this->db->lastInsertId();
            if (!$photoId) {
                throw new Exception('Photo not saved');
            }
            $photo->id = $photoId;

            return $photo;
        }
    }

?>