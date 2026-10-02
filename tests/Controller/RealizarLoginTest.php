<?php

namespace Alura\Cursos\Tests\Controller;

use Alura\Cursos\Controller\RealizarLogin;
use Alura\Cursos\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class RealizarLoginTest extends TestCase
{
    public function testRealizarLoginInvalidEmail(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $controller = new RealizarLogin($entityManager);

        $request = (new ServerRequest('POST', '/realiza-login'))
            ->withParsedBody(['email' => 'email-invalido', 'senha' => '123456']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(['/login'], $response->getHeader('Location'));
    }

    public function testRealizarLoginSuccess(): void
    {
        $usuario = new Usuario();
        $reflection = new \ReflectionClass(Usuario::class);
        $property = $reflection->getProperty('senha');
        $property->setAccessible(true);
        $property->setValue($usuario, password_hash('secret123', PASSWORD_ARGON2I));

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => 'user@example.com'])
            ->willReturn($usuario);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Usuario::class)
            ->willReturn($repository);

        $controller = new RealizarLogin($entityManager);

        $request = (new ServerRequest('POST', '/realiza-login'))
            ->withParsedBody(['email' => 'user@example.com', 'senha' => 'secret123']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(['/listar-cursos'], $response->getHeader('Location'));
    }
}
