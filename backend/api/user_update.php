<?php
    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/user.php';
    require_once __DIR__ . '/../dao/userDAO.php';

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');
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
        echo json_encode(['message' => 'Acesso negado. Token não fornecido.']);
        exit;
    }

    $user = $userDAO->findByToken($token);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['message' => 'Token inválido ou expirado.']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'));

    if (isset($data->name)) {
        $user->name = $data->name;
    }
    if (isset($data->lastname)) {
        $user->lastname = $data->lastname;
    }
    if (isset($data->email)) {
        if ($userDAO->findByEmail($data->email) && $data->email !== $user->email) {
            http_response_code(409); 
            echo json_encode(['message' => 'Este e-mail já está em uso por outra conta.']);
            exit;
        }
        $user->email = $data->email;
    }
    if (isset($data->bio)) {
        $user->bio = $data->bio;
    }

    try {
        $userDAO->update($user);
        
        $updatedUserData = [
            'name' => $user->name,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'bio' => $user->bio
        ];

        echo json_encode([
            'status' => 'success',
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $updatedUserData
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Erro ao atualizar o perfil: ' . $e->getMessage()]);
    }
?>