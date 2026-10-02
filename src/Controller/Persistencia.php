<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Helper\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Persistencia implements RequestHandlerInterface
{
    use FlashMessageTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $rawDescricao = is_array($parsedBody) && isset($parsedBody['descricao'])
            ? trim((string) $parsedBody['descricao'])
            : '';

        if (empty($rawDescricao)) {
            $this->defineMensagem('danger', 'A descrição do curso não pode ser vazia');
            return new Response(302, ['Location' => '/listar-cursos']);
        }

        // Refactored: sanitize input safely without using deprecated FILTER_SANITIZE_STRING
        $descricao = strip_tags($rawDescricao);

        $queryParams = $request->getQueryParams();
        $idParam = isset($queryParams['id']) ? $queryParams['id'] : null;
        $id = filter_var($idParam, FILTER_VALIDATE_INT);

        if (!is_null($idParam) && $id !== false && $id !== null) {
            // Refactored: Fetch existing managed entity instead of calling deprecated merge() or setting ID manually
            /** @var Curso|null $curso */
            $curso = $this->entityManager->find(Curso::class, $id);
            if (is_null($curso)) {
                $this->defineMensagem('danger', 'Curso não encontrado para alteração');
                return new Response(302, ['Location' => '/listar-cursos']);
            }
            $curso->setDescricao($descricao);
            $this->defineMensagem('success', 'Curso atualizado com sucesso');
        } else {
            $curso = new Curso();
            $curso->setDescricao($descricao);
            $this->entityManager->persist($curso);
            $this->defineMensagem('success', 'Curso inserido com sucesso');
        }

        $this->entityManager->flush();

        return new Response(302, ['Location' => '/listar-cursos']);
    }
}
