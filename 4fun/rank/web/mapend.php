<?php
require_once 'inc/func.php';
require_once 'geoip2.phar';
use GeoIp2\Database\Reader;

$style = isset($_GET['style']) ? $_GET['style'] : '';
$player = isset($_GET['player']) ? $_GET['player'] : '';

$mapend_css = $style ? 'css/mapend.css' : 'css/mapend_nonsteam.css';
$sql_user = DB::run('SELECT `Map Kills`, `Map Deaths`, `Map XP` FROM '.htmlspecialchars($_GET['db_table3']).' WHERE Player = ?', [$player]);
$user = $sql_user->fetch(PDO::FETCH_ASSOC);

$sql_mapinfo = DB::run('SELECT `Map Name`, `Team Win` FROM '.htmlspecialchars($_GET['db_table3']).'');
$mapstats = $sql_mapinfo->fetch();

$sql_id = DB::run('SELECT Player FROM '.htmlspecialchars($_GET['db_table1']).' WHERE Player = ?', [$player]);
$id = $sql_id->fetch();

$team = '7';
$team_name = 'Match Draw';

if(isset($mapstats['Team Win'])) {
	switch($mapstats['Team Win']) {
	    case 0:
	        $team = 5;
	        $team_name = 'Terrorists Win';
	        break;
	    case 1:
	        $team = 6;
	        $team_name = 'Counter-Terrorists Win';
	        break;
	    case 2:
	        $team = 7;
	        $team_name = 'Match Draw';
	        break;
	}
}

echo '
<!DOCTYPE html>
<meta charset="utf-8"/>
<link rel="stylesheet" href="'.$mapend_css.'" />
<table id="t2">
    <tr>
        <th id="th1">
            Match End Statistics
            <hr id="hr1" />
            </hr>
            <tr>
                <th id="th2">
                    Map:
                    <div id="th3">
                        '.(isset($mapstats['Map Name']) ? $mapstats['Map Name'] : 'n/a').'
                    </div>
                    <hr id="hr0"></hr>
                </th>
            </tr>
        </th>
    </tr>
</table>
<table id="t4">
    <tr>
        <th id="th'.$team.'">'.$team_name.'</th>
    </tr>
</table>
<table id="t1">
    <tr>
        <th>#</th>
        <th id="t">Top Players of the Match</th>
        <th id="k"></th>
        <th id="d"></th>
        <th id="kd"></th>
        <th id="m"></th>
        <th id="r">Rank</th>
    </tr>';

$sql_mapstats = DB::run('SELECT `Player`, Nick, `Steam ID`, IP, `Map Kills`, `Map Deaths`, `Map MVP`, `Map XP`, Level, Flags, Online, New, Steam, Avatar FROM '.htmlspecialchars($_GET['db_table3']).' ORDER BY `Map Kills` DESC, `Map Deaths` ASC, `Map MVP` DESC LIMIT 5')->FetchAll(PDO::FETCH_ASSOC);
$i = 1;
$table = false;

foreach ($sql_mapstats as $mapstats) { 
    if ($table) {
        $tr = '<tr id="b">';
        $table = false;
    } else {
        $tr = '<tr>';
        $table = true;
    }
    if ($id['Player'] == $mapstats['Player']) {
        $tr = '<tr id="i">';
    }

	switch($i) {
		case 1: $td = '<td id=z></td>'; break;
		case 2: $td = '<td id=w></td>'; break;
		case 3: $td = '<td id=y></td>'; break;
		default: $td = '<td id=p>'.$i.'</td>'; break;
	}

	$avatar = '';
	$steam = '';
	$new = '';
	$color = $default_name_color;
	$level = $mapstats['Level'] + 1;
	$reader = new Reader('GeoLite2-City.mmdb');

	try {
	    $record = $reader->city($mapstats['IP']);
	    $flag = strtolower($record->country->isoCode);
	} catch (GeoIp2\Exception\AddressNotFoundException $e) {
	    $flag = 'nn';
	}
	
	if($mapstats['Steam']) {
		$steam = '<img src="'.$main_url.'css/img/icon-steam.png" id=steam></img>';
		$avatar = '<img src="'.$mapstats['Avatar'].'" width=15px></img>';
	}

	if($mapstats['New']) $new = '<img src="'.$main_url.'css/img/icon-new.png" id=new></img>';

	$kd_ratio = 0.0;
	if($mapstats['Map Kills'] && $mapstats['Map Deaths'])
		$kd_ratio = floatval($mapstats['Map Kills']) / floatval($mapstats['Map Deaths']);

	foreach ($name_colors as $n_color) {
	    $flags = str_split($mapstats['Flags']);
	    $color_flags = str_split($n_color['flags']);
	    if (count(array_intersect($flags, $color_flags)) == count($color_flags)) {
	        $color = $n_color['color'];
	        break;
	    }
	}

	$user_url = ''.$main_url.'user.php?player='.$mapstats['Player'].'&me='.$player.'&style='.$style.'&top=15&show=1&default_order='.htmlspecialchars($_GET['default_order']).'&db_table1='.htmlspecialchars($_GET['db_table1']).'&db_table2='.htmlspecialchars($_GET['db_table2']).'&db_table3='.htmlspecialchars($_GET['db_table3']).'';
	$new_user_url = str_replace(' ', '%20', $user_url);

	if($color != $default_name_color) {
		echo '<style>.glow'.$i.' { text-shadow: 1px 1px 6px '.$color.'; }</style>';
	}

	echo '
		'.$tr.'
		    '.$td.'
		    <td>
	            <div id=sp><div id=o'.$mapstats['Online'].'><div id='.$flag.'>'.$avatar.'<a href="'.$new_user_url.'" style="color:'.$color.'" class="glow'.$i.'">'.$mapstats['Nick'].'</a>'.$steam.''.$new.'</a></div></div></div>
	            <td id=a><a>'.$mapstats['Map Kills'].'</a></td>
	            <td id=a><a>'.$mapstats['Map Deaths'].'</td>
	            <td id=a><a>'.number_format((float)$kd_ratio, 1, '.', '').'</td>
	            <td id=a><a>★<div id=s>'.$mapstats['Map MVP'].'</div></a></td>
	            <td id="r'.$level.'"></td>
		    </td>
		</tr>
	';
	$i++;
}?>
</table>
<?php
$asset = isset($user['Map XP']) && $user['Map XP'] >= 0 ? '+' : '';

echo '
	<hr id="hr2" />
		<div id="t5">
			<table id="t3">
			    <tr>
			        <th id="th4">
			            Your stats for<th id="th4"><a>this match</a></th>
			            <tr>
			                <td id="td2">
			                    Total Kills
			                    <td id="td3">
			                        '.(isset($user['Map Kills']) ? $user['Map Kills'] : '').'
			                        <tr>
			                            <td id="td2">
			                                Total Deaths
			                                <td id="td3">
			                                    '.(isset($user['Map Deaths']) ? $user['Map Deaths'] : '').'
			                                    <tr>
			                                        <td id="td2">
			                                            Total XP Acquired
			                                            <td id="td3">'.(isset($user['Map XP']) ? $asset.$user['Map XP'].' XP' : '').'</td>
			                                        </td>
			                                    </tr>
			                                </td>
			                            </td>
			                        </tr>
			                    </td>
			                </td>
			            </tr>
			        </th>
			    </tr>
			</table>
		</div>
	</hr>
';
?>