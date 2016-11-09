<?php
include_once 'wns.php';
?>
<form action="#" method="post">
<label>Subject(optional):</label> <input type="text" name="subject" /><br><br>
<label>Message:</label> <input type="text" name="message" /><br><br>
<label>Image URL(optional):</label> <input type="text" name="image" /><br><br>
<input type="submit" name="submit" value="Submit" /><br><br>
</form>
<?php
if(isset($_POST['submit']))
{
$subject = $_POST['subject'];
$image = $_POST['image'];
$message = $_POST['message'];

//wns
if($subject=="")
	$subject = "Your App Name";
notify_wns_users($message,$image,$subject);
}
?>
