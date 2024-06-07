<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = $_POST['field1'] . ", " . $_POST['field2'] . "\n";
  $file = 'data.txt'; // Имя файла, в который будем сохранять данные
  file_put_contents($file, $data, FILE_APPEND); // Сохраняем данные в файл
  echo "Данные сохранены!";
}
?>