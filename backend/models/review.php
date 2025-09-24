<?php

    class Review{

        public $id;
        public $rating;
        public $review;
        public $users_id;
        public $cities_id;
    }

    interface ReviewDAOInterface {

        public function buildReview($data);
        public function create(Review $review);
        public function getCitiesReview($id);
        public function hasAlreadyReviwed($id, $userId);
        public function getRating($id);

    }