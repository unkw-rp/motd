<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'inc/func.php';
require_once("geoip2.phar");
use GeoIp2\Database\Reader;

$search = $_GET['search'];
$style = $_GET['style'];
$top = isset($_GET['top']) ? $_GET['top'] : 0;
$player = $_GET['player'];
$orderby = $_GET['order'];
$db_table1 = $_GET['db_table1'];
$db_table2 = $_GET['db_table2'];

$top15_css = $style ? 'css/top15.css' : 'css/top15_nonsteam.css';

if(empty($search)) {
	$count = DB::run('SELECT COUNT(*) FROM '.$db_table1.'');
} else {
	$placeholder = '%'.$search.'%';
	$count = DB::run('SELECT COUNT(*) FROM '.$db_table1.' WHERE Nick LIKE ? OR IP LIKE ? OR `Steam ID` LIKE ?', [$placeholder, $placeholder, $placeholder]);
}
$total = $count->fetch(PDO::FETCH_ASSOC);

if($top > 0) {
	if($top <= 15) $top = 15;
	else if($top > $total['COUNT(*)']) $top = $total['COUNT(*)'];
	$offset = $total['COUNT(*)'] >= 15 ? $top - 15 : 0; 
	$page = ceil($top/15);
}

$items_per_page = 15;
$total_items = $total['COUNT(*)'];
$total_pages = ceil($total_items / $items_per_page);

if(!$top) {
	$page = isset($_GET['page']) ? $_GET['page'] : 1;
	$offset = ($page - 1) * $items_per_page;
}

$num1 = '0'; if(htmlspecialchars($orderby) == 0) $num1 = '14';
$num2 = '1'; if(htmlspecialchars($orderby) == 1) $num2 = '15';
$num3 = '2'; if(htmlspecialchars($orderby) == 2) $num3 = '16';
$num4 = '3'; if(htmlspecialchars($orderby) == 3) $num4 = '17';
$num5 = '4'; if(htmlspecialchars($orderby) == 4) $num5 = '18';
$num6 = '5'; if(htmlspecialchars($orderby) == 5) $num6 = '19';
$num7 = '6'; if(htmlspecialchars($orderby) == 6) $num7 = '20';
$num8 = '7'; if(htmlspecialchars($orderby) == 7) $num8 = '21';
$num9 = '8'; if(htmlspecialchars($orderby) == 8) $num9 = '22';
$num10 = '9'; if(htmlspecialchars($orderby) == 9) $num10 = '23';
$num11 = '10'; if(htmlspecialchars($orderby) == 10) $num11 = '24';
$num12 = '11'; if(htmlspecialchars($orderby) == 11) $num12 = '25';
$num13 = '12'; if(htmlspecialchars($orderby) == 12) $num13 = '26';

$underline1 = ''; $desc1 = '';
$underline2 = ''; $desc2 = '';
$underline3 = ''; $desc3 = '';
$underline4 = ''; $desc4 = '';
$underline5 = ''; $desc5 = '';
$underline6 = ''; $desc6 = '';
$underline7 = ''; $desc7 = '';
$underline8 = ''; $desc8 = '';
$underline9 = ''; $desc9 = '';
$underline10 = ''; $desc10 = '';
$underline11 = ''; $desc11 = '';
$underline12 = ''; $desc12 = '';
$underline13 = ''; $desc13 = '';
$underline14 = ''; $desc14 = '';

