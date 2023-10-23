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
  <title>Edit/Add Room Review</title>
</head>

<body>
  <?php
  include "header.php";
  include "menu.php";
  echo '<div id="site_content">';

  include "checksession.php";
  include "config.php";
  $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

  //check if the connection was good
  if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
    exit; //stop processing the page further
  }

  //function to clean input but not validate type and content
  function cleanInput($data)
  {
    return htmlspecialchars(stripslashes(trim($data)));
  }

  //check if id exists
  if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $id = $_GET['id'];
    if (empty($id) or !is_numeric($id)) {
      echo "<h2>Invalid booking id</h2>";
      exit;
    }
  }
  //on submit check if empty or not string and is submited by POST
  if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Update')) {

    $roomReview = cleanInput($_POST['roomReview']);
    $id = intval($_POST['id']);

    $upd = "UPDATE booking SET roomReview=? WHERE bookingID=?";

    $stmt = mysqli_prepare($DBC, $upd);
    mysqli_stmt_bind_param($stmt, 'si', $roomReview, $id);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    //print message
    echo "<h5>Room Review added to Booking</h5>";
  }

  $query = 'SELECT booking.bookingID, booking.roomReview FROM booking WHERE bookingID=' . $id;
  $result = mysqli_query($DBC, $query);

  if (!$result) {
    die("Query failed: " . mysqli_error($DBC));
  }

  // Fetch the row from the query result
  $row = mysqli_fetch_assoc($result);

  ?>

  <h1>Edit/add room review</h1>
  <h2>
    <a href="currentBookings.php">[Return to the Bookings listing]</a>
    <a href="/bnb_assignment3">[Return to the main page]</a>
  </h2>
  <h2>Review made by <?php echo $_SESSION['username']?></h2>
  <form method="POST">
    <div>
      <label for="roomReview">Room Review:</label>
      <textarea name="roomReview" id="roomReview" cols="30" rows="6"><?php echo $row['roomReview'] ?></textarea>
    </div>
    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
    <br />
    <input type="submit" name="submit" value="Update" />
  </form>
  <?php
  echo '</div></div>';
  include "footer.php";
  ?>
</body>

</html>