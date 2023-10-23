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
  <title>Edit a Booking</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
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

    $room = intval($_POST['rooms']);
    $checkIn = $_POST['from'];
    $checkOut = $_POST['to'];
    $contactNum = cleanInput($_POST['contactNumber']);
    $bookingExt = cleanInput($_POST['bookingExtras']);
    $id = intval($_POST['id']);

    $in = DateTime::createFromFormat('m/d/Y', $checkIn);
    $out = DateTime::createFromFormat('m/d/Y', $checkOut);

    $checkIn = $in->format('Y-m-d');
    $checkOut = $out->format('Y-m-d');

    $custID = cleanInput($_SESSION['userid']);

    $upd = "UPDATE `booking` SET roomID=?, checkInDate=?, checkOutDate=?, 
    contactNumber=?, bookingExtras=? WHERE bookingID=?";

    $stmt = mysqli_prepare($DBC, $upd); //prepare the query
    mysqli_stmt_bind_param($stmt, 'issssi', $room, $checkIn, $checkOut, $contactNum, $bookingExt, $id);

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    //print message
    echo "<h5>Booking updated successfully</h5>";
  }

  $query = 'SELECT booking.bookingID, room.roomID, room.roomname, room.roomtype, room.beds, booking.checkInDate, booking.checkOutDate, booking.contactNumber, booking.bookingExtras FROM booking 
  INNER JOIN room ON booking.roomID = room.roomID WHERE bookingID=' . $id;

  $result = mysqli_query($DBC, $query);

  if (!$result) {
    die("Query failed: " . mysqli_error($DBC));
  }

  $rowcount = mysqli_num_rows($result);

  ?>
  <h1>Edit a Booking</h1>
  <h2>
    <a href="currentBookings.php">[Return to the Bookings listing]</a>
    <a href="/bnb_assignment3">[Return to the main page]</a>
  </h2>
  <h2>Booking made for <?php echo $_SESSION['username']?></h2>
  <form method="POST">
    <div>
      <label for="rooms">Room (name,type,beds):</label>
      <select id="rooms" name="rooms">
        <?php
        if ($rowcount > 0) {
          $row = mysqli_fetch_assoc($result);
          ?>
          <option value="<?php echo $row['roomID']; ?>">
            <?php echo $row['roomname'] . ' ' .
              $row['roomtype'] . ' ' .
              $row['beds'] ?>
          </option>

          <?php
        } else
          echo "<option>No Rooms found</option>";
        ?>
      </select>
    </div>
    <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" />
    <br />
    <div>
      <label for="from">Check in Date:</label>
      <input type="text" id="from" name="from" required
        value="<?php echo date('m/d/Y', strtotime($row['checkInDate'])); ?>" />
    </div>
    <br />
    <div>
      <label for="to">Check out Date:</label>
      <input type="text" id="to" name="to" required
        value="<?php echo date('m/d/Y', strtotime($row['checkOutDate'])); ?>" />
    </div>
    <br />
    <div>
      <label for="contactNumber">Contact Number:</label>
      <input type="tel" placeholder="(###) ### ####" pattern="\([0-9]{3}\) [0-9]{3} [0-9]{4}" name="contactNumber"
        required value="<?php echo $row['contactNumber'] ?>" />
    </div>
    <br />
    <div>
      <label for="bookingExtras">Booking Extras:</label>
      <textarea name="bookingExtras" id="bookingExtras" cols="30" rows="6"><?php echo $row['bookingExtras'] ?></textarea>
    </div>
    <br />
    <div>
      <input type="submit" name="submit" value="Update" />
      <a href="currentBookings.php">[cancel]</a>
    </div>
  </form>
  <?php
  mysqli_free_result($result);
  mysqli_close($DBC);
  ?>
  <?php
  echo '</div></div>';
  include "footer.php";
  ?>
</body>

<script>
  $(function () {
    var dateFormat = "mm/dd/yy",
      from = $("#from")
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 2
        })
        .on("change", function () {
          to.datepicker("option", "minDate", getDate(this))
        }),
      to = $("#to")
        .datepicker({
          defaultDate: "+1w",
          changeMonth: true,
          numberOfMonths: 2
        })
        .on("change", function () {
          from.datepicker("option", "maxDate", getDate(this))
        })

    function getDate(element) {
      var date
      try {
        date = $.datepicker.parseDate(dateFormat, element.value)
      } catch (error) {
        date = null
      }

      return date
    }
  })
</script>

</html>