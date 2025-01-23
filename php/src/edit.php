<?php
require_once 'database.php'; // Inclui a classe de conexão

try {
    // Obtém a conexão com o banco de dados
    $db = DatabaseConnection::getInstance()->getConnection();

    // Consulta para listar todas as vagas que não têm o status 'negada'
    $stmt = $db->prepare("SELECT job_link, company_name, job_title FROM applications WHERE status != 'negada'");
    $stmt->execute();

    // Verifica se há resultados
    if ($stmt->rowCount() > 0) {
        echo "<h1>Selecione a Vaga para Editar</h1>";
        echo "<form action='edit_form.php' method='POST'>";
        echo "<div class='form-group'>";
        echo "<label for='job_link'>Escolha uma vaga:</label>";
        echo "<select name='job_link' id='job_link' class='form-control' required>";

        // Itera sobre os resultados e os exibe em um menu suspenso
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='{$row['job_link']}'>{$row['company_name']} - {$row['job_title']}</option>";
        }

        echo "</select>";
        echo "</div><br>";
        echo "<button type='submit' class='btn btn-primary'>Editar</button>";
        echo "</form>";
    } else {
        echo "<p>Não há vagas disponíveis para edição.</p>";
    }
} catch (PDOException $e) {
    echo "<h1>Erro ao acessar o banco de dados:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
<head>
    <!-- Adicionando o CSS do Bootstrap -->
    <link href="bootstrap.min.css" rel="stylesheet">
</head>