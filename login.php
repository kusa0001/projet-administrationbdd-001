<?php
session_start();
include 'connexion_bdd.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        $stmt_perm = $pdo->prepare("
            SELECT p.name 
            FROM permissions p
            JOIN permission_role pr ON pr.permission_id = p.id
            JOIN role_user ru ON ru.role_id = pr.role_id
            WHERE ru.user_id = ?
        ");
        $stmt_perm->execute([$user['id']]);
        $permissions = $stmt_perm->fetchAll(PDO::FETCH_COLUMN);
        $_SESSION['permissions'] = $permissions;

        header("Location: index.php");
        exit;
    } else {
        $error = "Identifiant ou mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Connexion</h1>
<?php if ($error): ?>
<p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="POST" action="login.php">
    Nom d'utilisateur: <input type="text" name="username" required><br><br>
    Mot de passe: <input type="password" name="password" required><br><br>
    <button type="submit" name="login">Se connecter</button>
</form>
</body>
</html>