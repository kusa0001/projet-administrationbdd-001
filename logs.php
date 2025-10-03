<?php
require 'vendor/autoload.php';
use MongoDB\Client;
$client = new Client("mongodb://localhost:27017");
$collection = $client->projet_logs->activity_logs;
$filter = [];
$options = ['sort' => ['timestamp' => -1]];
if (!empty($_GET['user_id'])) $filter['user_id'] = (int)$_GET['user_id'];
if (!empty($_GET['action'])) $filter['action'] = $_GET['action'];
if (!empty($_GET['date'])) {
    $date = new DateTime($_GET['date']);
    $filter['timestamp'] = ['$gte' => new MongoDB\BSON\UTCDateTime($date->getTimestamp()*1000)];
}
$rows = $collection->find($filter, $options);
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Logs d'activité</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <p>Connecté en tant que <?= htmlspecialchars($_SESSION['username']) ?> | <a href="logout.php">Déconnexion</a></p>
        <h1>Logs d'activité</h1>
        <a href="index.php"><button>Dashboard</button></a>
        <form method="get">
        <label>User ID:</label>
        <input type="number" name="user_id" value="<?= htmlspecialchars($_GET['user_id'] ?? '') ?>">
        <label>Action:</label>
        <input type="text" name="action" value="<?= htmlspecialchars($_GET['action'] ?? '') ?>">
        <label>Date après:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">
        <button type="submit">Filtrer</button>
    </form>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>User ID</th>
            <th>Action</th>
            <th>Cible</th>
            <th>Timestamp</th>
            <th>IP</th>
            <th>Status</th>
        </tr>
        <?php foreach ($rows as $log): ?>
        <tr>
            <td><?= htmlspecialchars($log['user_id']) ?></td>
            <td><?= htmlspecialchars($log['action']) ?></td>
            <td><?= htmlspecialchars($log['target']) ?></td>
            <td><?= $log['timestamp']->toDateTime()->format("Y-m-d H:i:s") ?></td>
            <td><?= htmlspecialchars($log['ip']) ?></td>
            <td><?= htmlspecialchars($log['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>