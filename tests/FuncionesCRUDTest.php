<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../api/funciones.php';

class FuncionesCRUDTest extends TestCase
{
    // ---------- CRUD INSUMO ----------
    public function testCRUDInsumo()
    {
        // 1. Crear insumo
        $nuevo = (object)[
            'codigo' => 'TEST123',
            'nombre' => 'Insumo Test',
            'descripcion' => 'Desc test',
            'precio' => 10,
            'tipo' => 'PLATILLO',
            'categoria' => 1 // Usa un ID de categoría válido
        ];
        $registrado = registrarInsumo($nuevo);
        $this->assertTrue($registrado, "Debe registrar el insumo");

        // 2. Obtener insumo por nombre
        $insumos = obtenerInsumosPorNombre('Insumo Test');
        $this->assertIsArray($insumos, "Debe devolver un array");
        $this->assertNotEmpty($insumos, "Debe encontrar el insumo creado");
        $insumo = $insumos[0];
        $this->assertEquals('Insumo Test', $insumo->nombre);

        // 3. Actualizar insumo
        $insumo->nombre = 'Insumo Test Editado';
        $actualizado = editarInsumo($insumo);
        $this->assertTrue($actualizado, "Debe actualizar el insumo");

        // 4. Obtener insumo actualizado
        $insumosEditados = obtenerInsumosPorNombre('Insumo Test Editado');
        $this->assertNotEmpty($insumosEditados, "Debe encontrar el insumo editado");

        // 5. Eliminar insumo
        $eliminado = eliminarInsumo($insumo->id);
        $this->assertTrue($eliminado, "Debe eliminar el insumo");

        // 6. Verificar que ya no existe
        $insumosBorrados = obtenerInsumosPorNombre('Insumo Test Editado');
        $this->assertEmpty($insumosBorrados, "No debe encontrar el insumo eliminado");
    }

    // ---------- CRUD CATEGORIA ----------
    public function testCRUDCategoria()
    {
        // 1. Crear categoría
        $nueva = (object)[
            'tipo' => 'PLATILLO',
            'nombre' => 'Categoría Test',
            'descripcion' => 'Descripción test'
        ];
        $registrada = registrarCategoria($nueva);
        $this->assertTrue($registrada, "Debe registrar la categoría");

        // 2. Obtener categoría por nombre
        $todas = obtenerCategorias();
        $categorias = array_filter($todas, function($cat) {
            return $cat->nombre === 'Categoría Test';
        });
        $this->assertNotEmpty($categorias, "Debe encontrar la categoría creada");
        $categoria = array_values($categorias)[0];

        // 3. Actualizar categoría
        $categoria->nombre = 'Categoría Test Editada';
        $categoria->descripcion = 'Descripción editada';
        $actualizada = editarCategoria($categoria);
        $this->assertTrue($actualizada, "Debe actualizar la categoría");

        // 4. Obtener categoría actualizada
        $todasEditadas = obtenerCategorias();
        $categoriasEditadas = array_filter($todasEditadas, function($cat) {
            return $cat->nombre === 'Categoría Test Editada';
        });
        $this->assertNotEmpty($categoriasEditadas, "Debe encontrar la categoría editada");

        // 5. Eliminar categoría
        $categoriaEditada = array_values($categoriasEditadas)[0];
        $eliminada = eliminarCategoria($categoriaEditada->id);
        $this->assertTrue($eliminada, "Debe eliminar la categoría");

        // 6. Verificar que ya no existe
        $todasBorradas = obtenerCategorias();
        $categoriasBorradas = array_filter($todasBorradas, function($cat) {
            return $cat->nombre === 'Categoría Test Editada';
        });
        $this->assertEmpty($categoriasBorradas, "No debe encontrar la categoría eliminada");
    }
}