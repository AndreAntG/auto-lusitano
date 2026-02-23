<?php
// auth.php - Authentication functions

require_once 'cnn.php';

// Login function
function login($username, $password) {
    global $pdo;

    try {
        // Get user from database
        $stmt = $pdo->prepare("SELECT id, username, password, email, role, status FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Utilizador não encontrado'];
        }

        if ($user['status'] != 1) {
            return ['success' => false, 'message' => 'Conta desativada'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            // Debug output
            echo "<script>console.log('Password verification failed');</script>";
            echo "<script>console.log('Input password: " . addslashes($password) . "');</script>";
            echo "<script>console.log('Stored hash: " . addslashes($user['password']) . "');</script>";
            echo "<script>console.log('Generated hash for input: " . addslashes(password_hash($password, PASSWORD_DEFAULT)) . "');</script>";
            echo "<script>alert('Debug Info:\\nInput: " . addslashes($password) . "\\nStored Hash: " . addslashes($user['password']) . "\\nGenerated Hash: " . addslashes(password_hash($password, PASSWORD_DEFAULT)) . "');</script>";
            return ['success' => false, 'message' => 'Palavra-passe incorreta'];
        }

        // Debug output for successful verification
        echo "<script>console.log('Password verification successful');</script>";
        echo "<script>console.log('Input password: " . addslashes($password) . "');</script>";
        echo "<script>console.log('Stored hash: " . addslashes($user['password']) . "');</script>";

        // Update last login
        $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        $updateStmt->execute([$user['id']]);

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        return ['success' => true, 'message' => 'Login realizado com sucesso'];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erro no login: ' . $e->getMessage()];
    }
}

// Check if username exists
function usernameExists($username) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Check if email exists
function emailExists($email) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Create new user (for future registration functionality)
function createUser($username, $password, $email, $role = 'user') {
    global $pdo;

    try {
        // Check if username or email already exists
        if (usernameExists($username)) {
            return ['success' => false, 'message' => 'Nome de utilizador já existe'];
        }

        if (emailExists($email)) {
            return ['success' => false, 'message' => 'Email já está registado'];
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $hashedPassword, $email, $role]);

        return ['success' => true, 'message' => 'Utilizador criado com sucesso'];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erro ao criar utilizador: ' . $e->getMessage()];
    }
}
?>