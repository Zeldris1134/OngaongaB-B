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
  <title>Make a Booking</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>

<body>
  <?php
  include "header.php";
  include "menu.php";
  echo '<div id="site_content">';

  include "checksession.php";
  include "config.php"; //load in variables
  $DBC = mysqli_connect(DBHOST, DBUSER, DBPASSWORD, DBDATABASE);

  if (mysqli_connect_errno()) {
    echo "Error: Unable to connect to MySQL." . mysqli_connect_error();
    exit; //stop processing the page further
  }

  //function to clean input but not validate type and content
  function cleanInput($data)
  {
    return htmlspecialchars(stripslashes(trim($data)));
  }

  //on submit check if empty or not string and is submited by POST
  if (isset($_POST['submit']) and !empty($_POST['submit']) and ($_POST['submit'] == 'Add')) {
    $room = intval($_POST['rooms']);
    $checkIn = $_POST['from'];
    $checkOut = $_POST['to'];
    $contactNum = cleanInput($_POST['contactNumber']);
    $bookingExt = cleanInput($_POST['bookingExtras']);

    $error = 0;
    $msg = "Error: ";
    $in = DateTime::createFromFormat('m/d/Y', $checkIn);
    $out = DateTime::createFromFormat('m/d/Y', $checkOut);

    $checkIn = $in->format('Y-m-d');
    $checkOut = $out->format('Y-m-d');

    if ($in >= $out) {
      $error++;
      $msg .= "Check out date cannot be earlier or equal to check in date";
      $checkOut = '';
    }

    $custID = cleanInput($_SESSION['userid']);

    if ($error == 0) {
      $query = "INSERT INTO booking (checkInDate, checkOutDate, contactNumber, bookingExtras, customerID, roomID ) VALUES (?,?,?,?,?,?)";

      $stmt = mysqli_prepare($DBC, $query);
      if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssssii', $checkIn, $checkOut, $contactNum, $bookingExt, $custID, $room);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo "<h2>Booking added Successfully</h2>";
      } else {
        echo "Error: " . mysqli_error($DBC); // Add error handling for the prepare statement
      }
    } else {
      echo "<h2>$msg</h2>" . PHP_EOL;
    }
  }

  $query = 'SELECT roomID, roomname, roomtype, beds FROM room ORDER BY roomID';
  $result = mysqli_query($DBC, $query);
  $rowcount = mysqli_num_rows($result);
  ?>
  <h1>Make a Booking</h1>
  <h2>
    <a href="currentBookings.php">[Return to the Bookings listing]</a>
    <a href="/bnb_assignment3">[Return to the main page]</a>
  </h2>
  <h2>Booking for Test</h2>
  <form method="POST">
    <div>
      <label for="rooms">Room (name,type,beds):</label>
      <select id="rooms" name="rooms">
        <?php
        if ($rowcount > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['roomID'];
            ?>
            <option value="<?php echo $row['roomID'] ?>">
              <?php
              echo $row['roomname'] . ' ' .
                $row['roomtype'] . ' ' .
                $row['beds']
                ?>
            </option>
            <?php
          }
        } else {
          echo "<option>NO rooms available</option>";
        }
        mysqli_free_result($result);
        ?>
      </select>
    </div>

    <br />
    <div>
      <label for="from">Check in Date:</label>
      <input type="text" id="from" name="from" required />
    </div>
    <br />
    <div>
      <label for="to">Check out Date:</label>
      <input type="text" id="to" name="to" required />
    </div>
    <br />
    <div>
      <label for="contactNumber">Contact Number:</label>
      <input type="tel" placeholder="(###) ### ####" pattern="\([0-9]{3}\) [0-9]{3} [0-9]{4}" id="contactNumber" name="contactNumber"
        required />
    </div>
    <br />
    <div>
      <label for="bookingExtras">Booking Extras:</label>
      <textarea name="bookingExtras" id="bookingExtras" cols="30" rows="6"></textarea>
    </div>
    <br />
    <div>
      <input type="submit" name="submit" value="Add" />
      <a href="currentBookings.php">[cancel]</a>
    </div>
  </form>
  <br />
  <br />
  <hr />
  <h2>Search for room availability</h2>
  <div>
    <form id="searchForm" method="POST" name="searching">
      <label for="fromDate">Start Date:</label>
      <input type="text" id="fromDate" name="fromDate" required placeholder="From Date" />
      <label for="toDate">End Date:</label>
      <input type="text" id="toDate" name="toDate" required placeholder="To Date" />
      <input type="submit" value="Search Availability" />
    </form>
  </div>
  <br>
  <table id="result" border="1">
    <thead>
      <tr>
        <th>Room #</th>
        <th>Room Name</th>
        <th>Room Type</th>
        <th>Beds</th>
      </tr>
    </thead>
  </table>
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
  });

  $(document).ready(function () {
    $("#fromDate").datepicker({ dateFormat: 'yy-mm-dd' });
    $("#toDate").datepicker({ dateFormat: 'yy-mm-dd' });

    $('#searchForm').submit(function (event) {
      console.log("Form submitted");
      var formData = {
        sqa: $('#fromDate').val(),
        sqb: $('#toDate').val()
      };
      $.ajax({
        type: "POST",
        url: "bookingsearch.php",
        data: formData,
        dataType: "json",
        encode: true,

      }).done(function (data) {
        var tbl = document.getElementById("result");
        var rowCount = tbl.rows.length;

        for (var i = 1; i < rowCount; i++) {
          tbl.deleteRow(1)
        }

        for (var i = 0; i < data.length; i++) {
          var rid = data[i]['roomID'];
          var rn = data[i]['roomname'];
          var rt = data[i]['roomtype'];
          var bd = data[i]['beds'];

          tr = tbl.insertRow(-1);
          var tabCell = tr.insertCell(-1);
          tabCell.innerHTML = rid;
          var tabCell = tr.insertCell(-1);
          tabCell.innerHTML = rn;
          var tabCell = tr.insertCell(-1);
          tabCell.innerHTML = rt;
          var tabCell = tr.insertCell(-1);
          tabCell.innerHTML = bd;
        }
        console.log("Data received from server:", data);
      });
      event.preventDefault();
    })
  })
</script>

</html>