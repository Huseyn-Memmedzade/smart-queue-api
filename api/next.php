<?php
header("Content-Type: application/json; charset=UTF-8");
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Yalnız POST metodu qəbul edilir"]);
    exit();
}

try {

    $pdo->beginTransaction();


    $stmt = $pdo->prepare("SELECT * FROM queue WHERE status = 'Waiting' ORDER BY created_at ASC LIMIT 1 FOR UPDATE");
    $stmt->execute();
    $customer = $stmt->fetch();

    if (!$customer) {
        $pdo->rollBack();
        echo json_encode(["message" => "Gözləyən müştəri yoxdur"]);
        exit();
    }


    $updatePrev = $pdo->prepare("UPDATE queue SET status = 'Completed' WHERE status = 'Serving'");
    $updatePrev->execute();


    $updateStmt = $pdo->prepare("UPDATE queue SET status = 'Serving' WHERE id = ?");
    $updateStmt->execute([$customer['id']]);


    $pdo->commit();

    $customer['status'] = 'Serving';
    echo json_encode([
        "message" => "Növbəti müştəri çağırıldı",
        "customer" => $customer
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["error" => "Xəta baş verdi: " . $e->getMessage()]);
}