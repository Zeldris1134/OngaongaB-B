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
    <title>Delete Booking</title>
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

      include "config.php";
      $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

      if (mysqli_connect_errno()) {
        echo "Error:Unable to connect to MySql." . mysqli_connect_error();
        exit; //stop processing the page further.
      }

      function cleanInput($data)
      {
        return htmlspecialchars(stripslashes(trim($data)));
      }

      //check if id exists
      if ($_SERVER["REQUEST_METHOD"] =="GET"){
        $id = $_GET['id'];
        if (empty($id) or !is_numeric($id)){
          echo "<h2>Invalid booking id</h2>";
          exit;
        }
      }

      if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Delete')) {
        $error = 0;
        $msg = "Error:";

        if (isset($_POST['id']) and !empty($_POST['id']) and is_integer(intval($_POST['id']))) {
            $id = cleanInput($_POST['id']);
        }else{
            $error++;
            $msg .="Invalid Booking iD";
            $id = 0;
        }
        if($error == 0 and $id >0){
            $query = "DELETE FROM booking WHERE bookingID=?";
            $stmt = mysqli_prepare($DBC,$query);
            mysqli_stmt_bind_param($stmt, 'i',$id);

            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo "<h2>Booking deleted</h2>";
        }else{
            echo "<h2>$msg</h2>".PHP_EOL;
        }
      }

      $query = 'SELECT booking.bookingID, room.roomname, booking.checkInDate, booking.checkOutDate FROM booking
      INNER JOIN room ON booking.roomID = room.roomID WHERE bookingID=' . $id;

      $result = mysqli_query($DBC, $query);
      $rowcount = mysqli_num_rows($result);

    ?>
    <h1>Booking Details View</h1>
    <h2>
      <a href="currentBookings.php">[Return to the booking listing]</a>
      <a href="/bnb_assignment3">[Return to the main page]</a>
    </h2>
    <?php 
      if ($rowcount > 0) {
        echo "<fieldset><legend>Booking Detail #$id</legend><dl>";
        $row = mysqli_fetch_assoc($result);
        $id = $row['bookingID'];

        echo "<dt>Room Name:</dt><dd>" . $row['roomname'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkin Date:</dt><dd>" . $row['checkInDate'] . "</dd>" . PHP_EOL;
        echo "<dt>Checkout Date:</dt><dd>" . $row['checkOutDate'] . "</dd>" . PHP_EOL;
        echo '</dl></fieldset>' . PHP_EOL;
      
    ?>    
        <form method="POST" action="deleteBooking.php">
          <h2>Are you sure you want to delete this Booking?</h2>
          <input type="hidden" name="id" value="<?php echo $id; ?>">
          <input type="submit" name="submit" value="Delete" />
          <a href="currentBookings.php">[Cancel]</a>
        </form>
      <?php
      } else echo "<h5> No booking found! Possibly deleted!</h5>";
      mysqli_free_result($result);
      mysqli_close($DBC);
      ?>
      <?php
      echo '</div></div>';
      include "footer.php";
      ?>
  </body>
</html>
