<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/funciones.php';

class FuncionesTest extends TestCase
{
    public function testObtenerNumeroUsuarios()
    {
        $num = obtenerNumeroUsuarios();
        $this->assertIsInt($num, "Debe devolver un entero");
        $this->assertGreaterThanOrEqual(0, $num, "No puede ser negativo");
    }

    public function testObtenerVentasDelDia()
    {
        $ventas = obtenerVentasDelDia();
        $this->assertIsNumeric($ventas, "Debe devolver un número");
        $this->assertGreaterThanOrEqual(0, $ventas, "No puede ser negativo");
    }

    public function testObtenerNumeroInsumos()
    {
        $num = obtenerNumeroInsumos();
        $this->assertIsInt($num, "Debe devolver un entero");
        $this->assertGreaterThanOrEqual(0, $num, "No puede ser negativo");
    }

    public function testObtenerTotalVentas()
    {
        $total = obtenerTotalVentas();
        $this->assertIsNumeric($total, "Debe devolver un número");
        $this->assertGreaterThanOrEqual(0, $total, "No puede ser negativo");
    }

    public function testObtenerNumeroMesasOcupadas()
    {
        $num = obtenerNumeroMesasOcupadas();
        $this->assertIsInt($num, "Debe devolver un entero");
        $this->assertGreaterThanOrEqual(0, $num, "No puede ser negativo");
    }

    public function testObtenerInsumosMasVendidos()
    {
        $insumos = obtenerInsumosMasVendidos(5);
        $this->assertIsArray($insumos, "Debe devolver un array");
        $this->assertLessThanOrEqual(5, count($insumos), "No debe devolver más de 5");
        foreach ($insumos as $insumo) {
            $this->assertObjectHasProperty('nombre', $insumo);
            $this->assertObjectHasProperty('total', $insumo);
        }
    }

    public function testObtenerTotalesPorMesa()
    {
        $mesas = obtenerTotalesPorMesa();
        $this->assertIsArray($mesas, "Debe devolver un array");
        foreach ($mesas as $mesa) {
            $this->assertObjectHasProperty('mesa', $mesa);
            $this->assertObjectHasProperty('total', $mesa);
        }
    }

    public function testObtenerVentasPorMesesDeUsuario()
    {
        $anio = date('Y');
        $idUsuario = 1; // Cambia por un ID válido de tu base de datos de pruebas
        $ventas = obtenerVentasPorMesesDeUsuario($anio, $idUsuario);
        $this->assertIsArray($ventas, "Debe devolver un array");
        foreach ($ventas as $venta) {
            $this->assertObjectHasProperty('mes', $venta);
            $this->assertObjectHasProperty('totalVentas', $venta);
        }
    }

    public function testObtenerVentasPorDiaMes()
    {
        $mes = date('n');
        $anio = date('Y');
        $idUsuario = 1;
        $ventas = obtenerVentasPorDiaMes($mes, $anio, $idUsuario);
        $this->assertIsArray($ventas, "Debe devolver un array");
        foreach ($ventas as $venta) {
            $this->assertObjectHasProperty('dia', $venta);
            $this->assertObjectHasProperty('totalVentas', $venta);
        }
    }

    public function testObtenerVentasSemanaDeUsuario()
    {
        $idUsuario = 1;
        $ventas = obtenerVentasSemanaDeUsuario($idUsuario);
        $this->assertIsArray($ventas, "Debe devolver un array");
        foreach ($ventas as $venta) {
            $this->assertObjectHasProperty('dia', $venta);
            $this->assertObjectHasProperty('totalVentas', $venta);
        }
    }

    public function testObtenerVentasUsuario()
    {
        $fechaInicio = date('Y-m-01');
        $fechaFin = date('Y-m-t');
        $ventas = obtenerVentasUsuario($fechaInicio, $fechaFin);
        $this->assertIsArray($ventas, "Debe devolver un array");
        foreach ($ventas as $venta) {
            $this->assertObjectHasProperty('usuario', $venta);
            $this->assertObjectHasProperty('total', $venta);
        }
    }

    public function testObtenerVentasPorHora()
    {
        $fechaInicio = date('Y-m-d');
        $fechaFin = date('Y-m-d');
        $ventas = obtenerVentasPorHora($fechaInicio, $fechaFin);
        $this->assertIsArray($ventas, "Debe devolver un array");
        foreach ($ventas as $venta) {
            $this->assertObjectHasProperty('hora', $venta);
            $this->assertObjectHasProperty('total', $venta);
        }
    }
}