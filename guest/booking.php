
<?php
require_once __DIR__ . "/../config/database.php";

$message = "";

$room_id = $_GET["room_id"] ?? "";
$checkin = $_GET["checkin"] ?? "";
$checkout = $_GET["checkout"] ?? "";
$guests = $_GET["guests"] ?? "";

$name = "";
$email = "";
$phone = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $room_id = $_POST["room_id"];
    $checkin = $_POST["checkin"];
    $checkout = $_POST["checkout"];
    $guests = $_POST["guests"];

    if ($checkout <= $checkin || $checkin < date("Y-m-d")) {

        $message = "Please select valid booking dates.";

    } else {

        // Check whether the room is already booked

        $check_sql = "SELECT booking_id FROM bookings
                      WHERE room_id = ?
                      AND checkin < ?
                      AND checkout > ?
                      AND COALESCE(status, 'Pending') <> 'Cancelled'";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        if ($check_stmt) {

            mysqli_stmt_bind_param(
                $check_stmt,
                "iss",
                $room_id,
                $checkout,
                $checkin
            );

            mysqli_stmt_execute($check_stmt);

            $check_result = mysqli_stmt_get_result($check_stmt);

            if (mysqli_num_rows($check_result) > 0) {

                $message = "Sorry! This room is already booked for these dates.";

            } else {

                $sql = "INSERT INTO bookings
                (guest_name, email, phone, room_id, checkin, checkout, guests)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "sssissi",
                        $name,
                        $email,
                        $phone,
                        $room_id,
                        $checkin,
                        $checkout,
                        $guests
                    );

                    if (mysqli_stmt_execute($stmt)) {
                        $message = "Booking submitted successfully!";
                        $name = "";
                        $email = "";
                        $phone = "";
                    } else {
                        $message = "Booking failed. Please try again.";
                    }

                    mysqli_stmt_close($stmt);

                } else {
                    $message = "Booking failed!";
                }
            }

            mysqli_stmt_close($check_stmt);

        } else {
            $message = "Unable to check room availability.";
        }
    }
}

$rooms = mysqli_query($conn, "SELECT * FROM rooms ORDER BY room_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ceylonora - Room Booking</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/guest.css">
</head>
<body>

<nav>
    <h2>Ceylonora</h2>

    <div class="links">
        <a href="../index.php">Home</a>
        <a href="rooms.php">Rooms</a>
        <a href="check-availability.php">Check Availability</a>
    </div>
</nav>

<section class="section">

    <h1>Book Your Room</h1>
    <p>Enter your booking details below.</p>

    <p class="availability-message">
        <?php echo htmlspecialchars($message); ?>
    </p>

    <form class="booking-form" method="POST">

        <input type="text"
               name="name"
               placeholder="Full Name"
               value="<?php echo htmlspecialchars($name); ?>"
               required>

        <input type="email"
               name="email"
               placeholder="Email Address"
               value="<?php echo htmlspecialchars($email); ?>"
               required>

        <input type="tel"
               name="phone"
               placeholder="Phone Number"
               value="<?php echo htmlspecialchars($phone); ?>"
               required>

        <label>Select Room</label>

        <select name="room_id" required>
            <option value="">Select Room</option>

            <?php while ($room = mysqli_fetch_assoc($rooms)) { ?>

                <option value="<?php echo $room['room_id']; ?>"
                    <?php echo ($room_id == $room['room_id']) ? "selected" : ""; ?>>

                    <?php echo htmlspecialchars($room['room_name']); ?>

                </option>

            <?php } ?>

        </select>

        <label>Check-in Date</label>
        <input type="date"
               name="checkin"
               min="<?php echo date('Y-m-d'); ?>"
               value="<?php echo htmlspecialchars($checkin); ?>"
               required>

        <label>Check-out Date</label>
        <input type="date"
               name="checkout"
               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
               value="<?php echo htmlspecialchars($checkout); ?>"
               required>

        <label>Number of Guests</label>

        <select name="guests" required>
            <option value="">Number of Guests</option>

            <?php for ($i = 1; $i <= 4; $i++) { ?>

                <option value="<?php echo $i; ?>"
                    <?php echo ($guests == $i) ? "selected" : ""; ?>>

                    <?php echo $i; ?>

                </option>

            <?php } ?>

        </select>

        <button type="submit">Submit Booking</button>

    </form>

</section>

</body>
</html>
