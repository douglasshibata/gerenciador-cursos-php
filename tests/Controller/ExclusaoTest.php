<?php

namespace Alura\Cursos\Tests\Controller;

use Alura\Cursos\Controller\Exclusao;
use Alura\Cursos\Entity\Curso;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class ExclusaoTest extends TestCase
{
    public function testExclusaoSuccess(): void
    {
        $curso = new Curso();
        $curso->setId(3);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('find')
            ->with(Curso::class, 3)
            ->willReturn($curso);
        $entityManager->expects($this->once())
            ->method('remove')
            ->with($curso);
        $entityManager->expects($this->once())
            ->method('flush');

        $controller = new Exclusao($entityManager);

        $request = (new ServerRequest('GET', '/excluir-curso'))
            ->withQueryParams(['id' => '3']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(['/listar-cursos'], $response->getHeader('Location'));
    }

    public function testExclusaoNonExistentCourse(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('find')
            ->with(Curso::class, 999)
            ->willReturn(null);
        $entityManager->expects($this->never())->method('remove');

        $controller = new Exclusao($entityManager);

        $request = (new ServerRequest('GET', '/excluir-curso'))
            ->withQueryParams(['id' => '999']);

        $response = $controller->handle($request);

        $this->assertEquals(302, $response->getStatusCode());
    }
}
