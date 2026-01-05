<?php

class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function register(array $data): bool
    {
        if ($this->findByEmail($data['email'])) {
            throw new Exception("Email already exists");
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, password, full_name, role)
                VALUES (:email, :password, :full_name, :role)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'email' => $data['email'],
            'password' => $hashedPassword,
            'full_name' => $data['full_name'],
            'role' => $data['role']
        ]);
    }

    public function login(string $email, string $password): array
    {
        $user = $this->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            throw new Exception("Invalid credentials");
        }

        if (!$user['is_active']) {
            throw new Exception("Account disabled");
        }

        return $user;
    }

    public function isAdmin(array $user): bool
    {
        return $user['role'] === 'admin';
    }

    public function isHost(array $user): bool
    {
        return $user['role'] === 'host';
    }

    public function isTraveler(array $user): bool
    {
        return $user['role'] === 'traveler';
    }
}
