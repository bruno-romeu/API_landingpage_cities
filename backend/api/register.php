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
        !isset($data->name) || !isset($data->lastname) || !isset($data->email) || 
        !isset($data->password) || !isset($data->confirmpassword)
    ) {
        $response['message'] = 'Por favor, preencha todos os campos obrigatórios.';
        http_response_code(400); 
        echo json_encode($response);
        exit; 
    }

    if ($data->password !== $data->confirmpassword) {
        $response['message'] = 'As senhas devem ser iguais.';
        http_response_code(400);
        echo json_encode($response);
        exit;
    }

    if ($userDAO->findByEmail($data->email) !== false) {
        $response['message'] = 'Usuário já cadastrado, use outro email.';
        http_response_code(409); 
        echo json_encode($response);
        exit;
    }

    try {
        $user = new User();

        $userToken = $user->generateToken();
        $finalPassword = $user->generatePassword($data->password);

        $user->name = $data->name;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $finalPassword;
        $user->token = $userToken;
        
        $success = $userDAO->create($user);

        if ($success) {
            $response = [
                'status' => 'success',
                'message' => 'Usuário cadastrado com sucesso!',
                'token' => $userToken 
            ];
            http_response_code(201); 
        }

    } catch (Exception $e) {
        $response['message'] = 'Erro interno ao criar usuário: ' . $e->getMessage();
        http_response_code(500); 
    }

    echo json_encode($response);

?>