switch($orderby)
{
	case 0: $order = 'XP DESC, Nick ASC'; $underline1 = 'style=text-decoration:underline;'; $desc1 = '▾'; break;
	case 1: $order = 'Nick ASC'; $underline2 = 'style=text-decoration:underline;'; $desc2 = '▾'; break;
	case 2: $order = 'Kills DESC, Nick ASC'; $underline3 = 'style=text-decoration:underline;'; $desc3 = '▾'; break;
	case 3: $order = 'Assists DESC, Nick ASC'; $underline4 = 'style=text-decoration:underline;'; $desc4 = '▾'; break;
	case 4: $order = 'Deaths DESC, Nick ASC'; $underline5 = 'style=text-decoration:underline;'; $desc5 = '▾'; break;
	case 5: $order = '`Skill Range` DESC, Nick ASC'; $underline6 = 'style=text-decoration:underline;'; $desc6 = '▾'; break;
	case 6: $order = 'Headshots DESC, Nick ASC'; $underline7 = 'style=text-decoration:underline;'; $desc7 = '▾'; break;
	case 7: $order = 'Planted DESC, Nick ASC'; $underline8 = 'style=text-decoration:underline;'; $desc8 = '▾'; break;
	case 8: $order = 'Exploded DESC, Nick ASC'; $underline9 = 'style=text-decoration:underline;'; $desc9 = '▾'; break;
	case 9: $order = 'Defused DESC, Nick ASC'; $underline10 = 'style=text-decoration:underline;'; $desc10 = '▾'; break;
	case 10: $order = '`Rounds Won` DESC, Nick ASC'; $underline11 = 'style=text-decoration:underline;'; $desc12 = '▾'; break;
	case 11: $order = 'MVP DESC, Nick ASC'; $underline12 = 'style=text-decoration:underline;'; $desc11 = '▾'; break;
	case 12: $order = 'Level DESC, XP DESC'; $underline13 = 'style=text-decoration:underline;'; $desc13 = '▾'; break;
	case 13: $order = '(Kills - Deaths) DESC, Assists DESC, Headshots DESC, MVP DESC, `Rounds Won` DESC, Planted DESC, Exploded DESC, Defused DESC, XP DESC, Nick ASC'; break;
	case 14: $order = 'XP ASC, Nick ASC'; $underline1 = 'style=text-decoration:underline;'; $desc1 = '▴'; break;
	case 15: $order = 'Nick DESC'; $underline2 = 'style=text-decoration:underline;'; $desc2 = '▴'; break;
	case 16: $order = 'Kills ASC, Nick ASC'; $underline3 = 'style=text-decoration:underline;'; $desc3 = '▴'; break;
	case 17: $order = 'Assists ASC, Nick ASC'; $underline4 = 'style=text-decoration:underline;'; $desc4 = '▴'; break;
	case 18: $order = 'Deaths ASC, Nick ASC'; $underline5 = 'style=text-decoration:underline;'; $desc5 = '▴'; break;
	case 19: $order = '`Skill Range` ASC, Nick ASC'; $underline6 = 'style=text-decoration:underline;'; $desc6 = '▴'; break;
	case 20: $order = 'Headshots ASC, Nick ASC'; $underline7 = 'style=text-decoration:underline;'; $desc7 = '▴'; break;
	case 21: $order = 'Planted ASC, Nick ASC'; $underline8 = 'style=text-decoration:underline;'; $desc8 = '▴'; break;
	case 22: $order = 'Exploded ASC, Nick ASC'; $underline9 = 'style=text-decoration:underline;'; $desc9 = '▴'; break;
	case 23: $order = 'Defused ASC, Nick ASC'; $underline10 = 'style=text-decoration:underline;'; $desc10 = '▴'; break;
	case 24: $order = '`Rounds Won` ASC, Nick ASC'; $underline11 = 'style=text-decoration:underline;'; $desc12 = '▴'; break;
	case 25: $order = 'MVP ASC, Nick ASC'; $underline12 = 'style=text-decoration:underline;'; $desc11 = '▴'; break;
	case 26: $order = 'Level ASC, XP ASC, Nick ASC'; $underline13 = 'style=text-decoration:underline;'; $desc13 = '▴'; break;
}

$sql = DB::run('SELECT Player FROM '.$db_table1.' WHERE Player = ?', [$player]);
$id = $sql->fetch(PDO::FETCH_ASSOC);

$default_order = htmlspecialchars($_GET['default_order']);

