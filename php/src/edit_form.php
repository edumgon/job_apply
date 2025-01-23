<?php
require_once 'database.php'; // Inclui a classe de conexão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_link = $_POST['job_link'];

    try {
        // Obtém a conexão com o banco de dados
        $db = DatabaseConnection::getInstance()->getConnection();

        // Consulta para buscar os dados da vaga selecionada
        $stmt = $db->prepare("SELECT * FROM applications WHERE job_link = :job_link");
        $stmt->bindParam(':job_link', $job_link, PDO::PARAM_STR);
        $stmt->execute();

        // Verifica se a vaga foi encontrada
        if ($stmt->rowCount() > 0) {
            $application = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "<h1>Editando a Vaga</h1>";
            echo "<form action='update_application.php' method='POST'>";
            echo "<input type='hidden' name='job_link' value='{$application['job_link']}'>";
            echo "<div class='form-group'>";
            echo "<label for='company_name'>Empresa:</label>";
            echo "<input type='text' id='company_name' name='company_name' class='form-control' value='{$application['company_name']}' required><br><br>";
            echo "</div>";
            
            echo "<div class='form-group'>";
            echo "<label for='job_title'>Nome da Vaga:</label>";
            echo "<input type='text' id='job_title' name='job_title' class='form-control' value='{$application['job_title']}' required><br><br>";
            echo "</div>";
            
            echo "<div class='form-group'>";
            echo "<label for='status'>Status:</label>";
            echo "<select id='status' name='status' class='form-control'>";
            echo "<option value='inicial' " . ($application['status'] === 'inicial' ? 'selected' : '') . ">Inicial</option>";
            echo "<option value='entrevista' " . ($application['status'] === 'entrevista' ? 'selected' : '') . ">Entrevista</option>";
            echo "<option value='proposta' " . ($application['status'] === 'proposta' ? 'selected' : '') . ">Proposta</option>";
            echo "<option value='aprovado' " . ($application['status'] === 'aprovado' ? 'selected' : '') . ">Aprovado</option>";
            echo "<option value='negado' " . ($application['status'] === 'negado' ? 'selected' : '') . ">Negado</option>";
            echo "</select><br><br>";
            echo "</div>";

            echo "<div class='form-group'>";
            echo "<label for='return_date'>Data de Retorno:</label>";
            echo "<input type='date' id='return_date' name='return_date' class='form-control' value='{$application['return_date']}'><br><br>";
            echo "</div>";
            
            echo "<button type='submit' class='btn btn-success'>Salvar Alterações</button>";
            echo "</form>";
        } else {
            echo "<p>Vaga não encontrada.</p>";
        }
    } catch (PDOException $e) {
        echo "<h1>Erro ao acessar o banco de dados:</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>Requisição inválida.</p>";
}
?>
<head>
    <!-- Adicionando o CSS do Bootstrap -->
    <link href="bootstrap.min.css" rel="stylesheet">
</head>