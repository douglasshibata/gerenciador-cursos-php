<?php

namespace Alura\Cursos\Tests\Helper;

use Alura\Cursos\Helper\RenderizadorDeHtmlTrait;
use PHPUnit\Framework\TestCase;

class RenderizadorDeHtmlTraitTest extends TestCase
{
    public function testRenderizaHtml(): void
    {
        $class = new class {
            use RenderizadorDeHtmlTrait;
        };

        $html = $class->renderizaHtml('login/formulario.php', [
            'titulo' => 'Login Test'
        ]);

        $this->assertStringContainsString('Login Test', $html);
        $this->assertStringContainsString('<form action="/realiza-login"', $html);
    }
}
