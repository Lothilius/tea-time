<?php
session_start();
include_once 'functions.php';

$js_script = "require(['dijit/form/Textarea', 'dojo/domReady!'], function(Textarea){
    var textarea = new Textarea({
        name: 'description',
        value: 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.',
        style: 'width: 300px; height: 100px;'
    }, 'myarea').startup();
});";
createHeader(array("style.css"), array(getGoogleMapsJSFilePath(), "validate.js","//ajax.googleapis.com/ajax/libs/dojo/1.10.3/dojo/dojo.js", $js_script));


$connect = connectMySql();

$result = $connect->query("SELECT * FROM Users WHERE userId=" . $_SESSION["userId"]);

$row = $result->fetch_assoc();



echo '
<div class="main_body">
	<div class="information">
		<h1>Tell me about your day.</h1>
		<form id="entry_form" action="process.php" method="POST" onsubmit="return validateTreff();">
			<table>
                <tr>
					<td><input type="text" name="Title" maxlength="50" placeholder="Journal Title"/></td>
				</tr>
				<tr>
					<td><input type="text" name="Subject" maxlength="50" size="37" placeholder="Subject"/></td>
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
