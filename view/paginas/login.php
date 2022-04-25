<?php

if(isset($_SESSION)){
    session_start(); 
}

include "./view/estrutura/cabecalho.php";
require_once "./util/mensagem.php";
?>

<body class="purple darken-1 main">

    <div class="row login">

        <div class="card-form">
            <!--Card contendo os inputs -->
            <div class="card card-form-2">

                <div class="card-action purple darken-1 white-text center-align title-form">
                    <h3>PLIX</h3>
                </div>
                <form method="POST">
                    <div class="card-content">

                        <div class="form-field">
                            <label for="username">Usuário</label>
                            <input class="input__usuario" type="email" id="usuario" name="usuario" required>
                        </div><br>

                        <div class="form-field">
                            <label for="senha">Senha</label>
                            <input class="input__senha" type="password" id="senha" name="senha" required>
                        </div><br>

                        <div class="form-field center-align login-button">
                            <button class="btn-large waves-effect waves-light btn purple btn-login" type="submit">Login</button>
                        </div>

                        <div class="form-field center-align esqueci-senha">

                            <a class="modal-trigger" href="#modal-redefinir-senha">Esqueci a Senha</a>

                        </div>

                        <div class="form-field center-align esqueci-senha">

                            <a id="cadastro-usuario" href="/cadastro-de-usuario">Não tenho cadastro</a>

                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Modal Para Cadastro de Novo Filme -->
    <div class="row">
        <!-- Modal Structure -->
        <div id="modal-redefinir-senha" class="modal modal-fixed-footer">
            <!--Início Form-->
            <div class="modal-content">
                <span class="card-title blcak-text">Redefinir Senha</span>
                <form method="POST">
                    <div class="row">
                        <div class="col s12 m12 l12">
                            <div class="card-content">
                                <p>Caso possua login aqui no plix.app.br, digite seu email de cadastro abaixo
                                    e lhe enviaremos uma mensagem que contém um link para que possa redefinir sua senha.
                                </p>
                            </div>
                            <input id="email" type="email" class="validate" name="email_redefinir" value="" required>
                            <label for="email">Email de Cadastro</label>
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <a class="waves-effect waves-light btn grey" href="/login">Cancelar</a>
                <button type="submit" class="waves-effect waves-light btn purple">Enviar</a>
            </div>
            </form>
        </div>
    </div>

    
    <?php
    Mensagem::mostrar();
    require './view/estrutura/rodape.php';
    ?>
</body>
</html>

