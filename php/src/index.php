<?php
require_once 'database.php'; // Inclui a classe de conexão

// Função para exibir candidaturas em HTML
function renderApplications()
{
    $db = DatabaseConnection::getInstance()->getConnection();

    try {
        // Consulta para listar todas as candidaturas
        $stmt = $db->query("SELECT * FROM applications ORDER BY application_date DESC");

        // Verifica se há resultados
        if ($stmt->rowCount() > 0) {
            // Exibe os resultados em uma tabela responsiva
            echo "<div class='table-responsive'>";
            echo "<table class='table table-striped table-bordered'>
                    <thead class='thead-dark'>
                        <tr>
                            <th>Link da Vaga</th>
                            <th>Empresa</th>
                            <th>Vaga</th>
                            <th>Data da Candidatura</th>
                            <th>Status</th>
                            <th>Data de Retorno</th>
                            <th>Data de Criação</th>
                            <th>Última Atualização</th>
                        </tr>
                    </thead>
                    <tbody>";

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td><a href='{$row['job_link']}' target='_blank'>{$row['job_link']}</a></td>
                        <td>{$row['company_name']}</td>
                        <td>{$row['job_title']}</td>
                        <td>{$row['application_date']}</td>
                        <td>{$row['status']}</td>
                        <td>" . ($row['return_date'] ?? 'N/A') . "</td>
                        <td>{$row['created_at']}</td>
                        <td>{$row['updated_at']}</td>
                      </tr>";
            }

            echo "</tbody></table>";
            echo "</div>";
        } else {
            echo "<p class='alert alert-info'>Nenhuma candidatura encontrada.</p>";
        }
    } catch (PDOException $e) {
        echo "<h1>Erro ao buscar dados:</h1><p>" . $e->getMessage() . "</p>";
    }
}

// HTML para exibição
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Candidaturas</title>
    <!-- Incluindo o Bootstrap -->
    <link href="bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <h1 class="text-center">Lista de Candidaturas</h1>
        <?php renderApplications(); ?>
    </div>
    <!-- Scripts do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
