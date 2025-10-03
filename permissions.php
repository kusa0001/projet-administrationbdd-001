<?php
session_start();
include 'connexion_bdd.php';
if (!isset($_SESSION['user_id']) || !in_array('read_permissions', $_SESSION['permissions'])) {
    header("HTTP/1.1 403 Forbidden");
    echo "Accès refusé";
    exit;
}
if (isset($_POST['create_permission']) && in_array('write_permissions', $_SESSION['permissions'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $pdo->prepare("INSERT INTO permissions (name, description) VALUES (?, ?)")->execute([$name, $description]);
    header("Location: permissions.php");
    exit;
}
if (isset($_GET['delete']) && in_array('write_permissions', $_SESSION['permissions'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM permission_role WHERE permission_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM permissions WHERE id = ?")->execute([$id]);
    header("Location: permissions.php");
    exit;
}
if (isset($_POST['update_permission']) && in_array('write_permissions', $_SESSION['permissions'])) {
    $id = $_POST['id'];
    $pdo->prepare("UPDATE permissions SET name = ?, description = ? WHERE id = ?")
        ->execute([$_POST['name'], $_POST['description'], $id]);
    header("Location: permissions.php");
    exit;
}
$permissions = $pdo->query("SELECT id, name, description FROM permissions")->fetchAll(PDO::FETCH_ASSOC);
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : null;
$create_id = isset($_GET['create']) ? true : false;
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Permissions</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <p>Connecté en tant que <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Déconnexion</a></p>
        <h1>Permissions</h1>
        <a href="index.php"><button>Dashboard</button></a>
        <?php if (in_array('write_permissions', $_SESSION['permissions'])): ?>
            <a href="permissions.php?create"><button>Créer une permission</button></a>
        <?php endif; ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <?php if (in_array('write_permissions', $_SESSION['permissions'])): ?><th>Actions</th><?php endif; ?>
            </tr>
            <?php if ($create_id && in_array('write_permissions', $_SESSION['permissions'])): ?>
                <tr>
                    <form method="POST">
                        <td></td>
                        <td><input type="text" name="name"></td>
                        <td><input type="text" name="description"></td>
                        <td><button type="submit" name="create_permission">Créer</button></td>
                    </form>
                </tr>
            <?php endif; ?>
            <?php foreach ($permissions as $perm): ?>
                <tr>
                    <td><?= $perm['id'] ?></td>
                    <td><?= htmlspecialchars($perm['name']) ?></td>
                    <td><?= htmlspecialchars($perm['description']) ?></td>
                    <?php if (in_array('write_permissions', $_SESSION['permissions'])): ?>
                        <td>
                            <a href="permissions.php?edit=<?= $perm['id'] ?>"><button>Edit</button></a>
                            <a href="permissions.php?delete=<?= $perm['id'] ?>" onclick="return confirm('Supprimer ?');"><button>Supprimer</button></a>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php if ($edit_id === $perm['id'] && in_array('write_permissions', $_SESSION['permissions'])): ?>
                    <tr>
                        <form method="POST">
                            <td><?= $perm['id'] ?></td>
                            <td><input type="text" name="name" value="<?= htmlspecialchars($perm['name']) ?>"></td>
                            <td><input type="text" name="description" value="<?= htmlspecialchars($perm['description']) ?>"></td>
                            <td><button type="submit" name="update_permission">Modifier</button></td>
                        </form>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
    </body>
</html>