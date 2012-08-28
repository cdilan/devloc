<?php

/*

 Plugin Name: Backup and Move Plugin
 Plugin URI: http://www.logiclord.com/backup-and-move-plugin/
 Description: This plugin will create complete backup of your wordpress installation in a zip file which can be easily used to recover from a crash or simply shift to a new server.
 Author: Gaurav Aggarwal
 Version: 0.1
 Author URI: http://www.logiclord.com/

*/

set_time_limit (60*60);
 
function full_copy( $source , $target ) {
	if ( is_dir( $source ) ) {
		@mkdir( $target );
		$d = dir( $source );
		while ( FALSE !== ( $entry = $d->read() ) ) {
			if ( $entry == '.' || $entry == '..' ) {
				continue;
			}
			$Entry = $source . '/' . $entry;
			if ( is_dir( $Entry ) ) {
				full_copy( $Entry, $target . '/' . $entry );
				continue;
			}
			copy( $Entry, $target . '/' . $entry );
		}

		$d->close();
	}else {
		copy( $source, $target );
	}
}

function deleteAll($directory, $empty = false) {
	if( substr( $directory , -1 ) == "/") {
		$directory = substr( $directory , 0 , -1 );
	}

	if( ! file_exists($directory) || !is_dir($directory) ) {
		return false;
	} elseif(!is_readable($directory)) {
		return false;
	} else {
		$directoryHandle = opendir($directory);
			
		while ($contents = readdir($directoryHandle)) {
			if($contents != '.' && $contents != '..') {
				$path = $directory . "/" . $contents;
				if(is_dir($path)) {
					deleteAll($path);
				} else {
					unlink($path);
				}
			}
		}
		closedir($directoryHandle);
		if( $empty == false) {
			if( ! rmdir($directory) ) {
				return false;
			}
		}
		return true;
	}
}

function RemoveExtension($strName)	{
	$ext = strrchr($strName, '.');
	if($ext !== false)	{
		$strName = substr($strName, 0, -strlen($ext));
	}
	return $strName;
}


$dbhost =isset( $_POST['dbhost']) ? trim( $_POST['dbhost'] ) :null;
$dbname = isset( $_POST['dbname']) ? trim( $_POST['dbname'] ) :null;
$dbuser = isset( $_POST['dbuser']) ? trim( $_POST['dbuser'] ) : null;
$dbpassword = isset( $_POST['dbpassword']) ? trim( $_POST['dbpassword'] ) : null;
$old_domain = isset( $_POST['purl']) ? trim( $_POST['purl'] ) : null;
$new_domain = isset( $_POST['nurl']) ? trim( $_POST['nurl'] ) : null;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Restore - Backup and move plugin wordpress</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #CCC;
}

#content {
	position: relative;
	width: 700px;
	height: 620px;
	z-index: 1;
	background-color: #FFF;
	top: 0px;
	border-top-width: 0px;
	border-right-width: 2px;
	border-bottom-width: 0px;
	border-left-width: 2px;
	border-top-style: solid;
	border-right-style: solid;
	border-bottom-style: solid;
	border-left-style: solid;
	border-top-color: #666;
	border-right-color: #666;
	border-bottom-color: #666;
	border-left-color: #666;
	font-family: Verdana, Geneva, sans-serif;
}

#apDiv1 {
	position: absolute;
	width: 446px;
	height: 32px;
	z-index: 1;
	top: 15px;
	left: 50px;
}

body,td,th {
	font-size: 14px;
	color: #000;
}

a {
	text-decoration: none;
	color: #30C;
}

#apDiv2 {
	position: absolute;
	width: 200px;
	height: 115px;
	z-index: 1;
}

#instructions {
	position: absolute;
	width: 80% px;
	height: 140px;
	z-index: 1;
}

smalll {
	font-size: 9px;
	font-variant: small-caps;
}

#apDiv3 {
	position: absolute;
	width: 80%;
	height: 500px;
	z-index: 2;
	top: 70px;
	padding: 20px;
}
</style>
</head>

