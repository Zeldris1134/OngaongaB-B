    <div id="header">
      <div id="logo">
        <div id="logo_text">
          <!-- class="logo_colour", allows you to change the colour of the text -->
          <h1><a href="/bnb/"><span class="logo_colour">Ongaonga Bed & Breakfast</span></a></h1>
          <h2>Make yourself at home is our slogan. We offer some of the best beds on the east coast. Sleep well and rest well.</h2>
        </div>
      </div>
      <div id="menubar">
        <ul id="menu">
          <?php
          $current_page = basename($_SERVER['PHP_SELF']); // Gets the current filename
          ?>
          <!-- put class="selected" in the li tag for the selected page - to highlight which page you're on -->
          <li <?php if ($current_page === 'index.php') echo 'class="selected"'; ?>><a href="/bnb_assignment3">Home</a></li>
          <li <?php if ($current_page === 'listrooms.php') echo 'class="selected"'; ?>><a href="listrooms.php">Rooms</a></li>
          <!-- <li><a href="/">blank</a></li> -->
          <?php
            if($_SESSION['loggedin'] == 1)
            {
              ?>
              <li <?php if ($current_page === 'listcustomers.php') echo 'class="selected"'; ?>><a href="listcustomers.php">Customers</a>
              <li <?php if ($current_page === 'currentBookings.php') echo 'class="selected"'; ?>><a href="currentBookings.php">Bookings</a>
              <?php
            } else {
              ?>
              <li <?php if ($current_page === 'registercustomer.php') echo 'class="selected"'; ?>><a href="registercustomer.php">Register</a></li>
              <li <?php if ($current_page === 'login.php') echo 'class="selected"'; ?>><a href="login.php">Login</a></li>
              <?php
            }
        ?>
        </ul>
      </div>
    </div>

	