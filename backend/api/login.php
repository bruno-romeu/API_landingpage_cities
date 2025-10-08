<?php

    require_once __DIR__ . '/../db.php'; 
    require_once __DIR__ . '/../models/user.php';
    require_once __DIR__ . '/../dao/userDAO.php';

    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    $userDAO = new UserDAO($conn);

    $response = [
        'status' => 'error',
        'message' => 'Ocorreu um erro.'
    ];

    $data = json_decode(file_get_contents('php://input'));


    if (
        !isset($data->email) || 
        !isset($data->password)
    ) {
        $response['message'] = 'Por favor, preencha todos os campos obrigatórios.';
        http_response_code(400); 
        echo json_encode($response);
        exit; 
    }

    $token = $userDAO->authenticateUser($data->email, $data->password);

    if ($token) {
        $response = [
            'status' => 'success',
            'message' => 'Login realizado com sucesso!',
            'token' => $token 
        ];
        http_response_code(200); 
        echo json_encode($response);
        exit;
    } else {
        http_response_code(401);
        $response['message'] = 'Usuário e/ou senha incorretos.';
    }

    

    echo json_encode($response);

?>