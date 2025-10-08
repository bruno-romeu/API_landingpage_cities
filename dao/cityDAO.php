<?php

    require_once __DIR__ . '/../models/city.php';
    require_once __DIR__ . '/reviewDAO.php';


    class CityDAO implements CityDAOInterface {

        private $conn;

        public function __construct(PDO $conn) {
            $this->conn = $conn;
        }

        public function buildCity($data){
            $city = new City();

            $city->id = $data->id;
            $city->name = $data->name;
            $city->description = $data->description;
            $city->image = $data->image;
            $city->users_id = $data->users_id;

            $reviewDAO = new ReviewDAO($this->conn);
            $rating = $reviewDAO->getRating($city->id);
            $city->rating = $rating;

            return $city;
        }
        public function findAll(){

            $cities = [];

            $stmt = $this->conn->query("SELECT * FROM cities ORDER BY name ASC");

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $citiesArray = $stmt->fetchAll();

                foreach($citiesArray as $city) {
                    $cities[] = $this->buildCity($city);
                }
            }

            return $cities;

        }
        public function getLatestCities(){

            $cities = [];

            $stmt = $this->conn->query("SELECT * FROM cities ORDER BY id DESC");

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $citiesArray = $stmt->fetchAll();

                foreach($citiesArray as $city) {
                    $cities[] = $this->buildCity($city);
                }
            }

            return $cities;

        }
    
        public function getCitiesByUserId($id){

            $cities = [];

            $stmt = $this->conn->prepare("SELECT * FROM cities
            WHERE users_id = :users_id");

            $stmt->bindParam(":users_id", $id);

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $citiesArray = $stmt->fetchAll();

                foreach($citiesArray as $city) {
                    $cities[] = $this->buildCity($city);
                }
            }

            return $cities;

        }
        public function findById($id){

            $city = [];

            $stmt = $this->conn->prepare("SELECT * FROM cities
            WHERE id = :id");

            $stmt->bindParam(":id", $id);

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $cityData = $stmt->fetch();

                $city = $this->buildCity($cityData);

                return $city;
            } else {
                return false;
            }
        }
        public function findByName($name){

            $cities = [];

            $name = strtolower($name);

            $stmt = $this->conn->prepare("SELECT * FROM cities
            WHERE LOWER(name) LIKE :name");

            $stmt->bindValue(":name", "%".$name."%");

            $stmt->execute();

            if($stmt->rowCount() > 0) {
                $citiesArray = $stmt->fetchAll();

                foreach($citiesArray as $city) {
                    $cities[] = $this->buildCity($city);
                }
            }

            return $cities;

        }
        public function create(City $city){

            $stmt = $this->conn->prepare("INSERT INTO cities (
            name, description, image, tourist_attractions, users_id) VALUES (
            :name, :description, :image, :tourist_attractions, :users_id)");

            $stmt->bindParam(":name", $city->name);
            $stmt->bindParam(":description", $city->description);
            $stmt->bindParam(":image", $city->image);
            $stmt->bindParam(":tourist_attractions", $city->tourist_attractions);
            $stmt->bindParam(":users_id", $city->users_id);

            $stmt->execute();

            return true;


        }
        public function update(City $city){

            $stmt = $this->conn->prepare("UPDATE cities SET 
            name = :name,
            description = :description,
            image = :image,
            tourist_attractions = :tourist_attractions
            WHERE id = :id
            ");

            $stmt->bindParam(":name", $city->name);
            $stmt->bindParam(":description", $city->description);
            $stmt->bindParam(":image", $city->image);
            $stmt->bindParam(":tourist_attractions", $city->tourist_attractions);
            $stmt->bindParam(":id", $city->id);


            $stmt->execute();

            return true;

        }
        public function destroy($id){

            $stmt = $this->conn->prepare("DELETE FROM cities WHERE id = :id");

            $stmt->bindParam(":id", $id);

            $stmt->execute();

            return true;

        }

    }