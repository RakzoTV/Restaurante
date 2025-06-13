<?php
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class ApiIntegracionTest extends TestCase
{
    public function testRegistrarInsumoEndpoint()
    {
        $client = new Client(['base_uri' => 'http://localhost/Restaurante/api/']);
        $response = $client->post('registrar_insumo.php', [
            'json' => [
                'codigo' => 'API123',
                'nombre' => 'Insumo API',
                'descripcion' => 'Desc API',
                'precio' => 30,
                'tipo' => 'PLATILLO',
                'categoria' => 1
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getBody());
        var_dump($body);
        $this->assertTrue($body->resultado);
    }
}