<?php

$servername = "localhost";
$database = "brainforce";
$username = "root";
$password = "root";
$conn = mysqli_connect($servername, $username, $password, $database);

if (preg_match("/\D/", $_POST['minprice'])) {
    echo "Неверно введены данные в поле \"Минимальная цена\"!";
    exit;
}

if (preg_match("/\D/", $_POST['maxprice'])) {
    echo "Неверно введены данные в поле \"Максимальная цена\"!";
    exit;
}

if (preg_match("/\D/", $_POST['quantity'])) {
    echo "Неверно введены данные в поле \"Количество штук\"!";
    exit;
}
?>

<table style="margin-top: 15px" border="1" id="information">
            <caption style="font-size: 20px">Pricelist</caption>
            <tr >
                <th >ID</th>
                <th>Наименование товара</th>
                <th>Стоимость, руб</th>
                <th>Стоимость опт, руб</th>
                <th>Наличие на складе 1, шт</th>
                <th>Наличие на складе 2, шт</th>
                <th>Страна производства</th>
                <th>Примечания</th>
            </tr>

<?php

$id = 'id';
$name = 'Наименование товара';
$price1 = 'Стоимость, руб';
$price2 = 'Стоимость опт, руб';
$stock1 = 'Наличие на складе 1, шт';
$stock2 = 'Наличие на складе 2, шт';
$country = 'Страна производства';
$notes = 'Примечания';

if ($_POST['selectprice'] == "retail") {
    $price = $price1;
} else {
    $price = $price2;
}

if ($_POST['maxprice'] == NULL) {
    $sqlMAX = mysqli_query($conn, "SELECT MAX(`$price`) FROM `pricelist` WHERE `$price` IS NOT    NULL;");
    $max = mysqli_fetch_array($sqlMAX);
    $_POST['maxprice'] = $max[0];
}
if ($_POST['minprice'] == NULL) {
    $_POST['minprice'] = 0;
}

$sqlPrint = mysqli_query($conn, "SELECT * FROM `pricelist`;");
while ($row = mysqli_fetch_array($sqlPrint)) {
    if ($row["$price"] >= $_POST['minprice'] && $row["$price"] <= $_POST['maxprice'] ) {
        if ($_POST['selectquantity'] == "more" && $_POST['quantity'] != NULL && $row["$stock1"] > $_POST['quantity'] && $row["$stock2"] > $_POST['quantity']) {
            echo "<tr>";
            echo "<td>".$row["$id"]."</td>";
            echo "<td>".$row["$name"]."</td>";
            echo "<td>".$row["$price1"]."</td>";
            echo "<td>".$row["$price2"]."</td>";
            echo "<td>".$row["$stock1"]."</td>";
            echo "<td>".$row["$stock2"]."</td>";
            echo "<td>".$row["$country"]."</td>";
            if ($row["$stock1"] < 20 || $row["$stock2"] < 20) {
                echo "<td>Осталось мало!! Срочно докупите!!!</td>";
            } else {
                echo "<td>".$row["$notes"]."</td>";
            }
            echo "</tr>";
        }
        else if ($_POST['selectquantity'] == "less" && $_POST['quantity'] != NULL && $row["$stock1"] < $_POST['quantity'] && $row["$stock2"] < $_POST['quantity']) {
            echo "<tr>";
            echo "<td>".$row["$id"]."</td>";
            echo "<td>".$row["$name"]."</td>";
            echo "<td>".$row["$price1"]."</td>";
            echo "<td>".$row["$price2"]."</td>";
            echo "<td>".$row["$stock1"]."</td>";
            echo "<td>".$row["$stock2"]."</td>";
            echo "<td>".$row["$country"]."</td>";
            if ($row["$stock1"] < 20 || $row["$stock2"] < 20) {
                echo "<td>Осталось мало!! Срочно докупите!!!</td>";
            } else {
                echo "<td>".$row["$notes"]."</td>";
            }
            echo "</tr>";
        } elseif ($_POST['quantity'] == NULL) {
            echo "<tr>";
            echo "<td>".$row["$id"]."</td>";
            echo "<td>".$row["$name"]."</td>";
            echo "<td>".$row["$price1"]."</td>";
            echo "<td>".$row["$price2"]."</td>";
            echo "<td>".$row["$stock1"]."</td>";
            echo "<td>".$row["$stock2"]."</td>";
            echo "<td>".$row["$country"]."</td>";
            if ($row["$stock1"] < 20 || $row["$stock2"] < 20) {
                echo "<td>Осталось мало!! Срочно докупите!!!</td>";
            } else {
                echo "<td>".$row["$notes"]."</td>";
            }
            echo "</tr>";
        }
    }
}

?>

</table>