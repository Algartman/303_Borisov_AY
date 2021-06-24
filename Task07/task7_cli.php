<?php
$db = new PDO('sqlite:students.db');
$r = iconv('CP866', 'utf-8',chr(197));
$r2 = iconv('CP866', 'utf-8',chr(196));
$r3 = iconv('CP866', 'utf-8',chr(179));
$g1 = iconv('CP866', 'utf-8',chr(218));
$g2 = iconv('CP866', 'utf-8',chr(194));
$g3 = iconv('CP866', 'utf-8',chr(191));
$lg = iconv('CP866', 'utf-8',chr(195));
$rg = iconv('CP866', 'utf-8',chr(180));
$gb1 = iconv('CP866', 'utf-8',chr(192));
$gb2 = iconv('CP866', 'utf-8',chr(193));
$gb3 = iconv('CP866', 'utf-8',chr(217));
$p = iconv('CP866', 'utf-8',chr(32));

$split_str = "\n".$lg.str_repeat($r2, 7).$r.str_repeat($r2, 39).$r.str_repeat($r2, 15).$r.str_repeat($r2, 15).$r.str_repeat($r2, 15).$r.str_repeat($r2, 4).$r.str_repeat($r2, 14).$r.str_repeat($r2, 9).$rg."\n";
$top_split = "\n".$g1.str_repeat($r2, 7).$g2.str_repeat($r2, 39).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 4).$g2.str_repeat($r2, 14).$g2.str_repeat($r2, 9).$g3."\n";
$bottom_split = "\n".$gb1.str_repeat($r2, 7).$gb2.str_repeat($r2, 39).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 4).$gb2.str_repeat($r2, 14).$gb2.str_repeat($r2, 9).$gb3."\n";

$sql_gr = 'SELECT distinct direction_id as dir from groups order by direction_id;';
$gr = $db->prepare($sql_gr);
$gr->execute();
$res = $gr->fetchAll(PDO::FETCH_ASSOC);

echo "\n".$g1.str_repeat($r2, 8).$g3."\n";
echo $r3." Группа ".$r3;
$array_groups = array();
foreach($res as $res_string){
    array_push($array_groups, $res_string['dir']);
    $value1 = sprintf("  %' -6d", $res_string['dir']);
    echo "\n".$lg.str_repeat($r2, 8).$rg."\n";
    echo $r3.$value1.$r3;
}
echo "\n".$gb1.str_repeat($r2, 8).$gb3."\n";

echo "\nВведите '*' для выхода из программы.\n";
$find_group = "";
$v1 = sprintf(" %' -4s", "Группа");
$v2 = sprintf(" %' -46s\t", "Направление");
$v3 = sprintf(" %' -15s\t", "Фамилия");
$v4 = sprintf(" %' -13s\t", "Имя");
$v5 = sprintf(" %' -20s\t", "Отчество");
$v6 = sprintf(" %' -3s", "Пол");
$v7 = sprintf(" %' -11s", "Дата рождения");
$v8 = sprintf(" %' -13s", "Билет");
$top_table = $r3.$v1.$r3.$v2.$r3.$v3.$r3.$v4.$r3.$v5.$r3.$v6.$r3.$v7.$r3.$v8.$r3;

while($find_group!="*"){
    echo "\nВведите номер группы: ";
    $find_group = ""; 
    fscanf(STDIN, "%s", $find_group);
    if(ctype_digit($find_group)){
        if (in_array($find_group, $array_groups)){
            $sql_find_students = "SELECT groups.direction_id AS g,
                                  directions.name as dir,
			                      students.surname as f,
			                      students.name as n,
			                      students.patronymic as p,
			                      case when students.gender == 1 then 'м' else 'ж' end as s,
			                      students.bdate as b, students.card_num as c from groups
			                      join students on students.id = groups.student_id
			                      join directions on groups.direction_id = directions.id
								  where g = '$find_group' order by f;";
            $st_gr = $db->prepare($sql_find_students);
            $st_gr->execute();
            $results_st_gr = $st_gr->fetchAll(PDO::FETCH_ASSOC);

            echo $top_split;
            echo $top_table;
            echo $split_str;

            $counter = 0;
            foreach($results_st_gr as $res_string){
                $value1 = sprintf(" %' -4d\t", $res_string['g']);
                $value2 = sprintf(" %' -56s\t", $res_string['dir']);
                $value3 = sprintf(" %' -15s\t", $res_string['f']);
                $value4 = sprintf(" %' -13s\t", $res_string['n']);
                $value5 = sprintf(" %' -20s\t", $res_string['p']);
                $value6 = sprintf(" %' -4s", $res_string['s']);
                $value7 = sprintf(" %' -13s", $res_string['b']);
                $value8 = sprintf(" %' -8d", $res_string['c']);
                echo $r3,$value1,$r3, $value2,$r3, $value3,$r3, $value4,$r3, $value5,$r3, $value6,$r3, $value7,$r3,$value8, $r3;

                if(++$counter==count($results_st_gr)){
                    echo $bottom_split;
                }
                else{
                    echo $split_str;
                }

            }
        }
        else{
            echo "Группа с введённым номером не найдена";
        }
    } 
    else if($find_group==""){
        $sql_q = "SELECT groups.direction_id AS g,
                  directions.name as dir,
			      students.surname as f,
			      students.name as n,
			      students.patronymic as p,
			      case when students.gender == 1 then 'м' else 'ж' end as s,
			      students.bdate as b, students.card_num as c from groups
			      join students on students.id = groups.student_id
			      join directions on groups.direction_id = directions.id
			      order by groups.direction_id, students.surname;";

        $st = $db->prepare($sql_q);
        $st->execute();
        $results = $st->fetchAll(PDO::FETCH_ASSOC);

        $res_string;
        echo $top_split;
        echo $top_table;
        echo $split_str;
        $counter = 0;
        foreach($results as $res_string){
            $value1 = sprintf(" %' -4d\t", $res_string['g']);
            $value2 = sprintf(" %' -56s\t", $res_string['dir']);
            $value3 = sprintf(" %' -15s\t", $res_string['f']);
            $value4 = sprintf(" %' -13s\t", $res_string['n']);
            $value5 = sprintf(" %' -20s\t", $res_string['p']);
            $value6 = sprintf(" %' -3s", $res_string['s']);
            $value7 = sprintf(" %' -13s", $res_string['b']);
            $value8 = sprintf(" %' -8d", $res_string['c']);

            echo $r3,$value1,$r3, $value2,$r3, $value3,$r3, $value4,$r3, $value5,$r3, $value6,$r3, $value7,$r3,$value8, $r3;
            if(++$counter==count($results)){
                echo $bottom_split;
            }
            else{
                echo $split_str;
            }
        }
    }
    else if($find_group!="*"){
        echo "Введены некорректные данные\n";
    }
}
?>