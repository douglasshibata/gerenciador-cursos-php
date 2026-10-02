<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Helper\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Exclusao implements RequestHandlerInterface
{
    use FlashMessageTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $idParam = isset($queryParams['id']) ? $queryParams['id'] : null;
        $id = filter_var($idParam, FILTER_VALIDATE_INT);

        $resposta = new Response(302, ['Location' => '/listar-cursos']);
        if ($id === false || is_null($id)) {
            $this->defineMensagem('danger', 'Curso inexistente');
            return $resposta;
        }

        // Refactored: Fetch real entity and check for existence before removal to avoid fatal ORM errors
        $curso = $this->entityManager->find(Curso::class, $id);
        if (is_null($curso)) {
            $this->defineMensagem('danger', 'Curso não encontrado');
            return $resposta;
        }

        $this->entityManager->remove($curso);
        $this->entityManager->flush();
        $this->defineMensagem('success', 'Curso excluído com sucesso');

        return $resposta;
    }
}
