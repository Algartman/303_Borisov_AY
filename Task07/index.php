<?php
    $db = new PDO('sqlite:students.db');
    $sql_q = "SELECT groups.direction_id AS g,
              directions.name as dir,
			  students.surname as f,
			  students.name as n,
			  students.patronymic as p,
			  case when students.gender == 1 then 'male' else 'female' end as s,
			  students.bdate as b,
			  students.card_num as с 
			  from groups
			  join students on students.id = groups.student_id
			  join directions on groups.direction_id = directions.id
			  order by groups.direction_id, students.surname;";

    $st = $db->prepare($sql_q);
    $st->execute();
    $all_students = $st->fetchAll(PDO::FETCH_ASSOC);
    $sql_gr = 'SELECT distinct direction_id as dir from groups;';
    $gr = $db->prepare($sql_gr);
    $gr->execute();
    $res = $gr->fetchAll(PDO::FETCH_ASSOC);
    $array_groups = array();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Студенты</title>
    <style>
    body{
        background-color: whitesmoke;
    }
    h1{
        font-family: 'Courier New', Courier, monospace ;
        font-size: 20px;
        font-weight: bolder;
    }
    table{
        border: 1px solid black;
        font-family: 'Courier New', Courier, monospace ;
        padding: 8px;
    }
    td{
        text-align: center;
        border: 1px solid black;
        font-family: 'Courier New', Courier, monospace ;
        padding: 4px;
    }
    input,
    select{
        padding: .50rem 1rem .50rem .50rem;
        background: none;
        border: 1px solid #ccc;
        border-radius: 2px;
        font-family: inherit;
        font-size: 1rem;
        color: #444;

    }
    option{
        font-family: inherit;
        font-size: 1rem;
        color: #444;
        border: 1px solid #ccc;
    }
    hr{
        padding: 3;
        height: 2px;
        border: none;
        background: linear-gradient(45deg, #333, #ddd);
    }

    </style>

</head>
<body>
<h1>Студенты</h1>

<section>
<hr>
<form action = "<?php $_PHP_SELF ?>" method = "GET">
    <select name = "group_name">
        <option>Все группы</option>
            <?php
                foreach($res as $res_string){
                    array_push($array_groups, $res_string['dir']);
                    $groups = $res_string['dir'];
            ?>
        <option><?=$groups;?></option>
            <?php
                }
            ?>
    </select>
    <input type = "submit" style="color: #333; background: LightSteelBlue;"/>
</form>
<hr>
<?php
    if( $_GET["group_name"]){
        $gru = $_GET["group_name"];
        ?>
        <table>
        <tbody>     
        <tr style= "background-color: SteelBlue;
                    font-weight: bold;
                    font-size: 16px;">
            <td>Группа</td>
            <td>Направление подготовки</td>
            <td>Фамилия</td>
            <td>Имя</td>
            <td>Отчество</td>
            <td>Пол</td>
            <td>Дата рождения</td>
            <td>Студенческий билет</td>
        </tr>
        <?php
        if($gru!="Все группы"){
            $sql_find_students = "SELECT groups.direction_id AS g,
                                  directions.name as dir,
			                      students.surname as f,
			                      students.name as n,
			                      students.patronymic as p,
			                      case when students.gender == 1 then 'male' else 'female' end as s,
			                      students.bdate as b, students.card_num as с from groups
			                      join students on students.id = groups.student_id
			                      join directions on groups.direction_id = directions.id
                                  where g = '$gru' order by f;";
            $st_gr = $db->prepare($sql_find_students);
            $st_gr->execute();
            $results_st_gr = $st_gr->fetchAll(PDO::FETCH_ASSOC);
            foreach($results_st_gr as $res_string){
                $value1 = sprintf(" %' -4d\t", $res_string['g']);
                $value2 = sprintf(" %' -56s\t", $res_string['dir']);
                $value3 = sprintf(" %' -15s\t", $res_string['f']);
                $value4 = sprintf(" %' -13s\t", $res_string['n']);
                $value5 = sprintf(" %' -20s\t", $res_string['p']);
                $value6 = sprintf(" %' -2s", $res_string['s']);
                $value7 = sprintf(" %' -11s", $res_string['b']);
                $value8 = sprintf(" %' -8d", $res_string['c']);
            ?>
            <tr style=" background-color: LightSkyBlue; 
                        font-size: 14px;">
                <td><?=$value1;?></td>
                <td><?=$value2;?></td>
                <td><?=$value3;?></td>
                <td><?=$value4;?></td>
                <td><?=$value5;?></td>
                <td><?=$value6;?></td>
                <td><?=$value7;?></td>
                <td><?=$value8;?></td>
            </tr>
            <?php
                }
            ?>
            </tbody>
        </table>
        <?php
        }
        else{

                foreach($all_students as $res_string){
                    $value1 = sprintf(" %' -4d\t", $res_string['g']);
                    $value2 = sprintf(" %' -56s\t", $res_string['dir']);
                    $value3 = sprintf(" %' -15s\t", $res_string['f']);
                    $value4 = sprintf(" %' -13s\t", $res_string['n']);
                    $value5 = sprintf(" %' -20s\t", $res_string['p']);
                    $value6 = sprintf(" %' -2s", $res_string['s']);
                    $value7 = sprintf(" %' -11s", $res_string['b']);
                    $value8 = sprintf(" %' -8d", $res_string['c']);
                ?>
            <tr style=" background-color: LightSkyBlue; 
                        font-size: 14px;">
                <td><?=$value1;?></td>
                <td><?=$value2;?></td>
                <td><?=$value3;?></td>
                <td><?=$value4;?></td>
                <td><?=$value5;?></td>
                <td><?=$value6;?></td>
                <td><?=$value7;?></td>
                <td><?=$value8;?></td>
            </tr>
            <?php
                }
            ?>
            </tbody>
        </table>
        <?php
        }
    }
?>
<hr>
</body>
</html>