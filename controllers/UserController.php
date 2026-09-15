<?php

require_once("models/User.php");

class UserController {

    static function signin() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $email === '' || $password === '') {
                $error = "Tous les champs sont obligatoires.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "L'adresse e-mail n'est pas valide.";
            } elseif (User::getByUsername($username)) {
                $error = "Ce nom d'utilisateur est déjà pris.";
            } elseif (User::getByEmail($email)) {
                $error = "L'adresse e-mail existe déjà.";
            } else {
                $error = User::validatePassword($password, $username);

                if ($error === null) {
                    User::create($username, $email, $password);
                    setFlash("Inscription réussie, vous pouvez vous connecter.");
                    header('Location: index.php?action=User/login');
                    exit();
                }
            }
        }

        require_once('views/user/signin.php');
    }

    static function login() {
        $error = null;
        $message = consumeFlash();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = User::getByEmail($email);

            if ($user && password_verify($password, $user->getPassword())) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['email'] = $user->getEmail();

                header('Location: index.php?action=Media/library');
                exit();
            }

            $error = "Identifiants incorrects. Veuillez réessayer.";
        }

        require_once('views/user/login.php');
    }

    static function logout() {
        unset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['email']);

        setFlash("Vous avez été déconnecté.");
        header('Location: index.php?action=User/login');
        exit();
    }
}
