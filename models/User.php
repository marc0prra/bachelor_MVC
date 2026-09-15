<?php

require_once("includes/db_connect.php");

/**
 * Class User
 * Permet de créer un nouvel utilisateur et de gérer son authentification.
 */
class User {
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(int $id, string $username, string $email, string $password, string $createdAt, string $updatedAt) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Retourne le hash du mot de passe (jamais le mot de passe en clair).
     */
    public function getPassword(): string {
        return $this->password;
    }

    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    /**
     * Vérifie que le mot de passe respecte la règle de sécurité :
     * 8 caractères minimum, une majuscule, une minuscule, un chiffre,
     * un caractère spécial, et qu'il ne contient pas l'identifiant de l'utilisateur.
     * @return string|null Un message d'erreur, ou null si le mot de passe est valide.
     */
    public static function validatePassword(string $password, string $username): ?string {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/';

        if (!preg_match($regex, $password)) {
            return "Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }

        if ($username !== '' && stripos($password, $username) !== false) {
            return "Le mot de passe ne doit pas contenir l'identifiant.";
        }

        return null;
    }

    public static function getByEmail(string $email): ?User {
        try {
            $db = connection();
            $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? self::hydrate($row) : null;
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    public static function getByUsername(string $username): ?User {
        try {
            $db = connection();
            $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ? self::hydrate($row) : null;
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    /**
     * Crée un nouvel utilisateur, avec un mot de passe haché.
     */
    public static function create(string $username, string $email, string $password): User {
        try {
            $db = connection();
            $stmt = $db->prepare('INSERT INTO users (username, email, password, created_at, updated_at)
                VALUES (:username, :email, :password, NOW(), NOW())');

            $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

            $stmt->bindValue(':username', $username, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->execute();

            $id = (int) $db->lastInsertId();
            $now = date('Y-m-d H:i:s');

            return new User($id, $username, $email, $hashedPassword, $now, $now);
        } catch (PDOException $e) {
            die('Erreur de requête : ' . $e->getMessage());
        }
    }

    private static function hydrate(array $row): User {
        return new User(
            (int) $row['id'],
            $row['username'],
            $row['email'],
            $row['password'],
            $row['created_at'],
            $row['updated_at']
        );
    }
}
