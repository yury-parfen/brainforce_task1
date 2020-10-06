<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pricelist</title>

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

        $name = 'Наименование товара';
        $price1 = 'Стоимость, руб';
        $price2 = 'Стоимость опт, руб';
        $stock1 = 'Наличие на складе 1, шт';
        $stock2 = 'Наличие на складе 2, шт';
        $country = 'Страна производства';
        $notes = 'Примечания';


        $queryTable = "CREATE Table `pricelist`
            (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
              
        $price1Null = mysqli_query($conn, "UPDATE `pricelist` SET `$price1` = NULL WHERE `$price1` = 'Стоимость';");
        $price2Null = mysqli_query($conn, "UPDATE `pricelist` SET `$price2` = NULL WHERE `$price2` = '\#ЗНАЧЕН!';");
        $replaceDot = mysqli_query($conn, "UPDATE `pricelist` SET `$price1` = REPLACE(`$price1`, ',', '.');");
        $changeType1 = mysqli_query($conn, "ALTER TABLE `pricelist` CHANGE `$price1` `$price1` DOUBLE NULL DEFAULT NULL;");
        $changeType2 = mysqli_query($conn, "ALTER TABLE `pricelist` CHANGE `$price2` `$price2` INT(11) NULL DEFAULT NULL;
        ;");
    ?>
</head>
<body>

    <table style="margin-top: 15px" border="1">
        <caption style="font-size: 20px">Pricelist</caption>
        <tr>
            <th>ID</th>
            <th>Наименование товара</th>
            <th>Стоимость, руб</th>
            <th>Стоимость опт, руб</th>
            <th>Наличие на складе 1, шт</th>
            <th>Наличие на складе 2, шт</th>
            <th>Страна производства</th>
            <th>Примечания</th>
        </tr>
    
        <?php
            $printSql = mysqli_query($conn, "SELECT * FROM `pricelist`;");
            while ($row = mysqli_fetch_array($printSql)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row["$name"]}</td>";
                echo "<td>{$row["$price1"]}</td>";
                echo "<td>{$row["$price2"]}</td>";
                echo "<td>{$row["$stock1"]}</td>";
                echo "<td>{$row["$stock2"]}</td>";
                echo "<td>{$row["$country"]}</td>";
                echo "<td>{$row["$notes"]}</td>";
                echo "</tr>";
            }

            function ()
        ?>

    </table>

    
</body>
</html>