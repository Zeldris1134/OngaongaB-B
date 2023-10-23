<!DOCTYPE html>
<?php
include "checksession.php";
loginStatus();
checkUser();
?>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Booking Details</title>
</head>
<style>
  fieldset {
    width: 700px;
  }
</style>

<body>
  <?php
  include "header.php";
  include "menu.php";
  echo '<div id="site_content">';
  
  include "checksession.php";
  include "config.php";
  $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

  if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySql." . mysqli_connect_error();
    exit; //stop processing the page furhter.
  }

  //check if id exists
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
      echo "<h2>Invalid Booking</h2>";
      exit;
    }
  }

  $query = 'SELECT booking.bookingID, room.roomname, booking.checkInDate, booking.checkOutDate, booking.contactNumber, booking.bookingExtras, booking.roomReview FROM booking INNER JOIN room ON booking.roomID = room.roomID WHERE bookingID=' . $id;

  $result = mysqli_query($DBC, $query);

  if ($result === false) {
    echo "Error: " . mysqli_error($DBC);
    exit; // Stop processing the page further.
  }

  $rowcount = mysqli_num_rows($result);
  ?>
  <h2>Logged in as <?php echo $_SESSION['username']?></h2>
  <h1>Booking Details View</h1>
  <h2>
    <a href="currentBookings.php">[Return to the booking listing]</a>
    <a href="/bnb_assignment3">[Return to the main page]</a>
  </h2>
  <?php
  if ($rowcount > 0) {
    echo "<fieldset><legend>Booking Detail #$id</legend><dl>";
    $row = mysqli_fetch_assoc($result);

    echo "<dt>Room name: </dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkin Date: </dt><dd>" . $row['checkInDate'] . "</dd>" . PHP_EOL;
    echo "<dt>Checkout Date: </dt><dd>" . $row['checkOutDate'] . "</dd>" . PHP_EOL;
    echo "<dt>Contact Number: </dt><dd>" . $row['contactNumber'] . "</dd>" . PHP_EOL;
    echo "<dt>Booking Extras: </dt><dd>" . $row['bookingExtras'] . "</dd>" . PHP_EOL;
    echo "<dt>Room Review: </dt><dd>" . $row['roomReview'] . "</dd>" . PHP_EOL;
    echo '</dl></fieldset>' . PHP_EOL;
  } else
    echo "<h5>No Booking found! Possibly Deleted!</h5>";
  mysqli_free_result($result);
  mysqli_close($DBC);
  ?>
  <?php
echo '</div></div>';
include "footer.php";
?>
</body>

</html>