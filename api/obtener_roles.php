<?php
include_once "encabezado.php";
include_once "funciones.php";

$roles = obtener_roles();

echo json_encode($roles);