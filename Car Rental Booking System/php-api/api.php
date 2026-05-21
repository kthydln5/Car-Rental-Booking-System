<?php
/*
    STARTER VERSION - INTENTIONALLY INCOMPLETE

    Working:
    - SQLite connection
    - vehicles and reservations table creation
    - sample vehicle seed data
    - ping
    - list_vehicles
    - list_available_vehicles
    - list_reservations

    TODO later:
    - add_vehicle
    - update_vehicle_status
    - create_reservation
    - update_reservation_status
*/

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$dbFile = __DIR__ . '/car_rental_simple.sqlite';

function db() {
    global $dbFile;

    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("CREATE TABLE IF NOT EXISTS vehicles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        plate_number TEXT NOT NULL,
        brand TEXT NOT NULL,
        model TEXT NOT NULL,
        vehicle_type TEXT NOT NULL,
        daily_rate REAL NOT NULL DEFAULT 0,
        status TEXT NOT NULL DEFAULT 'Available',
        created_at TEXT NOT NULL
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS reservations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        customer_name TEXT NOT NULL,
        contact TEXT,
        license_number TEXT,
        vehicle_id INTEGER NOT NULL,
        pickup_date TEXT NOT NULL,
        return_date TEXT NOT NULL,
        rental_days INTEGER NOT NULL DEFAULT 1,
        total_amount REAL NOT NULL DEFAULT 0,
        reservation_status TEXT NOT NULL DEFAULT 'Pending',
        notes TEXT,
        created_at TEXT NOT NULL
    )");

    $count = $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();

    if ((int)$count === 0) {
        $stmt = $pdo->prepare("INSERT INTO vehicles(plate_number, brand, model, vehicle_type, daily_rate, status, created_at)
                               VALUES(?,?,?,?,?,?,?)");

        $stmt->execute(['ABC-1234', 'Toyota', 'Vios', 'Sedan', 1800, 'Available', date('Y-m-d H:i:s')]);
        $stmt->execute(['SUV-2026', 'Mitsubishi', 'Xpander', 'MPV', 2500, 'Available', date('Y-m-d H:i:s')]);
        $stmt->execute(['VAN-7788', 'Toyota', 'Hiace', 'Van', 4200, 'Available', date('Y-m-d H:i:s')]);
    }

    return $pdo;
}

function ok($data = []) {
    echo json_encode(['success' => true] + $data);
    exit;
}

function fail($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

$pdo = db();
$action = $_GET['action'] ?? $_POST['action'] ?? 'ping';

try {
    if ($action === 'ping') {
        ok(['message' => 'Starter Car Rental API is running']);
    }

    if ($action === 'list_vehicles') {
        $rows = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        ok(['vehicles' => $rows]);
    }

    if ($action === 'list_available_vehicles') {
        $stmt = $pdo->prepare("SELECT * FROM vehicles WHERE status='Available' ORDER BY id DESC");
        $stmt->execute();
        ok(['vehicles' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    if ($action === 'list_reservations') {
        $rows = $pdo->query("SELECT reservations.*, vehicles.plate_number, vehicles.brand, vehicles.model, vehicles.vehicle_type
                             FROM reservations
                             JOIN vehicles ON vehicles.id = reservations.vehicle_id
                             ORDER BY reservations.id DESC")->fetchAll(PDO::FETCH_ASSOC);

        ok(['reservations' => $rows]);
    }

    if ($action === 'add_vehicle') {
        fail('TODO: add_vehicle is not implemented yet. Follow the commit roadmap steps for Admin Vehicle Management.');
    }

    if ($action === 'update_vehicle_status') {
        fail('TODO: update_vehicle_status is not implemented yet. Follow the commit roadmap steps for Vehicle Status Management.');
    }

    if ($action === 'create_reservation') {
        fail('TODO: create_reservation is not implemented yet. Follow the commit roadmap steps for Customer Reservation Submission.');
    }

    if ($action === 'update_reservation_status') {
        fail('TODO: update_reservation_status is not implemented yet. Follow the commit roadmap steps for Admin Reservation Review.');
    }

    fail('Invalid action.');
} catch (Exception $e) {
    fail($e->getMessage(), 500);
}
?>