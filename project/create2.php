<?php
session_start();
include_once 'functions.php';

createHeader(array("style.css"), array(getGoogleMapsJSFilePath(), "validate.js","//ajax.googleapis.com/ajax/libs/dojo/1.10.3/dojo/dojo.js"));

$connect = connectMySql();

$result = $connect->query("SELECT * FROM Users WHERE user_Id=" . $_SESSION["user_Id"]);

$row = $result->fetch_assoc();



echo '
<div class="main_body">
	<div class="information">
		<h1>Tell me about your day.</h1>
		<form id="entry_form" action="process.php" method="POST" onsubmit="return validateTreff();">
			<table>
                <tr>
					<td><input type="text" name="title" maxlength="50" placeholder="Journal Title"/></td>
				</tr>
				<tr>
					<td><input type="text" name="subject" maxlength="50" size="37" placeholder="Subject"/></td>
				</tr>
				<tr>
					<td>
					    Awesome! <input type="radio" name="rating" value="0">
                    </td>
				</tr>
                <tr>
					<td>
                        So so :/ <input type="radio" name="rating" value="1">
                    </td>
				</tr>
                <tr>
					<td>
					    Terrible >:( <input type="radio" name="rating" value="2">
                    </td>
				</tr>
                <tr>
					<td>
					    <textarea id="myarea" placeholder="...was it a good day?"></textarea>
					</td>
				</tr>
			</table>
        
    </div>
    <div class="createTreff">
        <input class="createButton" type="submit" value="Log Your Day!" name="create" />
    </div>
	</form>
</div> <!-- End of main_body -->';

$result->free();
$connect->close();

include 'footer.php';
