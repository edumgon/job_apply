<?php
require_once 'database.php'; // Inclui a classe de conexão
//validação de sessao
session_start();

if (!isset($_SESSION['session_token'])) {
    // Redireciona para a página de login se não houver sessão
    header('Location: login.php');
    exit;
}

try {
    $db = DatabaseConnection::getInstance()->getConnection();

    // Valida o token no banco de dados
    $stmt = $db->prepare("SELECT email FROM users WHERE session_token = :session_token");
    $stmt->bindParam(':session_token', $_SESSION['session_token']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Atualiza o último acesso
        $stmt = $db->prepare("UPDATE users SET session_last_active = NOW() WHERE email = :email");
        $stmt->bindParam(':email', $user['email']);
        $stmt->execute();
    } else {
        // Token inválido, redireciona para o login
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    echo "Erro ao conectar ao banco: " . $e->getMessage();
}
//fim da validação
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
                $link = substr($row['job_link'],8,17);
                echo "<tr>
                        <td><a href='{$row['job_link']}' target='_blank'>{$link}</a></td>
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
    <script src="bootstrap.bundle.min.js"></script>
</body>
</html>
