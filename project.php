<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre o Projeto - DAW André Gonçalves</title>
</head>

<body>
    <?php include 'header.html' ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h1 class="card-title mb-0 text-center">PROJETO DAW ANDRÉ GONÇALVES</h1>
                    </div>
                    <div class="card-body">
                        <h4 class="text-center mb-4">CRUD - Create, Read, Update, Delete</h4>

                        <div class="row">
                            <div class="col-md-3">
                                <img src="images/php.jpeg" alt="PHP Logo" class="img-fluid shadow rounded" />
                            </div>
                            <div class="col-md-9">
                                <p class="lead">Este exercício simples tem como objetivo exemplificar as operações CRUD numa base de dados, utilizando PHP/PDO para o efeito.<br />Espera-se que este exemplo consolide os vossos conhecimentos sobre os conteúdos programáticos da disciplina e facilite a conclusão do trabalho resultante dos exercícios realizados em aula, no âmbito da avaliação contínua que tem decorrido ao longo do semestre.</p>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle"></i> Sobre o Desenvolvimento</h5>
                                    <p>A base do projeto foi criada pelo professor em aula. Foi adicionado a biblioteca Bootstrap 5 para facilitar o design do frontend.</p>
                                    <p>O projeto consiste em criar um stand virtual de compra e aluguer de carros. O sistema permite aos usuários gerenciar o inventário de veículos, realizando operações CRUD, utilizando PHP para a lógica de backend e PDO para a interação com a base de dados MySQL.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="fas fa-code"></i> Tecnologias Utilizadas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Frontend:</h6>
                                                <ul>
                                                    <li>HTML5</li>
                                                    <li>CSS3</li>
                                                    <li>Bootstrap 5</li>
                                                    <li>JavaScript (AJAX)</li>
                                                    <li>Font Awesome Icons</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Backend:</h6>
                                                <ul>
                                                    <li>PHP 7+</li>
                                                    <li>MySQL</li>
                                                    <li>PDO (PHP Data Objects)</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-success">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="fas fa-database"></i> Funcionalidades Implementadas</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Operações CRUD:</h6>
                                                <ul>
                                                    <li><strong>Create:</strong> Inserir novos clientes</li>
                                                    <li><strong>Read:</strong> Listar clientes existentes</li>
                                                    <li><strong>Update:</strong> Editar informações de clientes</li>
                                                    <li><strong>Delete:</strong> Remover clientes (soft delete via status)</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Recursos Adicionais:</h6>
                                                <ul>
                                                    <li>Toggle de status (Ativar/Desativar)</li>
                                                    <li>Validação de formulários</li>
                                                    <li>Interface responsiva</li>
                                                    <li>Feedback visual das operações</li>
                                                    <li><strong>Sistema de Autenticação:</strong> Login obrigatório com controlo de acesso baseado em roles</li>
                                                    <li>Proteção de rotas e segurança de sessão</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card border-info">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0"><i class="fas fa-folder-tree"></i> Estrutura do Projeto</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-folder"></i> Pasta API:</h6>
                                                <p>A pasta <code>api/</code> contém os endpoints REST desenvolvidos em PHP que separam a lógica de negócio da interface do usuário. Esta arquitetura permite:</p>
                                                <ul>
                                                    <li>Reutilização de código em diferentes interfaces</li>
                                                    <li>Separação clara entre frontend e backend</li>
                                                    <li>Facilidade de manutenção e testes</li>
                                                    <li>Possibilidade de integração com aplicações móveis</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <h6><i class="fas fa-file-code"></i> header.html:</h6>
                                                <p>O arquivo <code>header.html</code> contém a navegação comum a todas as páginas do sistema. Esta abordagem oferece:</p>
                                                <ul>
                                                    <li>Manutenção centralizada do menu de navegação</li>
                                                    <li>Consistência visual em todas as páginas</li>
                                                    <li>Facilidade para adicionar novas funcionalidades</li>
                                                    <li>Redução de código duplicado</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <h6><i class="fas fa-sitemap"></i> Organização Geral:</h6>
                                                <ul>
                                                    <li><strong>Raiz do projeto:</strong> Páginas principais (home.php, Clients.php, Cars.php, etc.)</li>
                                                    <li><strong>api/:</strong> Endpoints para operações CRUD</li>
                                                     <li><strong>images/:</strong> Guardar imagens locais</li>
                                                    <li><strong>header.html:</strong> Componente de navegação compartilhado</li>
                                                    <li><strong>session.php:</strong> Funções de gestão de sessão e controlo de acesso baseado em roles</li>
                                                    <li><strong>auth.php:</strong> Funções de autenticação, incluindo login, verificação de passwords e criação de utilizadores</li>
                                                    <li><strong>login.php:</strong> Página de autenticação com interface responsiva</li>
                                                    <li><strong>logout.php:</strong> Funcionalidade de terminação segura de sessão</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="lead mb-2">Realizado por André Gonçalves</p>
                            <p class="text-muted">Projeto Individual - Desenvolvimento de Aplicações Web</p>
                              <p class="text-muted">Estarei disponível para dúvidas, esclarecimentos e explicações sobre o projeto.</p>
                            <p class="text-muted">Data: 22 de Fevereiro de 2026</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>