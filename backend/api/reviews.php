<?php

    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/review.php';
    require_once __DIR__ . '/../dao/reviewDAO.php';

    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: POST, OPTIONS'); 

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $reviewDAO = new ReviewDAO($conn);

    $response = [
        'status' => 'error',
        'message' => 'Ocorreu um erro ao processar sua solicitação.'
    ];

    $data = json_decode(file_get_contents('php://input'));

    if (
        !isset($data->rating) || 
        !isset($data->review) || 
        !isset($data->cities_id) || 
        !isset($data->users_id)
    ) {
        $response['message'] = 'Dados incompletos. Todos os campos são obrigatórios.';
        http_response_code(400); 
        echo json_encode($response);
        exit; 
    }

    try {
        $review = new Review();

        $review->rating = $data->rating;
        $review->review = $data->review;
        $review->cities_id = $data->cities_id;
        $review->users_id = $data->users_id; 

        if ($reviewDAO->create($review)) {
            $response = [
                'status' => 'success',
                'message' => 'Avaliação enviada com sucesso!'
            ];
            http_response_code(201); 
        } else {
            $response['message'] = 'Não foi possível salvar a avaliação no banco de dados.';
            http_response_code(500); 
        }

    } catch (Exception $e) {
        $response['message'] = 'Erro interno no servidor: ' . $e->getMessage();
        http_response_code(500);
    }

    echo json_encode($response);