<?php
session_start();
include 'connexion_bdd.php';
include 'logger.php';
if (!isset($_SESSION['user_id']) || !in_array('read_user', $_SESSION['permissions'])) {
    header("HTTP/1.1 403 Forbidden");
    echo "Accès refusé";
    exit;
}
$roles_stmt = $pdo->query("SELECT id, name FROM roles");
$roles = $roles_stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_POST['create_user']) && in_array('write_user', $_SESSION['permissions'])) {
    $username = $_POST['username'];
    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$username, $password_hash, $email]);
    $user_id = $pdo->lastInsertId();
    if (!empty($_POST['roles'])) {
        $stmt_role = $pdo->prepare("INSERT INTO role_user (user_id, role_id) VALUES (?, ?)");
        foreach ($_POST['roles'] as $role_id) {
            $stmt_role->execute([$user_id, $role_id]);
        }
    }
    log_action($_SESSION['user_id'], "CREATE_USER", "user:$user_id", "success");
    header("Location: users.php");
    exit;
}
if (isset($_GET['delete']) && in_array('write_user', $_SESSION['permissions'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM role_user WHERE user_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    log_action($_SESSION['user_id'], "DELETE_USER", "user:$id", "success");
    header("Location: users.php");
    exit;
}
if (isset($_POST['update_user']) && in_array('write_user', $_SESSION['permissions'])) {
    $id = $_POST['id'];
    $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?")
        ->execute([$_POST['username'], $_POST['email'], $id]);
    $pdo->prepare("DELETE FROM role_user WHERE user_id = ?")->execute([$id]);
    if (!empty($_POST['roles'])) {
        $stmt_role = $pdo->prepare("INSERT INTO role_user (user_id, role_id) VALUES (?, ?)");
        foreach ($_POST['roles'] as $role_id) $stmt_role->execute([$id, $role_id]);
    }
    log_action($_SESSION['user_id'], "UPDATE_USER", "user:$id", "success");
    header("Location: users.php");
    exit;
}
$users = $pdo->query("SELECT id, username, email, created_at FROM users")->fetchAll(PDO::FETCH_ASSOC);
$user_roles_raw = $pdo->query("SELECT user_id, role_id FROM role_user")->fetchAll(PDO::FETCH_ASSOC);
$user_roles = [];
foreach ($user_roles_raw as $ur) $user_roles[$ur['user_id']][] = $ur['role_id'];
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$create_id = isset($_GET['create']) ? true : false;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Utilisateurs</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <p>Connecté en tant que <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Déconnexion</a></p>
        <h1>Utilisateurs</h1>
        <a href="index.php"><button>Dashboard</button></a>
        <?php if (in_array('write_user', $_SESSION['permissions'])): ?>
            <a href="users.php?create"><button>Créer un utilisateur</button></a>
        <?php endif; ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Créé le</th>
                <th>Rôles</th>
                <?php if (in_array('write_user', $_SESSION['permissions'])): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
            <?php if ($create_id && in_array('write_user', $_SESSION['permissions'])): ?>
                <tr>
                    <form method="POST">
                        <td></td>
                        <td><input type="text" name="username"></td>
                        <td><input type="email" name="email"></td>
                        <td></td>
                        <td>
                            <select name="roles[]" multiple>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <input type="password" name="password" placeholder="Mot de passe">
                            <button type="submit" name="create_user">Créer</button>
                        </td>
                    </form>
                </tr>
            <?php endif; ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= $user['created_at'] ?></td>
                    <td>
                        <?php
                            if (!empty($user_roles[$user['id']])) {
                                $role_names = [];
                                foreach ($roles as $role) if (in_array($role['id'], $user_roles[$user['id']])) $role_names[] = htmlspecialchars($role['name']);
                                echo implode(", ", $role_names);
                            } else echo "-";
                        ?>
                    </td>
                    <?php if (in_array('write_user', $_SESSION['permissions'])): ?>
                        <td>
                            <a href="users.php?edit=<?= $user['id'] ?>"><button>Edit</button></a>
                            <a href="users.php?delete=<?= $user['id'] ?>" onclick="return confirm('Supprimer ?');"><button>Supprimer</button></a>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php if ($edit_id === $user['id'] && in_array('write_user', $_SESSION['permissions'])): ?>
                    <tr>
                        <form method="POST">
                            <td><?= $user['id'] ?></td>
                            <td><input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>"></td>
                            <td><input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td>
                            <td></td>
                            <td>
                                <select name="roles[]" multiple>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?= $role['id'] ?>" <?= (!empty($user_roles[$user['id']]) && in_array($role['id'], $user_roles[$user['id']])) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($role['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><button type="submit" name="update_user">Modifier</button></td>
                        </form>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </body>
</html>