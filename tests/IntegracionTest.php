<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/funciones.php';

class IntegracionTest extends TestCase
{
    public function testFlujoCompletoCRUDInsumo()
    {
        // Registrar insumo
        $nuevo = (object)[
            'codigo' => 'INT123',
            'nombre' => 'Insumo Integracion',
            'descripcion' => 'Desc integración',
            'precio' => 20,
            'tipo' => 'PLATILLO',
            'categoria' => 1 // Asegúrate que exista
        ];
        $this->assertTrue(registrarInsumo($nuevo));

        // Consultar insumo
        $insumos = obtenerInsumosPorNombre('Insumo Integracion');
        $this->assertNotEmpty($insumos);
        $insumo = $insumos[0];

        // Actualizar insumo
        $insumo->precio = 25;
        $this->assertTrue(editarInsumo($insumo));

        // Consultar insumo actualizado
        $insumosEditados = obtenerInsumosPorNombre('Insumo Integracion');
        $this->assertEquals(25, $insumosEditados[0]->precio);

        // Eliminar insumo
        $this->assertTrue(eliminarInsumo($insumo->id));

        // Verificar que fue eliminado
        $insumosBorrados = obtenerInsumosPorNombre('Insumo Integracion');
        $this->assertEmpty($insumosBorrados);
    }
}