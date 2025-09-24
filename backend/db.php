<?php
  $db_driver = "mysql";
  $host = "localhost";
  $dbname = "nome_do_seu_banco";
  $user = "root";
  $pass = "";

  try {

    $conn = new PDO("{$db_driver}:host={$host};dbname={$dbname}", $user, $pass);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    $conn->exec("SET NAMES 'utf8'");

  } catch (PDOException $e) {

    http_response_code(500); 
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()
    ]);
    die(); 

  }

?>