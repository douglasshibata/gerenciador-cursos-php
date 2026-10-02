<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Usuario;
use Alura\Cursos\Helper\FlashMessageTrait;
use Doctrine\ORM\EntityManagerInterface;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RealizarLogin implements RequestHandlerInterface
{
    use FlashMessageTrait;

    private $repositorioDeUsuarios;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repositorioDeUsuarios = $entityManager
            ->getRepository(Usuario::class);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        $emailInput = is_array($parsedBody) && isset($parsedBody['email']) ? (string) $parsedBody['email'] : '';
        $senhaInput = is_array($parsedBody) && isset($parsedBody['senha']) ? (string) $parsedBody['senha'] : '';

        $email = filter_var($emailInput, FILTER_VALIDATE_EMAIL);

        $redirecionamentoLogin = new Response(302, ['Location' => '/login']);
        if ($email === false || empty($email)) {
            $this->defineMensagem(
                'danger',
                'O e-mail digitado não é um e-mail válido.'
            );

            return $redirecionamentoLogin;
        }

        /** @var Usuario|null $usuario */
        $usuario = $this->repositorioDeUsuarios
            ->findOneBy(['email' => $email]);

        if (is_null($usuario) || !$usuario->senhaEstaCorreta($senhaInput)) {
            $this->defineMensagem('danger', 'E-mail ou senha inválidos');

            return $redirecionamentoLogin;
        }

        // Fix: Ensure active session and regenerate session ID to prevent Session Fixation attacks
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_regenerate_id(true);
        $_SESSION['logado'] = true;

        return new Response(302, ['Location' => '/listar-cursos']);
    }
}
