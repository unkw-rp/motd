<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('inc/config.php');
$frame = 0; if(isset($_GET["frame"])) $frame = $_GET["frame"];
?>

<!DOCTYPE html>
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href='css/main.css' />
<html>
  <head>
    <title><?= $main_name ?></title>
  </head>
  <body>
    <div id="hd">
      <img src='css/img/site/statistics.png' style="float: left; margin-right: 10px; width:30px;">
      <h2><a href="<?=$main_url?>"><?=$main_name?></a>
    <a href="<?=$main_site?>"><button class="button">Back to site</button></a>
  </div>
  <?php
  if(!$frame) {
      echo '
    <table>
      <tr id=a>
        <th>Status</th>
        <th>Name</th>
        <th>IP</th>
        <th>Players</th>
        <th>Map</th>
      </tr>';

      require_once('lgsl/lgsl_files/lgsl_class.php');
      foreach ($servers as $server) {
        $server_ip = $server['ip'];
        $mysql_table1[$server_ip] = $server['mysql_table1'];
        $mysql_table2[$server_ip] = $server['mysql_table2'];

        $result = lgsl_query_live('halflife', $server_ip, $server['port'], $server['port'], 0, "sep");
        if($result['b']['status']) {
            $players = $result['s']['players'] . '/' . $result['s']['playersmax'];
            $map_image = '<img class="map-image" src="https://image.gametracker.com/images/maps/160x120/cs/' . strtolower($result['s']['map']) . '.jpg" alt="' . $result['s']['map'] . '" />';
            $server_name = $result['s']['name'];
            echo '<tr>';
            echo '<td id=on></td>';
            echo '<td><a href='.$main_url.'?frame=1&db_table1='.$mysql_table1[$server_ip].'&db_table2='.$mysql_table2[$server_ip].'>' . $server_name . '</a></td>';
            echo '<td>' . $server_ip . ':' . $server['port'] . '</td>';
            echo '<td>' . $players . '</td>';
            echo '<td>' . $map_image . '</td>';
            echo '</tr>';
        } else {
            $server_name = $server_ip . ':' . $server['port'];
            echo '<tr>';
            echo '<td id=off></td>';
            echo '<td>' . $server_name . '</td>';
            echo '<td>' . $server_ip . ':' . $server['port'] . '</td>';
            echo '<td>N/A</td>';
            echo '<td>N/A</td>';
            echo '</tr>';
        }
    }
      echo '</table>';
    } else {
      echo '<iframe src='.$main_url.'top15.php?top=15&player=&style=1&order='.$default_order.'&default_order='.$default_order.'&db_table1='.htmlspecialchars($_GET['db_table1']).'&db_table2='.htmlspecialchars($_GET['db_table2']).'&search=';
    }
  ?>
  </body>
</html>