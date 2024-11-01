<html><body>
<?

if (isset($_REQUEST["enabled"])) {

	require_once('wp-config.php');
	require_once('wp-photos.config.php');
	
	$result = mysql_query("CREATE TABLE $tablephotos ( "
						 ."ID int(11) NOT NULL auto_increment, "
						 ."post_ID int(11) NOT NULL default '0', "
						 ."photo varchar(100) NOT NULL default '', "
						 ."thumb varchar(100) default NULL, "
						 ."caption text, "
						 ."photographer varchar(50) default NULL, "
						 ."preview smallint(6) NOT NULL default '0', "
						 ."post smallint(6) NOT NULL default '0', "
						 ."PRIMARY KEY (ID) "
						 .")"
						 );
	
	if ($result == false) {
		print("Whoops, some error occured. Bummer, huh?");
	}
	else {
		print("Ok, the photos table should be all set.");
	}

}
else {
	print('Click here to <a href="'.$PHP_SELF.'?enabled=1">create the '.$tablephotos.' table</a>.');
}
?>
</body></html>