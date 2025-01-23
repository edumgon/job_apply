<?php
require_once 'database.php'; // Inclui a classe de conexão

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_link = $_POST['job_link'];
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
                              WHERE job_link = :job_link");
        $stmt->bindParam(':company_name', $company_name);
        $stmt->bindParam(':job_title', $job_title);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':return_date', $return_date);
        $stmt->bindParam(':job_link', $job_link);

        $stmt->execute();

        echo "<div class='alert alert-success' role='alert'>Vaga atualizada com sucesso!</div>";
        echo "<a href='edit.php' class='btn btn-info'>Voltar para edição</a>";
    } catch (PDOException $e) {
        echo "<h1>Erro ao atualizar o banco de dados:</h1>";
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
