<?php 
include "./view/estrutura/cabecalho.php";
// include "./controller/FilmesController.php";
require_once "./util/mensagem.php";
session_start();
$_SESSION['usuarioCadastro'] = $_SESSION['dados']['dados']['usuario'];
?>

<body class="purple darken-1 main">
    
        <div class="row login">

            <div class="card-form">
                <!--Card contendo os inputs -->
                <div class="card card-form-2">

                    <div class="card-action purple darken-1 white-text center-align title-form">
                        <h5>Redefinir Senha</h5>
                    </div>
                    <!-- <form method="POST" name="cadastroUsuario"> -->
                    <div class="card-content">
                        
                        <div class="form-field">
                            <label for="username">Email</label>
                            <input class="input__usuario" type="email" id="usuario" name = "usuario" required value="<?=$_SESSION['usuarioCadastro']?>" disabled>
                        </div><br>
                    

                        <div class="form-field">
                            <label for="senha">Senha</label>
                            <input class="input__senha" type="password" id="senha1" name = "senha1" required>
                        </div><br>

                        <div class="form-field">
                            <label for="senha">Senha</label>
                            <input class="input__senha" type="password" id="senha2" name = "senha2" required>
                        </div><br>
                        <div class="form-field center-align login-button">
                            <button id="btn-redefinir" class="btn-large waves-effect waves-light btn purple btn-login" type="submit">Redefinir</button>
                        </div>
   
                    </div>
                    <!-- </form> -->

                </div>
            </div>
        </div>
        <?= Mensagem::mostrar(); ?>
        <?php
            REQUIRE './view/estrutura/rodape.php';
        ?>
</body>

</html>