<?php
    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/user.php';
    require_once __DIR__ . '/../dao/userDAO.php';

    // Headers
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
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
        echo json_encode(['message' => 'Token não fornecido.']);
        exit;
    }

    $user = $userDAO->findByToken($token);

    if ($user) {
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'bio' => $user->bio,
            'image' => $user->image
        ];
        echo json_encode($userData);
    } else {
        http_response_code(401); 
        echo json_encode(['message' => 'Token inválido ou expirado.']);
    }
?>