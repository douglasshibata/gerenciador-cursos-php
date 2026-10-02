<?php

namespace Alura\Cursos\Tests\Controller;

use Alura\Cursos\Controller\FormularioEdicao;
use Alura\Cursos\Entity\Curso;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class FormularioEdicaoTest extends TestCase
{
    public function testFormularioEdicaoSuccess(): void
    {
        $curso = new Curso();
        $curso->setId(2);
        $curso->setDescricao('Refactoring PHP');

        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with(2)
            ->willReturn($curso);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Curso::class)
            ->willReturn($repository);

        $controller = new FormularioEdicao($entityManager);

        $request = (new ServerRequest('GET', '/alterar-curso'))
            ->withQueryParams(['id' => '2']);

        $response = $controller->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Refactoring PHP', (string) $response->getBody());
    }

    public function testFormularioEdicaoNotFound(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Curso::class)
            ->willReturn($repository);

        $controller = new FormularioEdicao($entityManager);

        $request = (new ServerRequest('GET', '/alterar-curso'))
            ->withQueryParams(['id' => '999']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(['/listar-cursos'], $response->getHeader('Location'));
    }
}
