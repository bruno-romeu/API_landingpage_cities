<?php
    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/review.php';
    require_once __DIR__ . '/../dao/reviewDAO.php';

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');

    $reviewDAO = new ReviewDAO($conn);

    $latestReviews = $reviewDAO->getLatestReviews(5);

    echo json_encode($latestReviews);
?>