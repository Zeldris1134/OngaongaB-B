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

  <title>Current bookings</title>
</head>

<body>
  <?php
  include "header.php";
  include "menu.php";
  echo '<div id="site_content">';
  include "sidebar.php";

  include "config.php";
  $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

  if (mysqli_connect_errno()) {
    echo 'Failed to connect: ' . mysqli_connect_error();
    exit;
  }
  $query = 'SELECT booking.bookingID, room.roomname, booking.checkInDate, booking.checkOutDate, customer.lastname, customer.firstname
      FROM booking
      INNER JOIN customer ON booking.customerID = customer.customerID
      INNER JOIN room ON booking.roomID = room.roomID 
      ORDER BY booking.bookingID ASC';
  $result = mysqli_query($DBC, $query);

  if ($result === false) {
    echo 'Query error: ' . mysqli_error($DBC);
  } else {
    $rowcount = mysqli_num_rows($result);
  }
  // $rowcount = mysqli_num_rows($result);
  ?>
  <h1>Current bookings</h1>

  <h2>
    <a href="makeBooking.php">[Make a booking]</a>

    <a href="/bnb_assignment3">[Return to main page]</a>
  </h2>

  <table border="1">
    <tr>
      <th><strong>Booking (room, dates)</strong></th>

      <th><strong>Customer</strong></th>

      <th><strong>Action</strong></th>
    </tr>
    <?php
    if ($rowcount > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['bookingID'];
        $rn = $row['roomname'];
        $firstName = $row['firstname'];
        $lastName = $row['lastname'];
        $sql = 'SELECT checkInDate, checkOutDate FROM booking WHERE bookingID = ' . $id;

        $res = mysqli_query($DBC, $sql);
        $rowc = mysqli_num_rows($res);

        if ($rowc > 0) {
          $rowr = mysqli_fetch_assoc($res);
        }

        echo '<tr>
            <td>' . $rn . ', ' . $row['checkInDate']
          . ' - ' . $row['checkOutDate'] . '</td>';

        echo '<td>' . $firstName . ' ' . $lastName . '</td>';

        echo '<td><a href="bookingDetails.php?id=' . $id . '">[view]</a>';
        echo '<a href="editBooking.php?id=' . $id . '">[edit]</a>';
        echo '<a href="editAddReview.php?id=' . $id . '">[manage reviews]</a>';
        echo '<a href="deleteBooking.php?id=' . $id . '">[delete]</a></td>';
        echo '</tr>' . PHP_EOL;

        mysqli_free_result($res);

      }
    } else
      echo "<h2>No bookings found!</h2>";
    mysqli_free_result($result);
    mysqli_close($DBC);
    ?>
  </table>
  <?php
  echo '</div></div>';
  include "footer.php";
  ?>
</body>

</html>