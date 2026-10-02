<?php

namespace Alura\Cursos\Tests\Controller;

use Alura\Cursos\Controller\Persistencia;
use Alura\Cursos\Entity\Curso;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class PersistenciaTest extends TestCase
{
    public function testPersistenciaNewCourseSuccess(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Curso::class));
        $entityManager->expects($this->once())
            ->method('flush');

        $controller = new Persistencia($entityManager);

        $request = (new ServerRequest('POST', '/salvar-curso'))
            ->withParsedBody(['descricao' => 'Novo Curso PHP']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(['/listar-cursos'], $response->getHeader('Location'));
    }

    public function testPersistenciaUpdateCourseSuccess(): void
    {
        $curso = new Curso();
        $curso->setId(5);
        $curso->setDescricao('Curso Antigo');

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('find')
            ->with(Curso::class, 5)
            ->willReturn($curso);
        $entityManager->expects($this->once())
            ->method('flush');

        $controller = new Persistencia($entityManager);

        $request = (new ServerRequest('POST', '/salvar-curso'))
            ->withQueryParams(['id' => '5'])
            ->withParsedBody(['descricao' => 'Curso Atualizado']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('Curso Atualizado', $curso->getDescricao());
    }

    public function testPersistenciaEmptyDescriptionFails(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())->method('flush');

        $controller = new Persistencia($entityManager);

        $request = (new ServerRequest('POST', '/salvar-curso'))
            ->withParsedBody(['descricao' => '   ']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(['/listar-cursos'], $response->getHeader('Location'));
    }
}
