<?php
require_once 'inc/func.php';
require_once("geoip2.phar");
use GeoIp2\Database\Reader;

$style = $_GET['style'];
$player = $_GET['player'];
$show = $_GET['show'];

switch(htmlspecialchars($_GET['default_order']))
{
    case 0: $order = 'XP'; $orderby = 'XP DESC, Nick ASC'; break;
    case 1: $order = 'Nick'; $orderby = 'Nick ASC'; break;
    case 2: $order = 'Kills'; $orderby = 'Kills DESC, Nick ASC'; break;
    case 3: $order = 'Assists'; $orderby = 'Assists DESC, Nick ASC'; break;
    case 4: $order = 'Deaths'; $orderby = 'Deaths DESC, Nick ASC'; break;
    case 5: $order = 'Skill'; $orderby = '`Skill Range` DESC, Nick ASC'; break;
    case 6: $order = 'Headshots'; $orderby = 'Headshots DESC, Nick ASC'; break;
    case 7: $order = 'C4 Planted'; $orderby = 'Planted DESC, Nick ASC'; break;
    case 8: $order = 'C4 Exploded'; $orderby = 'Exploded DESC, Nick ASC'; break;
    case 9: $order = 'C4 Defused'; $orderby = 'Defused DESC, Nick ASC'; break;
    case 10: $order = 'Rounds Won'; $orderby = '`Rounds Won` DESC, Nick ASC'; break;
    case 11: $order = 'MVP'; $orderby = 'MVP DESC, Nick ASC'; break;
    case 12: $order = 'Rank'; $orderby = 'Level DESC, XP DESC, Nick ASC'; break;
    case 13: $order = 'Overall'; $orderby = '(Kills - Deaths) DESC, Assists DESC, Headshots DESC, MVP DESC, `Rounds Won` DESC, Planted DESC, Exploded DESC, Defused DESC, XP DESC, Nick ASC'; break;
}

$stats_css = $style ? 'css/stats.css' : 'css/stats_nonsteam.css';
$sql = DB::run('SELECT Nick, `Steam ID`, IP, XP, `Rank XP`, `Next Rank XP`, Level, `Rank Name`, Kills, Assists, Headshots, Deaths, Shots, Hits, Damage, Planted, Exploded, Defused, MVP, `Rounds Won`, `Played Time`, `First Login`, `Last Login`, Online, Skill, Steam, Flags, New, Avatar, Profile FROM '.htmlspecialchars($_GET['db_table1']).' WHERE Player = ?', [$player]);
$user = $sql->fetch(PDO::FETCH_ASSOC);

$avatar = ''.$main_url.'css/img/default_avatar.jpg';
$reader = new Reader('GeoLite2-City.mmdb');
$country = '';

try {
    if (!empty($user['IP'])) {
        $record = $reader->city($user['IP']);
        $country = $record->country->name;
        $city = ''.$record->city->name.', ';
        $flag = strtolower($record->country->isoCode);
    } else {
        $city = 'n/a';
        $flag = 'nn';
    }
} catch (GeoIp2\Exception\AddressNotFoundException $e) {
    $city = 'n/a';
    $flag = 'nn';
}

if($user['Steam']) $avatar = $user['Avatar'];

$rank = 0;
$count = 0;
$sql_rank = DB::run('SELECT k.id, k.Player FROM (SELECT (@row_number:=@row_number+1) AS id, Player FROM (SELECT Player FROM '.htmlspecialchars($_GET['db_table1']).', (SELECT @row_number:=0) AS rn ORDER BY '.$orderby.') AS subquery) AS k WHERE k.Player = ?', [$player])->FetchAll(PDO::FETCH_ASSOC);
$sql_count = DB::run('SELECT COUNT(*) FROM '.htmlspecialchars($_GET['db_table1']).'')->FetchAll(PDO::FETCH_ASSOC);
foreach ($sql_rank as $row) { $rank = $row['id']; }
foreach ($sql_count as $row) { $count = $row['COUNT(*)']; }

$skill_range = 0.0;
if($user['Kills'] || $user['Deaths']) $skill_range = 100.0 * (floatval($user['Kills']) / floatval($user['Kills'] + $user['Deaths']));

