<?php
session_start();
include 'connexion_bdd.php';
if (!isset($_SESSION['user_id']) || !in_array('read_roles', $_SESSION['permissions'])) {
    header("HTTP/1.1 403 Forbidden");
    echo "Accès refusé";
    exit;
}
if (isset($_POST['create_role']) && in_array('write_roles', $_SESSION['permissions'])) {
    $name = $_POST['name'];
    $pdo->prepare("INSERT INTO roles (name) VALUES (?)")->execute([$name]);
    header("Location: roles.php");
    exit;
}
if (isset($_GET['delete']) && in_array('write_roles', $_SESSION['permissions'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM role_user WHERE role_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM roles WHERE id = ?")->execute([$id]);
    header("Location: roles.php");
    exit;
}
if (isset($_POST['update_role']) && in_array('write_roles', $_SESSION['permissions'])) {
    $id = $_POST['id'];
    $pdo->prepare("UPDATE roles SET name = ? WHERE id = ?")->execute([$_POST['name'], $id]);
    header("Location: roles.php");
    exit;
}
$roles = $pdo->query("SELECT id, name FROM roles")->fetchAll(PDO::FETCH_ASSOC);
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$create_id = isset($_GET['create']) ? true : false;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Rôles</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <p>Connecté en tant que <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Déconnexion</a></p>
        <h1>Rôles</h1>
        <a href="index.php"><button>Dashboard</button></a>
        <?php if (in_array('write_roles', $_SESSION['permissions'])): ?>
            <a href="roles.php?create"><button>Créer un rôle</button></a>
        <?php endif; ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <?php if (in_array('write_roles', $_SESSION['permissions'])): ?><th>Actions</th><?php endif; ?>
            </tr>
            <?php if ($create_id && in_array('write_roles', $_SESSION['permissions'])): ?>
                <tr>
                    <form method="POST">
                        <td></td>
                        <td><input type="text" name="name"></td>
                        <td><button type="submit" name="create_role">Créer</button></td>
                    </form>
                </tr>
            <?php endif; ?>
            <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?= $role['id'] ?></td>
                    <td><?= htmlspecialchars($role['name']) ?></td>
                    <?php if (in_array('write_roles', $_SESSION['permissions'])): ?>
                        <td>
                            <a href="roles.php?edit=<?= $role['id'] ?>"><button>Edit</button></a>
                            <a href="roles.php?delete=<?= $role['id'] ?>" onclick="return confirm('Supprimer ?');"><button>Supprimer</button></a>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php if ($edit_id === $role['id'] && in_array('write_roles', $_SESSION['permissions'])): ?>
                    <tr>
                        <form method="POST">
                            <td><?= $role['id'] ?></td>
                            <td><input type="text" name="name" value="<?= htmlspecialchars($role['name']) ?>"></td>
                            <td><button type="submit" name="update_role">Modifier</button></td>
                        </form>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </body>
</html>