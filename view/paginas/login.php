<?php 
session_start();
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
                            <input class="input__usuario" type="email" id="usuario" name = "usuario" required>
                        </div><br>
                    

                        <div class="form-field">
                            <label for="senha">Senha</label>
                            <input class="input__senha" type="password" id="senha" name = "senha" required>
                        </div><br>

                        <div class="form-field">
                        <label>
                            <input type="checkbox" class="filled-in" checked="checked"/>
                                <span>Lembrar</span>
                        </label>
                        </div><br>

                    
                        <div class="form-field center-align login-button">
                            <button class="btn-large waves-effect waves-light btn purple btn-login" type="submit">Login</button>
                        </div>

                        <div class="form-field center-align esqueci-senha">
                      
                            <a href="#">Esqueci a Senha</a>
                        
                        </div>  
                        
                        <div class="form-field center-align esqueci-senha">
                      
                            <a href="/cadastro-de-usuario">Cadastrar</a>
                        
                        </div>

                    </div>
                    </form>

                </div>
            </div>
        </div>
        <?= Mensagem::mostrar(); ?>
        <?php
            REQUIRE './view/estrutura/rodape.php';
        ?>
</body>

</html>