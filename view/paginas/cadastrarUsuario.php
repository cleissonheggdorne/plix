<?php 
include "./view/estrutura/cabecalho.php";

require_once "./util/mensagem.php";
session_start();
?>

<body class="purple darken-1 main">
    
        <div class="row login">

            <div class="card-form">
                <!--Card contendo os inputs -->
                <div class="card card-form-2">

                    <div class="card-action purple darken-1 white-text center-align title-form">
                        <h5>Cadastrar Usuário</h5>
                    </div>
                    <form method="POST" name="cadastroUsuario">
                    <div class="card-content">
                        
                        <div class="form-field">
                            <label for="username">Email</label>
                            <input class="input__usuario" type="email" id="usuario" name = "usuario" required value="<?=$_SESSION['usuarioCadastro']?>">
                        </div><br>
                    

                        <div class="form-field">
                            <label for="senha">Senha</label>
                            <input class="input__senha" type="password" id="senha" name = "senha1" required>
                        </div><br>

                        <div class="form-field">
                            <label for="senha">Senha</label>
                            <input class="input__senha" type="password" id="senha" name = "senha2" required>
                        </div><br>

                        <!-- <div class="form-field">
                        <label>
                            <input type="checkbox" class="filled-in" checked="checked"/>
                                <span>Lembrar</span>
                        </label>
                        </div><br> -->

                    
                        <div class="form-field center-align login-button">
                            <button class="btn-large waves-effect waves-light btn purple btn-login" type="submit">Cadastrar</button>
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