<body>

	<div align="center">

		<div id="content" align="center">
			<div id="apDiv1" align="left">
				<a href="http://logiclord.com" style="font-weight: bold"><img
					style="border: 0" src="http://logiclord.com/wp/logo.png"
					title="Backup And Move Plugin for wordpress" /> </a>

			</div>
			<div id="apDiv3">
			<?php
			if($dbhost && $dbuser && $dbuser && $old_domain && $new_domain)
			{
				$zip_name='';
				$folder_name='';
				echo "<div align=\"left\">";
				if( file_exists('wp-config.php') )	{
					die('Error !! a previous installation found.');
				}
				$con = mysql_connect( $dbhost , $dbuser , $dbpassword );
				if ( ! $con )	{
					die('Error !! Could not connect to databse: ' . mysql_error());
				}
				mysql_select_db($dbname, $con) or   die('Error !! Could not connect to databse: ' . mysql_error());
				echo '<h3>restoring please wait it may take few minutes .....</h3>';
				foreach (glob("*.zip") as $filename) {
					$zip_name = $filename;
					$folder_name = RemoveExtension( $zip_name );
				}
				if($filename==null)	{
					die('Error !! backup zip not found');
				}
				echo '<h3>Extracting files.</h3>';
				$zip = new ZipArchive;
				$source = dirname(__FILE__). '/' . $folder_name;
				$destination = dirname(__FILE__) ;
				if ($zip->open($zip_name) === TRUE)	{
					$zip->extractTo( dirname(__FILE__) );
					if(!file_exists($folder_name))	 	{
						die('Error !! Backup file is tempered');
					}
					full_copy($source,$destination);
					deleteAll($source);
					$zip->close();
				} else {
					echo 'failed in extracting';
				}

				$file = file_get_contents('wp-config.php', true);
					
				$patterns = array ("/'DB_NAME'((.*)*)'/",
                   "/'DB_USER'((.*)*)'/",
					"/'DB_PASSWORD'((.*)*)'/",
					"/'DB_HOST'((.*)*)'/");
					
				$replace = array ("'DB_NAME', ".'\''.$dbname.'\'',
                   "'DB_USER', ".'\''.$dbuser.'\'',
					"'DB_PASSWORD', ". '\''.$dbpassword.'\'',
					"'DB_HOST', ". '\''.$dbhost.'\'');
					
				echo '<h3>Creating configuration files.</h3>';
				
				$file = preg_replace($patterns, $replace, $file);
				$file = str_replace( $old_domain, $new_domain, $file );
				file_put_contents( 'wp-config.php' , $file );
				$file = utf8_decode( file_get_contents( 'database.sql' , true ) );
				$file = str_replace( $old_domain , $new_domain , $file );
				$res = explode(";\n", $file);
				$len = count($res);
				$temp = 0;
				echo '<h3>Importing database.</h3>';
				while ( $temp < $len )	 {
					mysql_query($res[$temp]);
					$temp++;
				}
				$sql="SHOW TABLES LIKE '%backup_and_move'";
				$backup_table=mysql_fetch_row(mysql_query($sql));
				$backup_table=$backup_table[0];
				mysql_query('Delete  from '.$backup_table);
				mysql_query("INSERT INTO wp_backup_and_move (bid, bfile,backup_created_on,backup_created_by) VALUES (1, '".$zip_name."','".date( "Y:m:d G:i:s",time())."','backup and move plugin')");
				mysql_close( $con );
				if(!file_exists('wp-backups'))	{
					mkdir('wp-backups',0755);
				}
				$ourFileHandle = fopen( 'wp-backups\index.php' , 'w' );
				fclose( $ourFileHandle );
				copy( $zip_name , 'wp-backups/' . $zip_name );
				echo '<h3>Removing temporary files.</h3>';
				unlink( $zip_name );
				unlink( 'database.sql' );
				echo '<h3>Process completed.</h3> <br> Delete restore.php to complete process.</div>';
			}
			else
			{
				?>
				<h3>Please provide following details :</h3>
				<form action="restore.php" method="post">
					<table width="80%" border="0" cellspacing="5" cellpadding="5">
						<tr>
							<td>Previous url (ex: logiclord.com/blog)</td>
							<td><label for="purl"></label> <input type="text" name="purl"
								id="purl" /></td>
						</tr>
						<tr>
							<td>New url (ex: logiclord.com/newblog)</td>
							<td><input type="text" name="nurl" id="nurl" /></td>
						</tr>
						<tr>
							<td>Database host</td>
							<td><input type="text" name="dbhost" id="dhost" /></td>
						</tr>
						<tr>
							<td>Database user</td>
							<td><input type="text" name="dbuser" id="duser" /></td>
						</tr>
						<tr>
							<td>Database name</td>
							<td><input type="text" name="dbname" id="dname" /></td>
						</tr>
						<tr>
							<td>Database password</td>
							<td><input type="text" name="dbpassword" id="dpassword" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" name="submit" id="submit"
								value="Restore" /></td>
						</tr>
					</table>
				</form>
				<?php
			}
			?>
				<br />
				<div align="left" id="instructions">
					<span id="internal-source-marker_0.07237507391823417"><strong
						style="color: #F00">Important Instructions</strong> </span> :-
					<ul>
						<li>All the fields are required.</li>
						<li>Make sure you have already placed your backup file in same
							directory as restore.php.</li>
						<li>Double check all database details.</li>
						<li>Database should already be created.</li>
						<li>In case of an error try again after deleting all files and
							directories except backup .zip file and restore.php.</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
