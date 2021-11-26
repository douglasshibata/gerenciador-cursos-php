# Gerenciador de Cursos

Projeto realizado com auxíio as aulas da alura sobre MVC com PHP


## Montando ambiente

Instalar o php, em seguida verificar se o php está instalado com ``` php -version```.
Instalar o composer que gerencia os pacotes do php.

Para baixar as dependências execute
```bash
composer install
```

## Rodar o projeto
Para rodar o projeto execute: 
```bash
php -S localhost:8080 -t public
```

## Estrutura do Projeto
A pasta public é a principal do projeto, onde a partir dela será executada a aplicação.

Dentro da pasta src
a pasta Controller é realizada a lógica e controle da aplicação.

a pasta Entity é onde está localizado o modelo da aplicação.

a pasta infra possui a configuração para conectar com o banco de dados.

A pasta view possui os arquivos responsáveis pela visualização da aplicação rodando na web.
