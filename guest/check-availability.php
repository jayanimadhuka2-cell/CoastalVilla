
<?php
require_once __DIR__ . "/../config/database.php";

$rooms = [];
$message = "";

$checkin = $_GET["checkin"] ?? "";
$checkout = $_GET["checkout"] ?? "";
$guests = $_GET["guests"] ?? "";

if (isset($_GET["search"])) {

    if (empty($checkin) || empty($checkout) || empty($guests)) {

        $message = "Please fill in all details.";

    } elseif ($checkin < date("Y-m-d") || $checkout <= $checkin) {

        $message = "Please select valid dates.";

    } else {

        $sql = "SELECT * FROM rooms
                WHERE room_id NOT IN (
                    SELECT room_id FROM bookings
                    WHERE checkin < ?
                    AND checkout > ?
                    AND COALESCE(status, 'Pending') <> 'Cancelled'
                )
                ORDER BY room_id DESC";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $checkout,
                $checkin
            );

            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            while ($room = mysqli_fetch_assoc($result)) {
                $rooms[] = $room;
            }

            mysqli_stmt_close($stmt);

            if (count($rooms) == 0) {
                $message = "No rooms available for selected dates.";
            }

        } else {
            $message = "Search failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Check Availability - Ceylonora</title>
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
        <a href="../auth/login.php">Login</a>
    </div>
</nav>

<section class="section availability-section">

    <h1>Check Room Availability</h1>
    <p>Find your perfect stay at Ceylonora</p>

    <form method="GET" class="availability-form">

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
            <option value="">Select Guests</option>

            <?php for ($i = 1; $i <= 4; $i++) { ?>
                <option value="<?php echo $i; ?>"
                    <?php echo ($guests == $i) ? "selected" : ""; ?>>
                    <?php echo $i; ?> Guest(s)
                </option>
            <?php } ?>

        </select>

        <button type="submit" name="search" value="1">
            Check Availability
        </button>

    </form>

    <?php if ($message != "") { ?>
        <p class="availability-message">
            <?php echo htmlspecialchars($message); ?>
        </p>
    <?php } ?>

    <?php if (isset($_GET["search"]) && count($rooms) > 0) { ?>

        <h2>Available Rooms</h2>

        <div class="room-container">

            <?php foreach ($rooms as $room) { ?>

                <div class="room-card">

                    <img
                        src="../images/<?php echo htmlspecialchars($room['image']); ?>"
                        alt="<?php echo htmlspecialchars($room['room_name']); ?>"
                    >

                    <h3>
                        <?php echo htmlspecialchars($room['room_name']); ?>
                    </h3>

                    <p>
                        <?php echo htmlspecialchars($room['description']); ?>
                    </p>

                    <h3>
                        Rs. <?php echo number_format($room['price']); ?>
                        / Night
                    </h3>

                    <a class="btn"
                       href="booking.php?room_id=<?php echo $room['room_id']; ?>&checkin=<?php echo urlencode($checkin); ?>&checkout=<?php echo urlencode($checkout); ?>&guests=<?php echo urlencode($guests); ?>">
                        Book Now
                    </a>

                </div>

            <?php } ?>

        </div>

    <?php } ?>

</section>

</body>
</html>
