<?php

require_once __DIR__ . '/../models/user.php';

    class UserDAO implements UserDAOInterface{

        private $conn;

        public function __construct(PDO $conn) {
            $this->conn = $conn;
        }

        public function buildUser($data) {

            $user = new User();

            $user->id = $data['id'];
            $user->name = $data['name'];
            $user->lastname = $data['lastname'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->image = $data['image'];
            $user->bio = $data['bio'];
            $user->token = $data['token'];

            return $user;

        }
        public function create(User $user, $authUser = false) {

            $stmt = $this->conn->prepare('INSERT INTO users(
            name, lastname, email, password, token) VALUES (
            :name, :lastname, :email, :password, :token)');

            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':lastname', $user->lastname);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':password', $user->password);
            $stmt->bindParam(':token', $user->token);

            $stmt->execute();

            //autenticar usuário caso auth seja true
            if($authUser) {
                $this->setTokenToSession($user->token);
            }

        }
        public function update(User $user) {

            $stmt = $this->conn->prepare('UPDATE users SET 
            name = :name,
            lastname = :lastname,
            email = :email,
            image = :image,
            bio = :bio,
            token = :token
            WHERE id = :id');

            $stmt->bindParam(':name', $user->name);
            $stmt->bindParam(':lastname', $user->lastname);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':image', $user->image);
            $stmt->bindParam(':bio', $user->bio);
            $stmt->bindParam(':token', $user->token);
            $stmt->bindParam(':id', $user->id);

            $stmt->execute();

            return true;

        }
        public function verifyToken($protected = false) {

            if(!empty($_SESSION['token'])) {

                //pega o token da seção
                $token = $_SESSION['token'];

                //verifica se o user existe através do token
                $user = $this->findByToken($token);
                if($user) {
                    return $user;
                } else if($protected) {
                    return false;
                }
            } else if($protected) {
                return false;
            }

        }
        public function setTokenToSession($token) {

            //salvar token na seção
            $_SESSION['token'] = $token;


        }
        public function authenticateUser($email, $password) {

            $user = $this->findByEmail($email);

            if($user) {

                //checar se as senhas são identicas
                if(password_verify($password, $user->password)) {

                    $token = $user->generateToken();

                    $this->setTokenToSession($token, false);

                    //atualizar o token do usuário
                    $user->token = $token;
                    $this->update($user, false);

                    return true;

                } else {
                    return false;
                }

            } else {
                return false;
            }

        }
        public function findByEmail($email) {

            if($email != '') {
                $stmt = $this->conn->prepare('SELECT * FROM users WHERE email = :email');

                $stmt->bindParam(':email', $email);

                $stmt->execute();

                if($stmt->rowCount() > 0) {

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;

                } else {
                    return false;
                }
            } else {
                return false;
            }

        }
        public function findById($id) {

            if($id != '') {
                $stmt = $this->conn->prepare('SELECT * FROM users WHERE id = :id');

                $stmt->bindParam(':id', $id);

                $stmt->execute();

                if($stmt->rowCount() > 0) {

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;

                } else {
                    return false;
                }
            } else {
                return false;
            }


        }
        public function findByToken($token) {

            if($token != '') {
                $stmt = $this->conn->prepare('SELECT * FROM users WHERE token = :token');

                $stmt->bindParam(':token', $token);

                $stmt->execute();

                if($stmt->rowCount() > 0) {

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;

                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        public function destroyToken() {

            //remove o token da seção
            $_SESSION['token'] = '';

            return true;

        }

        public function changePassword(User $user) {

            $stmt = $this->conn->prepare("UPDATE users SET password = :password
            WHERE id = :id");

            $stmt->bindParam(":password", $user->password);
            $stmt->bindParam(":id", $user->id);

            $stmt->execute();

            return true;

        }
    }