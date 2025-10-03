<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
function can($permission) {
  return isset($_SESSION['permissions']) && in_array($permission, $_SESSION['permissions']);
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8">
    <title>Page avec deux boutons</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <p>Connecté en tant que <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Déconnexion</a></p>
    <?php if (can('write_user') or can('read_user')): ?>
      <button><a href="users.php">Utilisateurs</a></button>
    <?php endif; ?>
    <?php if (can('write_roles') or can('read_roles')): ?>
      <button><a href="roles.php">Rôles</a></button>
    <?php endif; ?>
    <?php if (can('write_permissions') or can('read_permissions')): ?>
      <button><a href="permissions.php">Permissions</a></button>
    <?php endif; ?>
    <?php if (can('rwrite_logs') or can('read_logs')): ?>
      <button><a href="logs.php">Logs</a></button>
    <?php endif; ?>
  </body>
</html>