<?php
$creditos = $creditos - 1;
$sql = "UPDATE login SET creditos = '$creditos' WHERE usuario_api = '$usuario_api'";
$query = mysqli_query($conn,$sql);
?>