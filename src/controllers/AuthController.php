<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new User($this->db);
    }

    public function register($username, $email, $password, $is_admin = false) {
        // Validação básica
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception("Todos os campos são obrigatórios");
        }

        // Verifica se usuário já existe
        if ($this->userModel->findByUsername($username)) {
            throw new Exception("Nome de usuário já está em uso");
        }

        // Verifica se email já existe
        if ($this->userModel->findByEmail($email)) {
            throw new Exception("Email já está em uso");
        }

        // Cria hash da senha
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Prepara o usuário
        $this->userModel->username = $username;
        $this->userModel->email = $email;
        $this->userModel->password_hash = $password_hash;
        $this->userModel->is_admin = $is_admin;

        // Tenta criar o usuário
        if ($this->userModel->create()) {
            return $this->userModel->id;
        } else {
            throw new Exception("Erro ao registrar usuário");
        }
    }

    public function login($username, $password) {
        // Busca o usuário
        $user = $this->userModel->findByUsername($username);
        if (!$user) {
            // Tenta buscar por email
            $user = $this->userModel->findByEmail($username);
            if (!$user) {
                throw new Exception("Usuário não encontrado");
            }
        }

        // Verifica a senha
        if (!password_verify($password, $user['password_hash'])) {
            throw new Exception("Senha incorreta");
        }

        // Retorna os dados do usuário (sem a senha)
        unset($user['password_hash']);
        return $user;
    }

    public function getUserById($id) {
        return $this->userModel->getById($id);
    }

    public function getAllUsers() {
        $stmt = $this->userModel->getAllUsers();
        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }
        return $users;
    }
}