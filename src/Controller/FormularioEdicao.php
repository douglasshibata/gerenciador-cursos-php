<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Helper\FlashMessageTrait;
use Alura\Cursos\Helper\RenderizadorDeHtmlTrait;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormularioEdicao implements RequestHandlerInterface
{
    use RenderizadorDeHtmlTrait, FlashMessageTrait;

    private $repositorioCursos;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repositorioCursos = $entityManager
            ->getRepository(Curso::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $idParam = isset($queryParams['id']) ? $queryParams['id'] : null;
        $id = filter_var($idParam, FILTER_VALIDATE_INT);

        $respostaRedirect = new Response(302, ['Location' => '/listar-cursos']);
        if ($id === false || is_null($id)) {
            $this->defineMensagem('danger', 'ID de curso inválido');
            return $respostaRedirect;
        }

        // Refactored: Validate that course exists before rendering edit form to prevent call on null
        /** @var Curso|null $curso */
        $curso = $this->repositorioCursos->find($id);
        if (is_null($curso)) {
            $this->defineMensagem('danger', 'Curso não encontrado');
            return $respostaRedirect;
        }

        $html = $this->renderizaHtml('cursos/formulario.php', [
            'curso' => $curso,
            'titulo' => 'Alterar curso ' . $curso->getDescricao(),
        ]);

        return new Response(200, [], $html);
    }
}
