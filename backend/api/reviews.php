<?php

    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/review.php';
    require_once __DIR__ . '/../dao/reviewDAO.php';
    require_once __DIR__ . '/../dao/userDAO.php';

    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Methods: POST, OPTIONS'); 

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $userDAO = new UserDAO($conn);

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['message' => 'Acesso negado. Faça login para avaliar.']);
        exit;
    }

    $user = $userDAO->findByToken($token);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Token inválido ou expirado.']);
        exit;
    }

    $reviewDAO = new ReviewDAO($conn);


    $response = [
        'status' => 'error',
        'message' => 'Ocorreu um erro ao processar sua solicitação.'
    ];

    $data = json_decode(file_get_contents('php://input'));

    if (empty($data->rating) || empty($data->review) || empty($data->cities_id)) {
        http_response_code(400);
        echo json_encode(['message' => 'Dados incompletos. Avaliação, nota e cidade são obrigatórios.']);
        exit;
    }

    try {
        $review = new Review();

        $review->rating = $data->rating;
        $review->review = $data->review;
        $review->cities_id = $data->cities_id;
        $review->users_id = $user->id; 

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