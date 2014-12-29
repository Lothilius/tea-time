<?php

session_start();

include_once 'functions.php';
createHeader(array("style.css"), array());

    $connection = connectMySql();

    $query = "SELECT entry_Id, user_Id, title, subject, rating, description, DATE_FORMAT(datetime, '%c-%d-%Y') AS entry_date
                  FROM Entries
                  WHERE user_Id='" . $_SESSION['user_Id'] . "';";
    $result = $connection->query($query);

echo '
    <div class="main_body">
		<div class="viewTreff"><h2>Your Journal Entries</h2></div>
        <table class = "viewTreffTable">            
			<tr>
				<td class="tdName"><a>Title</a></td><td class="tdCon"><a>Subject</a></td><td class="tdCon"><a>Rating</a></td><td class="tdStatus"><a>Description</a></td><td class="tdLast"><a>Entry Date</a></td>
			</tr>';

    while ($row = $result->fetch_assoc()) {
        echo '<tr>
                  <td><a href="treff.php?entry_Id=' . $row['entry_Id'] . '">' . $row['title'] . '</a></td>
				  <td>' . $row['subject'] . '</td>
				  <td>' . $row['rating'] . '</td>
				  <td>' . $row['description'] . '</td>
				  <td>' . $row['entry_date'] . '</td>
              </tr>';
    }
	echo '
		</table>
	</div>';

    $result->free();
    $connection->close();

    


include 'footer.php';