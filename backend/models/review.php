<?php

    class Review{

        public $id;
        public $rating;
        public $review;
        public $users_id;
        public $cities_id;
        public $userName;
    }

    interface ReviewDAOInterface {

        public function buildReview($data);
        public function create(Review $review);
        public function findByCityId($cityId);
        public function getLatestReviews($limit);
        public function getRating($id);

    }