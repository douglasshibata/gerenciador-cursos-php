<?php

namespace Alura\Cursos\Tests\Controller;

use Alura\Cursos\Controller\CursosEmJson;
use Alura\Cursos\Controller\CursosEmXml;
use Alura\Cursos\Controller\ListarCursos;
use Alura\Cursos\Entity\Curso;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class ApiAndListControllersTest extends TestCase
{
    private function getMockCourses(): array
    {
        $c1 = new Curso();
        $c1->setId(1);
        $c1->setDescricao('PHP');

        $c2 = new Curso();
        $c2->setId(2);
        $c2->setDescricao('Doctrine');

        return [$c1, $c2];
    }

    public function testListarCursos(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn($this->getMockCourses());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Curso::class)
            ->willReturn($repository);

        $controller = new ListarCursos($entityManager);
        $request = new ServerRequest('GET', '/listar-cursos');

        $response = $controller->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = (string) $response->getBody();
        $this->assertStringContainsString('PHP', $body);
        $this->assertStringContainsString('Doctrine', $body);
    }

    public function testCursosEmJson(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn($this->getMockCourses());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Curso::class)
            ->willReturn($repository);

        $controller = new CursosEmJson($entityManager);
        $request = new ServerRequest('GET', '/buscarCursosEmJson');

        $response = $controller->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['application/json'], $response->getHeader('Content-Type'));
        $this->assertJsonStringEqualsJsonString(
            '[{"id":1,"descricao":"PHP"},{"id":2,"descricao":"Doctrine"}]',
            (string) $response->getBody()
        );
    }

    public function testCursosEmXml(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $repository->expects($this->once())
            ->method('findAll')
            ->willReturn($this->getMockCourses());

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Curso::class)
            ->willReturn($repository);

        $controller = new CursosEmXml($entityManager);
        $request = new ServerRequest('GET', '/buscarCursosEmXml');

        $response = $controller->handle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['application/xml'], $response->getHeader('Content-Type'));
        $this->assertStringContainsString('<curso><id>1</id><descricao>PHP</descricao></curso>', (string) $response->getBody());
    }
}
