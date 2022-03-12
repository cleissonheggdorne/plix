<?php include "cabecalho.php" ?>
<?php

require "./util/mensagem.php";

?>

<body>

<nav class="nav-extended purple darken-2">
        <!-- Define a cor da NavBar compreendendo o título central-->
        <div class="nav-wrapper">
            <a href="/inicio" class="brand-logo left">PLIX</a>
            <a href="#" data-target="mobile-demo" class="sidenav-trigger right"><i class="material-icons">menu</i></a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <!--Responsividade, esconde a barra quando a tela for média ou pequena-->
                <li class="active"><a href="/inicio">Galeria</a></li>
               
                <?php 
                if ($_SESSION['usuario'] != "") { ?>
                    <!--Verifica se usuario existe-->
                    
                  <a class='dropdown-trigger btn transparent' href='#' data-target='dropdown1'><?= $_SESSION['usuario'] ?></a>
                    <ul id='dropdown1' class='dropdown-content'>
                        <li><a onclick="sair()" href="#">Sair</a></li>
                        <li><a href="/favoritos">Favoritos</a></li>
                        <?php if ($_SESSION['usuario'] != "" && $_SESSION['admin'] == true) { ?>
                            <li><a href="/novo">Cadastrar Mídia</a></li>
                        <?php }?>
                    </ul>  

                <?php } else { ?>
                    <li><a href="/login">Entrar</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <?php 
    if ($_SESSION['usuario'] != "") { ?>
        <!--Verifica se usuario existe-->
        <ul class="sidenav purple lighten-3" id="mobile-demo">
            <li class="center purple darken-2"><?= $_SESSION['usuario'] ?></li>
            <li><a href="/favoritos">Favoritos</a></li>
            <li><a href="/syscontrol">Controle</a></li>
            <li><a onclick="sair()" href="#">Sair</a></li>
        </ul>
    <?php } else { ?>
        <ul class="sidenav purple lighten-3" id="mobile-demo">
            <li class="purple darken-2"><a href="/login">Entrar</a></li>
        </ul>
    <?php } ?>
    
        <div class="row">
        <div class="col s12 m6 l4 offset-l4 offset-m3">
        <form method="POST" enctype="multipart/form-data"> <!--Início Form-->
           
                <!--Card contendo os inputs -->
                <div class="card">
                    <div class="card-content white-text">
                        <span class="card-title black-text">Cadastrar Filme</span>
                        
                        <!--input do título -->
                        <div class="row">
                            <div class="input-field col s12">
                                <input id="titulo" type="text" class="validate" name="titulo" required>
                                <label for="titulo">Título do Filme</label>
                            </div>
                        </div>

                        <!--input da sinopse -->
                       

                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="sinopse" class="materialize-textarea" name="sinopse"></textarea>
                                <label for="textarea1">Sinopse</label>
                            </div>
                        </div>

                        <!--input da nota -->
                        <div class="row">
                            <div class="input-field col s4">
                                <input id="nota" type="number" step=".1" min=0 max=10 
                                    class="validate" required name="nota">
                                <label for="nota">Nota</label>
                            </div>
                        </div>

                        <!--input da capa do filme-->
                        <div class="file-field input-field">
                            <div class="btn purple lighten-2 black-text">
                                    <span>Capa</spam>
                                    <input type="file" name="poster_file">
                            </div>
                            <div class="input-path-wrapper">
                                    <input class="file-path validate" type="text" name="poster">
                            </div>
                        </div>
                        
                        <!--input do trailer -->
                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="sinopse" class="materialize-textarea" name="trailer"></textarea>
                                <label for="textarea1">Link Trailer</label>
                            </div>
                        </div>

                        <!--input do player -->
                        <div class="row">
                            <div class="input-field col s12">
                                <textarea id="sinopse" class="materialize-textarea" name="player"></textarea>
                                <label for="textarea1">URL Player do Filme</label>
                            </div>
                        </div>
                        <span class="card-title black-text">Importação de Arquivo CSV (Deixar em branco caso vá adicionar individualmente)</span>
                        <div class="file-field input-field">
                            <div class="btn purple lighten-2 black-text">
                                    <span>Arquivo CSV</spam>
                                    <input type="file" name="cvv_file">
                            </div>
                            <div class="input-path-wrapper">
                                    <input class="file-path validate" type="text" name="csv">
                            </div>
                        </div>

                        <!--Botões de ação-->
                        <div class="card-action">
                            <a class="waves-effect waves-light btn grey" href="/">Cancelar</a>
                            <button type="submit" class="waves-effect waves-light btn purple">Confirmar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?= Mensagem::mostrar();?>

    <?php 
include "rodape.php";
?>
</body>

</html>