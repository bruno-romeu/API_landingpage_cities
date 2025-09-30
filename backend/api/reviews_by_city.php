<?php
    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/review.php';
    require_once __DIR__ . '/../dao/reviewDAO.php';

    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');

    $reviewDAO = new ReviewDAO($conn);

    $cityId = $_GET['city_id'] ?? null;

    if (!$cityId) {
        http_response_code(400);
        echo json_encode(['message' => 'ID da cidade não fornecido.']);
        exit;
    }

    $reviews = $reviewDAO->findByCityId($cityId);

    echo json_encode($reviews);
?>