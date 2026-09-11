<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

switch ($method) {
    case 'GET':
        if ($id) {

            $stmt = $pdo->prepare("SELECT * FROM queue WHERE id = ?");
            $stmt->execute([$id]);
            $customer = $stmt->fetch();

            if (!$customer) {
                http_response_code(404);
                echo json_encode(["message" => "Müştəri tapılmadı"]);
                exit();
            }

            $position = null;
            if ($customer['status'] === 'Waiting') {

                $posStmt = $pdo->prepare("SELECT COUNT(*) as pos FROM queue WHERE status = 'Waiting' AND created_at < ?");
                $posStmt->execute([$customer['created_at']]);
                $position = $posStmt->fetch()['pos'] + 1;
            }

            echo json_encode([
                "customer" => $customer,
                "position" => $position
            ]);
        } else {

            $stmt = $pdo->prepare("SELECT * FROM queue WHERE status = 'Waiting' ORDER BY created_at ASC");
            $stmt->execute();
            $queue = $stmt->fetchAll();
            echo json_encode($queue);
        }
        break;

    case 'POST':

        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['name'])) {
            http_response_code(400);
            echo json_encode(["message" => "Müştəri adı daxil edilməlidir"]);
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO queue (name, status) VALUES (?, 'Waiting')");
        $stmt->execute([trim($input['name'])]);
        $newId = $pdo->lastInsertId();

        http_response_code(201);
        echo json_encode([
            "id" => $newId,
            "name" => trim($input['name']),
            "status" => "Waiting",
            "message" => "Müştəri növbəyə əlavə olundu"
        ]);
        break;

    case 'DELETE':

        if (!$id) {
            http_response_code(400);
            echo json_encode(["message" => "ID daxil edilməlidir"]);
            exit();
        }

        $stmt = $pdo->prepare("DELETE FROM queue WHERE id = ?");
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Müştəri növbədən silindi"]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Müştəri tapılmadı"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "İcazə verilməyən metod"]);
        break;
}