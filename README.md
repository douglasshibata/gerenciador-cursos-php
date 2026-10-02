# Gerenciador de Cursos

Um sistema web MVC em PHP moderno para gerenciamento de cursos e controle de acesso de usuários. O projeto utiliza padrões da comunidade PHP (PSR-7, PSR-11, PSR-15) com **Doctrine ORM**, **PHP-DI** e **Nyholm PSR-7**.

---

## Estrutura e Arquitetura do Projeto

O projeto segue a arquitetura **Model-View-Controller (MVC)** desacoplada:

```text
├── config/             # Configurações de rotas e injeção de dependência (PHP-DI)
├── public/             # Ponto de entrada (Front Controller) e assets públicos
├── src/
│   ├── Controller/     # Controladores das requisições (PSR-15 RequestHandlers)
│   ├── Entity/         # Entidades de domínio mapeadas via Doctrine ORM
│   ├── Helper/         # Traits utilitárias (Flash Messages, Renderização HTML)
│   └── Infra/          # Configuração de persistência e EntityManager
├── tests/              # Suíte de testes automatizados PHPUnit
├── view/               # Templates HTML / PHP de visualização
├── db.sqlite           # Banco de dados SQLite
└── phpunit.xml         # Configuração do PHPUnit
```

---

## Requisitos de Ambiente

- **PHP 8.1+** (com extensões `pdo_sqlite`, `mbstring`, `xml`)
- **Composer 2.x**

---

## Configuração & Instalação

1. **Clonar o repositório:**
   ```bash
   git clone <url-do-repositorio>
   cd <diretorio-do-projeto>
   ```

2. **Instalar as dependências via Composer:**
   ```bash
   composer install
   ```

3. **Inicializar/Verificar o Banco de Dados:**
   O projeto utiliza banco de dados SQLite (`db.sqlite`).

4. **Executar a Aplicação:**
   Inicie o servidor embutido do PHP apontando para o diretório `public`:
   ```bash
   php -S localhost:8080 -t public
   ```
   Acesse no navegador: [http://localhost:8080](http://localhost:8080)

---

## Variáveis de Ambiente

Atualmente o banco de dados e as configurações principais são gerenciados através das classes em `config/` e `src/Infra/EntityManagerCreator.php`. Para ambientes de produção, as seguintes variáveis de ambiente podem ser configuradas no servidor web:

- `APP_ENV`: `development` / `production`
- `DATABASE_URL` or `DATABASE_PATH`: Caminho para o arquivo SQLite ou string de conexão do banco de dados.

---

## Como Executar a Suíte de Testes

O projeto utiliza **PHPUnit 10** para testes unitários e de integração.

Para rodar todos os testes automatizados, execute:
```bash
vendor/bin/phpunit
```

---

## Considerações de Segurança e Vulnerabilidades Resolvidas

Durante a auditoria e refatoração do código (Audit & Security Remediation), as seguintes melhorias foram implementadas:

1. **Prevenção contra Cross-Site Scripting (XSS):**
   - Todas as saídas dinâmicas em views (`title`, descrições de cursos, mensagens de erro e sucesso) foram tratadas com `htmlspecialchars()` com flag `ENT_QUOTES` para evitar injeção de scripts maliciosos.

2. **Compatibilidade com PHP 8+:**
   - Remoção de funções e constantes obsoletas (`FILTER_SANITIZE_STRING`).
   - Higienização e sanitização adequadas de inputs PSR-7 através de `$request->getParsedBody()` e `$request->getQueryParams()`.

3. **Higiene de Sessão e Prevenção de Session Fixation:**
   - Invocação de `session_regenerate_id(true)` no momento da autenticação do usuário em `RealizarLogin.php`.
   - Limpeza completa de variáveis de sessão e invalidação de cookies no encerramento de sessão em `Deslogar.php`.

4. **Correção de Mutação de Entidades e Erros do Doctrine ORM:**
   - Substituição da chamada legada e propensa a erros `entityManager->merge()` em `Persistencia.php` por busca da entidade gerenciada existente (`entityManager->find()`) e atualização de propriedades.
   - Adição de verificações de existência de entidades em `Exclusao.php` e `FormularioEdicao.php` para impedir exceções de referência nula (*Call to a member function on null*).
