<?php

require_once("includes/db_connect.php");

/**
 * Class User
 * Permet de créer un nouvel utilisateur et de gérer son authentification.
 */
class User {
    /** @var int Identifiant en base de l'utilisateur. */
    private int $id;

    /** @var string Nom d'utilisateur, unique. */
    private string $username;

    /** @var string Adresse email, unique. */
    private string $email;

    /** @var string Hash du mot de passe (jamais le mot de passe en clair). */
    private string $password;

    /** @var string Date de création du compte (format Y-m-d H:i:s). */
    private string $createdAt;

    /** @var string Date de dernière mise à jour du compte (format Y-m-d H:i:s). */
    private string $updatedAt;

    /**
     * @param int $id Identifiant en base.
     * @param string $username Nom d'utilisateur.
     * @param string $email Adresse email.
     * @param string $password Hash du mot de passe.
     * @param string $createdAt Date de création (format Y-m-d H:i:s).
     * @param string $updatedAt Date de dernière mise à jour (format Y-m-d H:i:s).
     */
    public function __construct(int $id, string $username, string $email, string $password, string $createdAt, string $updatedAt) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int L'identifiant de l'utilisateur.
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @return string Le nom d'utilisateur.
     */
    public function getUsername(): string {
        return $this->username;
    }

    /**
     * @return string L'adresse email.
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * Retourne le hash du mot de passe (jamais le mot de passe en clair).
     * @return string Le hash du mot de passe.
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @return string La date de création du compte.
     */
    public function getCreatedAt(): string {
        return $this->createdAt;
    }

    /**
     * @return string La date de dernière mise à jour du compte.
     */
    public function getUpdatedAt(): string {
        return $this->updatedAt;
    }

    /**
     * Vérifie que le mot de passe respecte la règle de sécurité :
     * 8 caractères minimum, une majuscule, une minuscule, un chiffre,
     * un caractère spécial, et qu'il ne contient pas l'identifiant de l'utilisateur.
     * @param string $password Le mot de passe à valider.
     * @param string $username Le nom d'utilisateur, qui ne doit pas apparaître dans le mot de passe.
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

    /**
     * @param string $email Adresse email recherchée.
     * @return User|null L'utilisateur trouvé, ou null s'il n'existe pas.
     */
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

    /**
     * @param string $username Nom d'utilisateur recherché.
     * @return User|null L'utilisateur trouvé, ou null s'il n'existe pas.
     */
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
     * @param string $username Nom d'utilisateur.
     * @param string $email Adresse email.
     * @param string $password Mot de passe en clair, haché avant persistance.
     * @return User L'utilisateur créé.
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

    /**
     * @param array $row Ligne issue de la table users.
     * @return User L'instance correspondante.
     */
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
