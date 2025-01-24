<?php
require_once 'database.php'; // Inclui a classe de conexão

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['edit_form'] === 'edit_form') {
    $job_link_hash = $_POST['job_link_hash'];
    $company_name = $_POST['company_name'];
    $job_title = $_POST['job_title'];
    $status = $_POST['status'];
    $return_date = $_POST['return_date'];

    try {
        // Obtém a conexão com o banco de dados
        $db = DatabaseConnection::getInstance()->getConnection();

        // Atualiza os dados no banco
        $stmt = $db->prepare("UPDATE applications 
                              SET company_name = :company_name, 
                                  job_title = :job_title, 
                                  status = :status, 
                                  return_date = :return_date, 
                                  updated_at = NOW()
                              WHERE job_link_hash = :job_link_hash");
        $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':job_title', $job_title);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':return_date', $return_date);
        $stmt->bindParam(':job_link_hash', $job_link_hash);

        $stmt->execute();
        //echo "UPDATE applications SET company_name = '$company_name', job_title = '$job_title', status = '$status', return_date = '$return_date', updated_at = NOW() WHERE job_link_hash = '$job_link_hash';";

        if ($stmt->rowCount() > 0) {
            echo "<div class='alert alert-success' role='alert'>Vaga atualizada com sucesso!</div>";
            echo "<a href='edit.php' class='btn btn-info'>Voltar para edição</a>";
            } else {
            echo "Nenhuma linha foi atualizada. Verifique os dados enviados.";
        }        
    } catch (PDOException $e) {
        echo "<h1>Erro ao atualizar o banco de dados:</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST'  && $_POST['edit'] === 'edit' ) { //Antigo edit_form.php
    $job_link_hash = $_POST['job_link_hash'];

    try {
        // Obtém a conexão com o banco de dados
        $db = DatabaseConnection::getInstance()->getConnection();

        // Consulta para buscar os dados da vaga selecionada
        $stmt = $db->prepare("SELECT * FROM applications WHERE job_link_hash = :job_link_hash");
        $stmt->bindParam(':job_link_hash', $job_link_hash, PDO::PARAM_STR);
        $stmt->execute();

        // Verifica se a vaga foi encontrada
        if ($stmt->rowCount() > 0) {
            $application = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "<h1>Editando a Vaga</h1>";
            echo "<form action='edit.php' method='POST'>";
            echo "<input type='hidden' name='edit_form' value='edit_form'>";
            echo "<input type='hidden' name='job_link_hash' value='{$application['job_link_hash']}'>";
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
    try { //Antigo código edit.php
        // Obtém a conexão com o banco de dados
        $db = DatabaseConnection::getInstance()->getConnection();

        // Consulta para listar todas as vagas que não têm o status 'negada'
        $stmt = $db->prepare("SELECT job_link_hash, company_name, job_title FROM applications WHERE status != 'negada'");
        $stmt->execute();

        // Verifica se há resultados
        if ($stmt->rowCount() > 0) {
            echo "<h1>Selecione a Vaga para Editar</h1>";
            //echo "<form action='edit_form.php' method='POST'>";
            echo "<form action='edit.php' method='POST'>";
            echo "<input type='hidden' name='edit' value='edit'>";
            echo "<div class='form-group'>";
            echo "<label for='job_link_hash'>Escolha uma vaga:</label>";
            echo "<select name='job_link_hash' id='job_link_hash' class='form-control' required>";

            // Itera sobre os resultados e os exibe em um menu suspenso
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<option value='{$row['job_link_hash']}'>{$row['company_name']} - {$row['job_title']}</option>";
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
}
?>
<head>
    <!-- Adicionando o CSS do Bootstrap -->
    <link href="bootstrap.min.css" rel="stylesheet">
</head>