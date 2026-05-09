<?php
/*
    STARTER VERSION- CUSTOMER WEB SIDE

    This page is intentionally incomplete.
    It displays available vehicles and shows the customer reservation form.
    The form does NOT save yet.
*/

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

$pdo = db();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = 'STARTER TODO: Reservation form is not connected yet. Students must implement reservation saving in later commits.';
}

$vehicles = $pdo->query("SELECT * FROM vehicles WHERE status='Available' ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$totalAvailable = count($vehicles);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Starter Customer Car Rental Reservation</title>
    <style>
        *{box-sizing:border-box}
        body{margin:0;font-family:Segoe UI,Arial,sans-serif;background:#f8fafc;color:#0f172a}
        .hero{background:linear-gradient(135deg,#0f172a,#1d4ed8);color:white;padding:34px}
        .hero h1{margin:0;font-size:38px}
        .hero p{color:#dbeafe;font-weight:600}
        .links a{display:inline-block;color:white;background:rgba(255,255,255,.15);padding:9px 12px;border-radius:999px;text-decoration:none;margin-right:8px;font-weight:800}
        .wrap{max-width:1100px;margin:24px auto;padding:0 16px}
        .notice{background:#fef3c7;color:#92400e;border-left:6px solid #f59e0b;border-radius:14px;padding:14px 16px;margin-bottom:18px;font-weight:900}
        .grid{display:grid;grid-template-columns:1fr 360px;gap:22px}
        .panel{background:white;border-radius:24px;padding:22px;box-shadow:0 12px 30px rgba(15,23,42,.12);margin-bottom:20px}
        .panel h2{margin:0 0 15px;color:#1d4ed8}
        .cars{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}
        .card{background:#eff6ff;border:1px solid #bfdbfe;border-radius:18px;padding:16px}
        .card h3{margin:0;color:#0f172a}
        .card p{margin:7px 0;color:#475569;font-weight:600}
        .price{display:inline-block;background:#1d4ed8;color:white;border-radius:999px;padding:7px 10px;font-weight:900}
        label{display:block;font-weight:900;margin:10px 0 5px;color:#334155}
        input,select,textarea{width:100%;padding:10px;border:1px solid #cbd5e1;border-radius:12px;background:#f8fafc}
        button{width:100%;border:0;background:#1d4ed8;color:white;border-radius:12px;padding:12px;margin-top:12px;font-weight:900}
        .todo{background:#e0f2fe;color:#075985;border-radius:14px;padding:14px;margin-top:15px;font-weight:700}
        @media(max-width:900px){.grid{grid-template-columns:1fr}.cars{grid-template-columns:1fr}}
    </style>
</head>
<body>
    <section class="hero">
        <h1>Starter Customer Car Rental Reservation</h1>
        <p>Early-stage version. Customers can view available cars, but reservation saving is still a TODO.</p>
        <div class="links">
            <a href="api.php?action=ping">API Ping</a>
            <a href="api.php?action=list_available_vehicles">Available Vehicles JSON</a>
            <a href="api.php?action=list_reservations">Reservations JSON</a>
        </div>
    </section>

    <main class="wrap">
        <div class="notice">
            Starter build: this project is intentionally incomplete so commits can show real progress.
        </div>

        <?php if($msg): ?><div class="notice"><?=htmlspecialchars($msg)?></div><?php endif; ?>

        <div class="grid">
            <section>
                <div class="panel">
                    <h2>Available Vehicles: <?=htmlspecialchars($totalAvailable)?></h2>
                    <div class="cars">
                        <?php foreach($vehicles as $v): ?>
                        <div class="card">
                            <h3><?=htmlspecialchars($v['brand'].' '.$v['model'])?></h3>
                            <p>Plate: <?=htmlspecialchars($v['plate_number'])?></p>
                            <p>Type: <?=htmlspecialchars($v['vehicle_type'])?></p>
                            <span class="price">PHP <?=number_format((float)$v['daily_rate'],2)?> / day</span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="panel">
                    <h2>TODO: Customer Status Checker</h2>
                    <div class="todo">
                        Later commits should add a contact-number search form here so customers can check if their request is Pending, Approved, Active, Returned, or Cancelled.
                    </div>
                </div>
            </section>

            <aside class="panel">
                <h2>Reserve a Vehicle</h2>
                <form method="post">
                    <label>Full Name</label>
                    <input name="customer_name" placeholder="Juan Dela Cruz">

                    <label>Contact Number</label>
                    <input name="contact" placeholder="09XXXXXXXXX">

                    <label>Driver License Number</label>
                    <input name="license_number" placeholder="N01-23-456789">

                    <label>Vehicle</label>
                    <select name="vehicle_id">
                        <?php foreach($vehicles as $v): ?>
                        <option value="<?=htmlspecialchars($v['id'])?>">
                            <?=htmlspecialchars($v['plate_number'].' - '.$v['brand'].' '.$v['model'])?>
                        </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Pickup Date</label>
                    <input type="date" name="pickup_date">

                    <label>Return Date</label>
                    <input type="date" name="return_date">

                    <label>Notes</label>
                    <textarea name="notes" rows="4"></textarea>

                    <button>Submit Reservation Request</button>
                </form>

                <div class="todo">
                    TODO: Make this form save into SQLite using create_reservation logic.
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
