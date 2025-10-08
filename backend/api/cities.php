<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/city.php';
    require_once __DIR__ . '/../dao/cityDAO.php';

    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');


    $cityDAO = new CityDAO($conn);

    $cities = $cityDAO->findAll();

    if (!is_array($cities)) {
        $cities = [];
    }


    $response = [
        'status' => 'success',
        'data' => $cities
    ];

    echo json_encode($response);

?>