echo '
<!DOCTYPE html>
<meta charset="utf-8"><link rel="stylesheet" href='.$top15_css.' />
<table>
    <tr id="a">
        <th id=top><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order=13&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'">#</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num2.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline2.'>Name'.$desc2.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num3.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline3.'>Kills'.$desc3.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num4.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline4.'>Assists'.$desc4.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num5.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline5.'>Deaths'.$desc5.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num7.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline7.'>Headshots'.$desc7.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num12.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline12.'><img border="0" src='.$main_url.'css/img/icon-star.png id=himg2></img>'.$desc11.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num11.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline11.'><img border="0" src='.$main_url.'css/img/icon-trophy.png id=himg2></img>'.$desc12.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num8.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline8.'><img border="0" src='.$main_url.'css/img/icon-c4-explosive.png id=himg></img>'.$desc8.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num9.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline9.'><img border="0" src='.$main_url.'css/img/icon-explosion.png id=himg></img>'.$desc9.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num10.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline10.'><img border="0" src='.$main_url.'css/img/icon-defuse-kit.png id=himg></img>'.$desc10.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num1.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline1.'>XP'.$desc1.'</a></th>
        <th><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num6.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline6.'>Skill'.$desc6.'</a></th>
        <th id="v"><a id=url href="'.$main_url.'top15.php?top='.$top.'&player='.$player.'&style='.$style.'&order='.$num13.'&default_order='.$default_order.'&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.htmlspecialchars($search).'" '.$underline13.'>Rank'.$desc13.'</a></th>
   	</tr>';
   			if(!empty($search)) {
   				$placeholder = '%'.$search.'%';
   				$top15 = DB::run('SELECT Player, Nick, `Steam ID`, IP, XP, Level, Kills, Assists, Headshots, Deaths, Planted, Exploded, Defused, MVP, `Rounds Won`, Skill, `Skill Range`, Flags, Online, New, Steam, Avatar FROM '.$db_table1.' WHERE Nick LIKE ? OR IP LIKE ? OR `Steam ID` LIKE ? ORDER BY '.$order.' LIMIT '.$items_per_page.' OFFSET '.$offset.'', [$placeholder, $placeholder, $placeholder])->FetchAll(PDO::FETCH_ASSOC);
   			} else {
    			$top15 = DB::run('SELECT Player, Nick, `Steam ID`, IP, XP, Level, Kills, Assists, Headshots, Deaths, Planted, Exploded, Defused, MVP, `Rounds Won`, Skill, `Skill Range`, Flags, Online, New, Steam, Avatar FROM '.$db_table1.' ORDER BY '.$order.' LIMIT '.$items_per_page.' OFFSET '.$offset.'')->FetchAll(PDO::FETCH_ASSOC);
    		}
    		$i = $offset + 1;
    		$table = false;

    		foreach ($top15 as $row) {
    			$reader = new Reader('GeoLite2-City.mmdb');
				try {
					$record = $reader->city($row['IP']);
					$flag = strtolower($record->country->isoCode);
				} catch (GeoIp2\Exception\AddressNotFoundException $e) {
					$flag = 'nn';
				}

    			$skill_range = 0.0;
    			$hs_ratio = 0.0;
    			$skill = $row['Skill'];
    			$rank = $row['Level'] + 1;
    			$steam = '';
    			$new = '';
    			$avatar = '';
    			$color_name = $default_name_color;
    			$color_skill = $default_skill_color;

    			if($row['Steam']) {
    				$steam = '<img src="'.$main_url.'css/img/icon-steam.png" id="steam"></img>';
    				$avatar = '<img src="'.$row['Avatar'].'" id="avatar"></img>';
    			}
    			if($row['New']) $new = '<img src="'.$main_url.'css/img/icon-new.png" id="new"></img>';
    			if($row['Kills']) $hs_ratio = 100.0 * floatval($row['Headshots']) / floatval($row['Kills']);
    			if($row['Kills'] || $row['Deaths']) $skill_range = 100.0 * (floatval($row['Kills']) / floatval($row['Kills'] + $row['Deaths']));
    			if($table) {
    				echo '<tr id=b>'; $table = false;
    			} else {
    				echo '<tr>'; $table = true;
    			}
    			if($id && $id['Player'] == $row['Player']) echo '<tr id=i>';

    			switch($i) {
    				case 1: echo '<td id=z></td>'; break;
    				case 2: echo '<td id=w></td>'; break;
    				case 3: echo '<td id=y></td>'; break;
    				default: echo '<td id=p>'.$i.'</td>'; break;
    			}

				foreach ($name_colors as $n_color) {
				    $flags = str_split($row['Flags']);
				    $color_flags = str_split($n_color['flags']);
				    if (count(array_intersect($flags, $color_flags)) == count($color_flags)) {
				        $color_name = $n_color['color'];
				        break;
				    }
				}

				foreach ($skill_colors as $s_color) {
   					if ($row['Skill'] == $s_color['skill']) {
        				$color_skill = $s_color['color'];
						break;
					}
				}

				$user_url = ''.$main_url.'user.php?player='.$row['Player'].'&me='.$player.'&top='.$top.'&style='.$style.'&order='.$orderby.'&default_order='.htmlspecialchars($_GET['default_order']).'&show=0&page='.$page.'&db_table1='.$db_table1.'&db_table2='.$db_table2.'&search='.$search.'';
    			$new_user_url = str_replace(' ', '%20', $user_url);

    			if($color_name != $default_name_color) {
    				echo '<style>.glow'.$i.' { text-shadow: 1px 1px 6px '.$color_name.'; }</style>';
    			}

    			echo '<style>.skill'.$i.' { background: '.$color_skill.'; border-color: '.$color_skill.'; }</style>';
				echo '
						<td id=sp><div id=o'.$row['Online'].'><div id='.$flag.'>'.$avatar.'<a href="'.$new_user_url.'"; style=color:'.$color_name.' class="glow'.$i.'">'.$row['Nick'].'</a>'.$steam.''.$new.'</div></div></td>
						<td>'.$row['Kills'].'</td>
						<td>'.$row['Assists'].'</td>
						<td>'.$row['Deaths'].'</td>
						<td id=hs>'.$row['Headshots'].' <a>'.number_format((float)$hs_ratio, 2, '.', '').'%</a></td>
						<td id=s>★<a>'.$row['MVP'].'</a></td>
						<td>'.$row['Rounds Won'].'</td>
						<td>'.$row['Planted'].'</td>
						<td>'.$row['Exploded'].'</td>
						<td>'.$row['Defused'].'</td>
						<td>'.$row['XP'].'</td>
						<td><table id=sk1 class=skill'.$i.'><td id="sk11">'.$skill.'<td id=sk12>'.number_format((float)$skill_range, 2, '.', '').'</td></td></table></td>
						<td id="r'.$rank.'"></td>
					</tr>
				';
    			$i++;
    		}

			echo '</table><table class=attributes><td class=pagination>';

			if(isset($_GET['top'])) unset($_GET['top']);
			$link_att = http_build_query($_GET);
			$new_link = ''.$main_url.'top15.php?'.http_build_query($_GET).'';

			if ($page > 1) {
				
			    echo "<a href='$new_link&page=1'>First</a> ";
			}

			for ($i = max(1, $page - 2); $i <= min($page + 2, $total_pages); $i++) {
			    if ($i == $page) {
			        echo "<strong>$i</strong> ";
			    } else {
			        echo "<a href='$new_link&page=$i'>$i</a> ";
			    }
			}

			if ($page < $total_pages) {
			    echo "<a href='$new_link&page=$total_pages'>Last</a>";
			}

			echo '<td class="form-control"><form method="get">
			<input class="search-bar" name="search" type="search" id="inputSearch" placeholder="Nick / IP / Steam ID" aria-label="Search">
			<input type="hidden" name="player">
			<input type="hidden" name="page" value="1">
			<input type="hidden" name="style" value="'.$style.'">
			<input type="hidden" name="order" value="'.$orderby.'">
			<input type="hidden" name="default_order" value="'.$default_order.'">
			<input type="hidden" name="db_table1" value="'.$db_table1.'">
			<input type="hidden" name="db_table2" value="'.$db_table2.'">
			<button class="button" id="btnSearch" type="submit" >Search</button>';
        ?>