$current = $user['Level'] + 1;
$next = $user['Level'] + 2;
$progress = 100 * ($user['XP'] - $user['Rank XP']) / ($user['Next Rank XP'] - $user['Rank XP']); if($progress >= 100 || $progress < 0) $progress = 0;
$next_xp = $user['Next Rank XP'] - $user['XP']; if($next_xp <= 0) $next_xp = 0;
$next_rank = $user['XP'] < $user['Next Rank XP'] ? '<div id="rn'.$next.'"><a>Next</a></div>' : '';
$hours = floor($user['Played Time'] / 3600);
$minutes = floor(($user['Played Time'] / 60) % 60);

$kd_ratio = 0.0; if($user['Kills'] && $user['Deaths']) $kd_ratio = floatval($user['Kills']) / floatval($user['Deaths']);
$hs_percentage = 0.0; if($user['Kills']) $hs_percentage = 100.0 * floatval($user['Headshots']) / floatval($user['Kills']);
$accuracy = 0.0; if($user['Shots']) $accuracy = 100.0 * floatval($user['Hits']) / floatval($user['Shots']);
$efficiency = 0.0; if($user['Kills'] || $user['Deaths']) $efficiency = 100.0 * floatval($user['Kills']) / floatval($user['Kills'] + $user['Deaths']);

if(!$show) {
    $url_link = ''.$main_url.'top15.php?top='.htmlspecialchars($_GET['top']).'&player='.htmlspecialchars($_GET['me']).'&style='.htmlspecialchars($style).'&order='.htmlspecialchars($_GET['order']).'&default_order='.htmlspecialchars($_GET['default_order']).'&page='.htmlspecialchars($_GET['page']).'&db_table1='.htmlspecialchars($_GET['db_table1']).'&db_table2='.htmlspecialchars($_GET['db_table2']).'&search='.htmlspecialchars($_GET['search']).'';
    $new_url_link = str_replace(' ', '%20', $url_link);
    $show_link = '<style>#url:hover { text-decoration: underline; cursor:pointer; } #url { color:#A9A9A9; text-decoration: none; }</style><a id=url href="'.$new_url_link.'"><p>↵ Show Top Stats</p></a>';
} else {
    $url_link = ''.$main_url.'mapend.php?player='.htmlspecialchars($_GET['me']).'&style='.htmlspecialchars($style).'&default_order='.htmlspecialchars($_GET['default_order']).'&db_table1='.htmlspecialchars($_GET['db_table1']).'&db_table2='.htmlspecialchars($_GET['db_table2']).'&db_table3='.htmlspecialchars($_GET['db_table3']).'';
    $new_url_link = str_replace(' ', '%20', $url_link);
    $show_link = '<style>#url:hover { text-decoration: underline; cursor:pointer; } #url { color:#A9A9A9; text-decoration: none; }</style><a id=url href="'.$new_url_link.'"><p>↵ Show Map Stats</p></a>';
}

$profile = $user['Steam'] ? '<a href="'.$user['Profile'].'"><img border="0" src='.$avatar.'></a>' : '<img src='.$avatar.'>';

$color = $default_name_color;
foreach ($name_colors as $n_color) {
    $flags = str_split($user['Flags']);
    $color_flags = str_split($n_color['flags']);
    if (count(array_intersect($flags, $color_flags)) == count($color_flags)) {
        $color = $n_color['color'];
        break;
    }
}

foreach ($skill_colors as $s_color) {
    if ($user['Skill'] == $s_color['skill']) {
        $color_skill = $s_color['color'];
        break;
    }
}

