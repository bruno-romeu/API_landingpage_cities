<?php
    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/review.php';
    require_once __DIR__ . '/../dao/reviewDAO.php';
    require_once __DIR__ . '/../dao/userDAO.php';

    header('Access-control-allow-origin: *');
    header('Content-type: application/json; charset=utf-8');
    header('Access-Control-Allow-Methods: POST, OPTIONS'); 
    header('Access-Control-Allow-Headers: Content-Type, Authorization');

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $userDAO = new UserDAO($conn);
    
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $token = str_replace('Bearer ', '', $authHeader);

    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['message' => 'Acesso negado.']);
        exit;
    }

    $user = $userDAO->findByToken($token);
    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Token inválido ou expirado.']);
        exit;
    }

    $reviewDAO = new ReviewDAO($conn);
    $data = json_decode(file_get_contents('php://input'));

    $reviewId = $data->review_id ?? null;

    if (!$reviewId) {
        http_response_code(400);
        echo json_encode(['message' => 'ID da avaliação não fornecido.']);
        exit;
    }

    $review = $reviewDAO->findById($reviewId); 

    if ($review) {
        if ($review->users_id === $user->id) {
            $reviewDAO->destroy($reviewId);
            echo json_encode(['status' => 'success', 'message' => 'Avaliação excluída com sucesso!']);
        } else {
            http_response_code(403);
            echo json_encode(['message' => 'Você não tem permissão para excluir esta avaliação.']);
        }
    } else {
        http_response_code(404); 
        echo json_encode(['message' => 'Avaliação não encontrada.']);
    }
?>