<?php

require "conexao.php";

header("Content-Type: application/json");

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    case 'GET':
        if (isset($_GET['codigo'])) {
            $codigo = intval($_GET['codigo']);
            $sql = "SELECT * FROM pessoas WHERE codigo = :codigo";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->execute();
            $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($pessoa ?: ['error' => 'Pessoa não encontrada']);
        } else {
            $stmt = $conn->query("SELECT * FROM pessoas");
            $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($pessoas);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['nome_completo']) || empty($data['idade']) || empty($data['telefone']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campos obrigatórios não preenchidos.']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO pessoas (nome_completo, idade, telefone, email, foto) VALUES (:nome_completo, :idade, :telefone, :email, :foto)");
        $stmt->bindParam(':nome_completo', $data['nome_completo']);
        $stmt->bindParam(':idade', $data['idade']);
        $stmt->bindParam(':telefone', $data['telefone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':foto', $data['foto']);
        $stmt->execute();

        echo json_encode([
            'codigo' => $conn->lastInsertId(),
            'mensagem' => 'Pessoa adicionada com sucesso!'
        ]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['codigo']) || empty($data['nome_completo']) || empty($data['idade']) || empty($data['telefone']) || empty($data['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Todos os campos obrigatórios devem ser informados.']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE pessoas SET nome_completo = :nome_completo, idade = :idade, telefone = :telefone, email = :email, foto = :foto WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $data['codigo']);
        $stmt->bindParam(':nome_completo', $data['nome_completo']);
        $stmt->bindParam(':idade', $data['idade']);
        $stmt->bindParam(':telefone', $data['telefone']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':foto', $data['foto']);
        $stmt->execute();

        echo json_encode(['mensagem' => 'Pessoa atualizada com sucesso.']);
        break;

    case 'DELETE':
        if (!isset($_GET['codigo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'O código da pessoa é obrigatório para exclusão.']);
            exit;
        }

        $codigo = intval($_GET['codigo']);
        $stmt = $conn->prepare("DELETE FROM pessoas WHERE codigo = :codigo");
        $stmt->bindParam(':codigo', $codigo);
        $stmt->execute();

        echo json_encode(['mensagem' => 'Pessoa excluída com sucesso.']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;
}
