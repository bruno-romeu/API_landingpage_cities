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

        public function findByCityId($cityId) {
            $reviews = [];

            $stmt = $this->conn->prepare("
                SELECT r.*, u.name, u.lastname 
                FROM reviews r
                INNER JOIN users u ON r.users_id = u.id
                WHERE r.cities_id = :cities_id
                ORDER BY r.id DESC
            ");

            $stmt->bindParam(":cities_id", $cityId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $reviewsData = $stmt->fetchAll();

                foreach ($reviewsData as $review) {
                    $reviewObject = new Review();
                    $reviewObject->id = $review->id;
                    $reviewObject->rating = $review->rating;
                    $reviewObject->review = $review->review;
                    $reviewObject->cities_id = $review->cities_id;
                    $reviewObject->users_id = $review->users_id;

                    $reviewObject->userName = $review->name . " " . $review->lastname;
                    
                    $reviews[] = $reviewObject;
                }
            }

            return $reviews;
        }


        public function getLatestReviews($limit = 5) {
            $reviews = [];

            $stmt = $this->conn->prepare("
                SELECT 
                    r.rating, 
                    r.review,
                    u.name AS user_name,
                    u.lastname AS user_lastname,
                    c.name AS city_name
                FROM reviews r
                INNER JOIN users u ON r.users_id = u.id
                INNER JOIN cities c ON r.cities_id = c.id
                ORDER BY r.id DESC
                LIMIT :limit
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $reviews = $stmt->fetchAll();
            }

            return $reviews;
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