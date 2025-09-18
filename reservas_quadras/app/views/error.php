
<!DOCTYPE html>
<html>
<head>
    <title>Erro</title>
</head>
<body>
    <h1>Ocorreu um erro</h1>
    <p><?php echo isset($message) ? htmlspecialchars($message) : "Erro desconhecido."; ?></p>
</body>
</html>

