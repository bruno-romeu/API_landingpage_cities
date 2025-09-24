<?php

require_once __DIR__ . '/../models/review.php';
require_once __DIR__ . '/userDAO.php';

    class ReviewDAO implements ReviewDAOInterface {

        private $conn;

        public function __construct(PDO $conn) {
            $this->conn = $conn;
        }

        public function buildReview($data) {

            $reviewObject = new Review();

            $reviewObject->id = $data->id; 
            $reviewObject->rating = $data->rating;
            $reviewObject->review = $data->review;
            $reviewObject->users_id = $data->users_id;
            $reviewObject->cities_id = $data->cities_id;

            return $reviewObject;

        }
        public function create(Review $review) {

            $stmt = $this->conn->prepare('INSERT INTO reviews(
                rating, review, cities_id, users_id) VALUES (
                :rating, :review, :cities_id, :users_id)');
    
                $stmt->bindParam(':rating', $review->rating);
                $stmt->bindParam(':review', $review->review);
                $stmt->bindParam(':cities_id', $review->cities_id);
                $stmt->bindParam(':users_id', $review->users_id);
    
                $stmt->execute();
                return true;

        }
        public function getcitiesReview($id) {
            $reviews = [];

            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE cities_id = :cities_id");

            $stmt->bindParam(":cities_id", $id);

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $reviewsData = $stmt->fetchAll();

                $userDAO = new UserDAO($this->conn, $this->url);

                foreach($reviewsData as $review) {

                    $reviewObject = $this->buildReview($review);

                    //chamar dados do user
                    $user = $userDAO->findById($reviewObject->users_id);
                    $reviewObject->user = $user;

                    $reviews[] = $reviewObject;
                }
            } 
            return $reviews;

        }
        public function hasAlreadyReviwed($id, $userId) {

            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE cities_id = :cities_id AND users_id = :users_id");

            $stmt->bindParam(":cities_id", $id);
            $stmt->bindParam(":users_id", $userId);

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                return true;
            } else{
                return false;
            }

        }
        public function getRating($id) {

            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE cities_id = :cities_id");

            $stmt->bindParam("cities_id", $id);

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
            $soma_ratings = 0;
            $reviews = $stmt->fetchAll();

            foreach ($reviews as $review) {
                $soma_ratings += $review->rating; 
            }

            if (count($reviews) > 0) {
                $media = $soma_ratings / count($reviews);
            } else {
                $media = 0;
            }

            return $media;

        } else {
            return 0; 
        }
    
        }  
    }