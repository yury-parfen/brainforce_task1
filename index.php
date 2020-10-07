<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pricelist</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
            function funcBefore () {
                $("#information").text ("Выполнение запроса...");
            }
            function funcSuccess (data) {
                $("#information").html (data);
            }

            $(document).ready (function () {
                $("#show").bind("click", function () {
                    $.ajax ({
                        url: "filter.php", 
                        type: "POST",
                        data: ({selectprice: $("#selectprice").val(), minprice: $("#minprice").val(), maxprice: $("#maxprice").val(), selectquantity: $("#selectquantity").val(), quantity: $("#quantity").val()}),
                        dataType: "html",
                        beforeSend: funcBefore,
                        success: funcSuccess
                    });
                });
            });
    </script>

    <?php
        $servername = "localhost";
        $database = "brainforce";
        $username = "root";
        $password = "root";

        $conn = mysqli_connect($servername, $username, $password, $database);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error() . "<br>");
        } else {
            echo "Соединение установлено <br>\n";
        }

        $id = 'id';
        $name = 'Наименование товара';
        $price1 = 'Стоимость, руб';
        $price2 = 'Стоимость опт, руб';
        $stock1 = 'Наличие на складе 1, шт';
        $stock2 = 'Наличие на складе 2, шт';
        $country = 'Страна производства';
        $notes = 'Примечания';

        // -------------- создание таблицы
        $queryTable = "CREATE Table `pricelist`
            (
                `$id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `$name` VARCHAR(55) NOT NULL UNIQUE KEY,
                `$price1` VARCHAR(55),
                `$price2` VARCHAR(55),
                `$stock1` INT(11),
                `$stock2` INT(11),
                `$country` VARCHAR(55),
                `$notes` VARCHAR(255)
            )";
        if (mysqli_query($conn, $queryTable)) {
            echo "Таблица `pricelist` создана успешно <br>";
          } else {
             echo "Ошибка создания таблицы: " . mysqli_error($conn) . "<br>";
        }

          // -------------- импорт таблицы из файла csv
          $queryImport = "LOAD DATA LOCAL INFILE '/Applications/MAMP/htdocs/brainforce_task1/pricelist.csv' 
          INTO TABLE `pricelist` 
          COLUMNS TERMINATED BY ','
          OPTIONALLY ENCLOSED BY '\"'
          ESCAPED BY '\"'
          LINES TERMINATED BY '\n'
          IGNORE 1 LINES
          (`$name`, `$price1`,
          `$price2`, `$stock1`, `$stock2`, `$country`, `$notes`);";

          $resImport = mysqli_query($conn, $queryImport);
          if ($resImport) {
              echo "Импорт файла произведен успешно";
            } else {
               echo "Ошибка импорта файла: " . mysqli_error($conn);
            }
              
        // -------------- убрал текстовые значения из столбцов с ценами, заменил в ценаз запятые на точки, поменял тип столбцов с ценами
        $price1Null = mysqli_query($conn, "UPDATE `pricelist` SET `$price1` = NULL WHERE `$price1` = 'Стоимость';");
        $price2Null = mysqli_query($conn, "UPDATE `pricelist` SET `$price2` = NULL WHERE `$price2` = '\#ЗНАЧЕН!';");
        $replaceDot = mysqli_query($conn, "UPDATE `pricelist` SET `$price1` = REPLACE(`$price1`, ',', '.');");
        $changeType1 = mysqli_query($conn, "ALTER TABLE `pricelist` CHANGE `$price1` `$price1` DOUBLE NULL DEFAULT NULL;");
        $changeType2 = mysqli_query($conn, "ALTER TABLE `pricelist` CHANGE `$price2` `$price2` INT(11) NULL DEFAULT NULL;
        ;");
    ?>
</head>
<body>

    <div style="margin-top: 15px">
        <p style="font-size: 20px">
            Показать товары, у которых 
            <select name="selectprice" id="selectprice">
                <option value="retail">Розничная цена</option>
                <option value="wholesale">Оптовая цена</option>
            </select> от 
            <input type="text" name="minprice" id="minprice" placeholder="1000"> до
            <input type="text" name="maxprice" id="maxprice" placeholder="5000"> рублей и на складе
            <select name="selectquantity" id="selectquantity">
                <option value="more">Более</option>
                <option value="less">Менее</option>
            </select>
            <input type="text" name="quantity" id="quantity" placeholder="20">  штук.
            <input type="button" id="show" value="ПОКАЗАТЬ ТОВАРЫ"> 
        </p>
    </div>

    <div style="margin-top: 15px" id="information">
        <table style="margin-top: 15px" border="1">
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

                // -------------- находим максимальное и минимальное значение в столбцах
                $sqlMAX = mysqli_query($conn, "SELECT MAX(`$price1`) FROM `pricelist` WHERE `$price1` IS NOT NULL;");
                $max = mysqli_fetch_array($sqlMAX);

                $sqlMIN = mysqli_query($conn, "SELECT MIN(`$price2`) FROM `pricelist` WHERE `$price2` IS NOT NULL;");
                $min = mysqli_fetch_array($sqlMIN);
            

                $sqlPrint = mysqli_query($conn, "SELECT * FROM `pricelist`;");
                while ($row = mysqli_fetch_array($sqlPrint)) {
                    echo "<tr>";
                    echo "<td>".$row["$id"]."</td>";
                    echo "<td>".$row["$name"]."</td>";
                    if ($row["$price1"] == $max[0]) {
                        echo "<td style='background-color: red';>".$row["$price1"]."</td>";
                    } else {
                        echo "<td>".$row["$price1"]."</td>";
                    }
                    if ($row["$price2"] == $min[0]) {
                        echo "<td style='background-color: green';>".$row["$price2"]."</td>";
                    } else {
                        echo "<td>".$row["$price2"]."</td>";
                    }
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
            ?>

        </table>

        <?php   
                function functionSum ($stock) {
                    global $conn;
                    $sqlSum = mysqli_query($conn, "SELECT SUM(`$stock`) FROM `pricelist` WHERE `$stock` IS NOT NULL;");
                    $sum = mysqli_fetch_array($sqlSum);
                    echo $sum[0];
                }
        ?>    
        <p>Общее количество товаров на Складе 1: <?php functionSum($stock1); ?> шт.</p>
        <p>Общее количество товаров на Складе 2: <?php functionSum($stock2); ?> шт.</p>

        <?php   
                function functionAvg ($price) {
                    global $conn;
                    $sqlAvg = mysqli_query($conn, "SELECT AVG(`$price`) FROM `pricelist` WHERE `$price` IS NOT NULL;");
                    $avg = mysqli_fetch_array($sqlAvg);
                    echo round($avg[0], 2);
                }
        ?>  

        <p>Средняя стоимость розничной цены товара: <?php functionAvg($price1); ?> руб.</p>
        <p>Средняя стоимость оптовой цены товара: <?php functionAvg($price2); ?> руб.</p>
    </div>
</body>
</html>