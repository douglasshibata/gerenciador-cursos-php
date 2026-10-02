<?php

namespace Alura\Cursos\Tests\Entity;

use Alura\Cursos\Entity\Usuario;
use PHPUnit\Framework\TestCase;

class UsuarioTest extends TestCase
{
    public function testSenhaEstaCorretaWithValidPassword(): void
    {
        $usuario = new Usuario();

        // Use Reflection to set private $senha property for testing
        $reflection = new \ReflectionClass(Usuario::class);
        $property = $reflection->getProperty('senha');
        $property->setAccessible(true);
        $property->setValue($usuario, password_hash('123456', PASSWORD_ARGON2I));

        $this->assertTrue($usuario->senhaEstaCorreta('123456'));
        $this->assertFalse($usuario->senhaEstaCorreta('senha_errada'));
    }
}
