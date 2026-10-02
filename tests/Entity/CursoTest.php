<?php

namespace Alura\Cursos\Tests\Entity;

use Alura\Cursos\Entity\Curso;
use PHPUnit\Framework\TestCase;

class CursoTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $curso = new Curso();
        $curso->setId(1);
        $curso->setDescricao('PHP MVC');

        $this->assertEquals(1, $curso->getId());
        $this->assertEquals('PHP MVC', $curso->getDescricao());
    }

    public function testJsonSerialization(): void
    {
        $curso = new Curso();
        $curso->setId(10);
        $curso->setDescricao('Doctrine ORM');

        $json = json_encode($curso);
        $this->assertJsonStringEqualsJsonString(
            '{"id":10,"descricao":"Doctrine ORM"}',
            $json
        );
    }
}
