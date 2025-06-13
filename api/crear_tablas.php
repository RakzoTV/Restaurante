<?php
include_once "encabezado.php";
include_once "funciones.php";

$host = "localhost";
$usuario = "root";
$password = "";
$resultados = [];

$crear_bd = "CREATE DATABASE IF NOT EXISTS restaurante";

$sentencias = [
    // Tabla rol
    ["tabla"=>"rol",
    "sentencia" =>
        'CREATE TABLE IF NOT EXISTS rol (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            nombre VARCHAR(100) NOT NULL
        );'],
    // Inserts de roles
    ["tabla"=>"rol_insert",
    "sentencia" =>
        'INSERT INTO rol (nombre) VALUES
            ("Administrador"),
            ("Cajero"),
            ("Mesero");'],
    // Tabla categorias
    ["tabla"=>"categorias",
    "sentencia" =>
        'CREATE TABLE IF NOT EXISTS categorias (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            tipo ENUM("PLATILLO", "BEBIDA") NOT NULL,
            nombre VARCHAR(50) NOT NULL,
            descripcion VARCHAR(255)
        );'],
    // Tabla insumos
    ["tabla"=>"insumos",
    "sentencia" =>
        'CREATE TABLE IF NOT EXISTS insumos (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            codigo VARCHAR(100) NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            descripcion VARCHAR(255) NOT NULL,
            precio DECIMAL(6,2) NOT NULL,
            tipo ENUM("PLATILLO", "BEBIDA") NOT NULL,
            categoria BIGINT UNSIGNED NOT NULL,
            FOREIGN KEY (categoria) REFERENCES categorias(id)
        );'],
    // Tabla informacion_negocio
    ["tabla"=>"informacion_negocio",
    "sentencia" =>
        'CREATE TABLE IF NOT EXISTS informacion_negocio (
            nombre VARCHAR(100),
            telefono VARCHAR(15),
            numeroMesas TINYINT,
            logo VARCHAR(255)
        );'],
    // Tabla usuarios
    ["tabla"=>"usuarios",
    "sentencia" =>
        'CREATE TABLE IF NOT EXISTS usuarios (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            correo VARCHAR(100) NOT NULL,
            nombre VARCHAR(100) NOT NULL,
            telefono VARCHAR(20) NOT NULL,
            password VARCHAR(255) NOT NULL,
            rol_id BIGINT UNSIGNED NOT NULL,
            FOREIGN KEY (rol_id) REFERENCES rol(id)
        );'],
    // Tabla ventas
    ["tabla"=>"ventas",
    "sentencia" =>
        'CREATE TABLE IF NOT EXISTS ventas (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            idMesa TINYINT NOT NULL,
            cliente VARCHAR(100),
            fecha DATETIME NOT NULL,
            total DECIMAL(6,2) NOT NULL,
            pagado DECIMAL(6,2) NOT NULL,
            idUsuario BIGINT UNSIGNED NOT NULL,
            FOREIGN KEY (idUsuario) REFERENCES usuarios(id)
        );'],
    // Tabla insumos_venta
    ["tabla"=>"insumos_venta",
    "sentencia" =>
        'CREATE TABLE IF NOT EXISTS insumos_venta (
            id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            idInsumo BIGINT UNSIGNED NOT NULL,
            precio DECIMAL(6,2) NOT NULL,
            cantidad INT NOT NULL,
            idVenta BIGINT UNSIGNED,
            FOREIGN KEY (idInsumo) REFERENCES insumos(id),
            FOREIGN KEY (idVenta) REFERENCES ventas(id)
        );'],
];

try {
    $conexion = new PDO("mysql:host=$host", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conexion->exec($crear_bd);
    $conexion->exec('USE Restaurante');

    foreach ($sentencias as $sentencia) {
        $conexion->exec($sentencia["sentencia"]);
        array_push($resultados, "Sentencia para {$sentencia["tabla"]} ejecutada correctamente");
    }

    $conexion = null;
    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}