echo '<!DOCTYPE html>';
if($color != $default_name_color && $style) {
    echo '<style>.glow { text-shadow: 1px 1px 10px '.$color.'; }</style>';
}
echo'
<meta charset="utf-8"><link rel="stylesheet" href='.$stats_css.' />
'.$show_link.'
<table>
    <td id="a">
        <div id="d">
            '.$profile.'
            <div id='.$flag.'>
                <div id="u">'.$city.' '.$country.'</div>
                <div id="f'.$user['Online'].'"><a style=color:'.$color.' class="glow">
                    '.$user['Nick'].'</a></div>
                    <div id="t">rank '.$rank.' from '.$count.'</div>
                    <div id="t"><a><i>ranking by: '.$order.'</i></a></div>
                </div>
                <style>.skill { background: '.$color_skill.'; }</style><table id=sk1 class=skill><td id="sk11">'.$user['Skill'].'<td id=sk12>'.number_format((float)$skill_range, 2, '.', '').'</td></td></table>
            </div>
        <div id="c">
            <div id="r'.$current.'"><a>Current</a></div>
            <div id="h">
                <p id="i">'.$user['Rank Name'].'</p>
                <div id="j"><div class=progress style="width:'.$progress.'%"></div></div>
                <p id="k">'.$user['XP'].'xp (+'.$next_xp.')</p>
            </div>
            '.$next_rank.'
        </div>
        <div id="c">
            <p id="mvp">Most Valuable Player:<a id="g">'.$user['MVP'].'</a></p>
            <p id="rwn">Rounds Won:<a id="g">'.$user['Rounds Won'].'</a></p>
            <p id="bp">Bombs Planted:<a id="g">'.$user['Planted'].'</a></p>
            <p id="bc">Bombs Exploded:<a id="g">'.$user['Exploded'].'</a></p>
            <p id="di">Bombs Defused:<a id="g">'.$user['Defused'].'</a></p>
        </div>
    </td>
    <td id="n">
        <div id="d"><div id="f">Statistics</div></div>
        <div id="l1">
            <p id=kills>Kills:<a>'.$user['Kills'].'</a></p>
            <p id=deaths>Deaths:<a>'.$user['Deaths'].'</a></p>
            <p id=assists>Assists:<a>'.$user['Assists'].'</a></p>
            <p id=headshots>Headshots:<a>'.$user['Headshots'].' ('.number_format((float)$hs_percentage, 1, '.', '').'%)</a></p>
            <p id=kdratio>K/D Ratio:<a>'.number_format((float)$kd_ratio, 1, '.', '').'</a></p>
        </div>
        <div id="l2">
            <p id=shots>Shots:<a>'.$user['Shots'].'</a></p>
            <p id=hits>Hits:<a>'.$user['Hits'].'</a></p>
            <p id=damage>Damage:<a>'.$user['Damage'].'</a></p>
            <p id=accuracy>Accuracy:<a>'.number_format((float)$accuracy, 1, '.', '').'%</a></p>
            <p id=efficiency>Efficiency:<a>'.number_format((float)$efficiency, 1, '.', '').'%</a></p>
        </div>
        <div id="l3">
            <p id=firstlogin>First Login:<a>'.$user['First Login'].'</a></p>
            <p id=lastlogin>Last Login:<a>'.$user['Last Login'].'</a></p>
            <p id=playedtime>Played Time:<a>'.$hours.'h '.$minutes.'m</a></p>
        </div>
    </td>
    <td id="o">
    <div id="d"><div id="f">Top Weapons</div></div>';

$weapons = array(
    'Knife' => 0,
    'Glock18' => 1,
    'USP' => 2,
    'P228' => 3,
    'Deagle' => 4,
    'Fiveseven' => 5,
    'Elite' => 6,
    'M3' => 7,
    'XM1014' => 8,
    'TMP' => 9,
    'MAC10' => 10,
    'MP5 Navy' => 11,
    'UMP45' => 12,
    'P90' => 13,
    'M249' => 14,
    'Galil' => 15,
    'Famas' => 16,
    'AK47' => 17,
    'M4A1' => 18,
    'SG552' => 19,
    'AUG' => 20,
    'Scout' => 21,
    'AWP' => 22,
    'G3SG1' => 23,
    'SG550' => 24,
    'HE Grenade' => 25
);

$sql_weapon_parts = array();
foreach ($weapons as $weapon => $id) {
    $sql_weapon_parts[] = "SELECT '$weapon' AS name, `$weapon` AS kills, $id AS id FROM " . htmlspecialchars($_GET['db_table2']) . " WHERE Player = ?";
}

$sql_weapon_query = "SELECT name, kills, id FROM (" . implode(" UNION ALL ", $sql_weapon_parts) . ") AS subquery ORDER BY kills DESC LIMIT 5";
$sql_weapon_data = DB::run($sql_weapon_query, array_fill(0, count($weapons), $player))->fetchAll(PDO::FETCH_ASSOC);

foreach ($sql_weapon_data as $weapon) {
    if(!$weapon['kills']) {
        $weapon['name'] = 'n/a';
        $weapon['id'] = -1;
    }

    echo '
        <div id="m">
            <p>'.$weapon['name'].'</p>
            <div id=w'.($weapon['id'] + 1).'>'.$weapon['kills'].' Kills</div>
        </div>
    ';
}
?>
    </td>
</table>
