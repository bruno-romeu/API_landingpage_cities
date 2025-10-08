<?php

    class City {
        
        public $id;
        public $name;
        public $description;
        public $image;
        public $tourist_attractions;
        public $users_id;
        public $rating;

        public function imageGenerateName() {
            return bin2hex(random_bytes(60)) . '.jpg';
        } 

    }

    interface CityDAOInterface {

        public function buildCity($data);
        public function findAll();
        public function getLatestCities();
        public function getCitiesByUserId($id);
        public function findById($id);
        public function findByName($name);
        public function create(City $city);
        public function update(City $city);
        public function destroy($id);


    }