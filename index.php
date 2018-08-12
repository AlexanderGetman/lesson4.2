<?php

$pdo = new PDO('mysql:host=localhost;dbname=mybd', 'root', '');
$result = $pdo->query('SELECT * FROM tasks');

if (isset($_GET['id']) && isset($_GET['action'])) {
    if (($_GET['action'] == 'edit') && isset($_POST['description'])) {
        $sql = "UPDATE tasks SET description = ? WHERE id = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute(["{$_POST['description']}", "{$_GET['id']}"]);
        header('Location: ./indexbc.php');
    } else {
        $sql = "SELECT * FROM tasks";
    }
    if ($_GET['action'] == 'done') {
        $sql = "UPDATE tasks SET is_done = 1 WHERE id = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute(["{$_GET['id']}"]);
        header( 'Location: ./indexbc.php');
    }
    if ($_GET['action'] == 'delete') {
        $sql = "DELETE FROM tasks WHERE id = ?";
        $statement = $pdo->prepare($sql);
        $statement->execute(["{$_GET['id']}"]);
        header( 'Location: ./indexbc.php');
    }
}

if (isset($_POST['description']) && empty($_GET['action'])) {
    $date = date('Y-m-d H:i:s');
    $sql = "INSERT INTO  tasks (description, date_added) VALUES (?, ?)";
    $statement = $pdo->prepare($sql);
    $statement->execute(["{$_POST['description']}", "{$date}"]);
    header( 'Location: ./indexbc.php');
}

if (isset($_POST['sort']) && isset($_POST['sort_by'])) {
    $sql = "SELECT * FROM tasks ORDER BY {$_POST['sort_by']} ASC";
    $statement = $pdo->prepare($sql);
    $statement->execute(["{$_POST['sort_by']}"]);
    header( 'Location: ./indexbc.php');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
<div style="text-align: center">
    <h1>Задачи</h1>
</div>
<form action="indexbc.php" method="POST">
    <input type="text" name="description" placeholder="Описание задачи" value="<?php if (isset($_POST['description'])) echo $_POST['description']; ?>">
    <input type="submit" name="save" value="Сохранить">
</form>

<form action="indexbc.php" method="POST">
    <input type="submit" class="button" value="Сортировать по:" name="sort"/>
    <select name="sort_by">
        <option value="description"> Описанию </option>
        <option value="date_added"> Дате добавления </option>
        <option value="is_done"> Статусу выполнения </option>
    </select>
</form>


<br>
<div>
    <table border="1">
        <thead>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус выполнения</th>
        <th>Управление задачами</th>
        </thead>
        <tbody>
        <?php foreach($result as $row): ?>
            <tr>
                <td><?=htmlspecialchars($row['description'])?></td>
                <td><?=htmlspecialchars($row['date_added'])?></td>
                <td <?php if ($row['is_done'] == 1) echo 'style="color: red;"'; ?>>
                    <?php if ($row['is_done'] == 0) {
                        echo 'В процессе';
                    } else {
                        echo 'Выполнено';
                    } ?>
                </td>
                <td><a href="?id=<?php echo $row['id']; ?>&action=edit&description=<?php echo $row['description']; ?>">Изменить</a>
                    <a href="?id=<?php echo $row['id']; ?>&action=done">Выполнить</a>
                    <a href="?id=<?php echo $row['id']; ?>&action=delete">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
