<?php include "cabecalho.php" ?>
<?php
$controller = new FilmesController();
//$filmes = $controller->index();
//$controller = new FilmesController();
$infoFilme = $controller-> buscaInfoFilme($_GET['id']);

?>
<body>
<?php echo $_SESSION['admin'] ?>
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
                            <li><a href="/syscontrol">Controle</a></li>
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
                <!--Card contendo os inputs -->
                <div class="card">
                    <div class="card-content white-text">
                    <?php foreach ($infoFilme as $info) : ?>
                        <span class="card-title black-text">Editar Informações</span> 
                            <div class="card-action">
                                
                                <button class="waves-effect waves-light btn-small right red accent-2 btn-delete" data-id="<?=$info->id?>">
                                    <i class="material-icons">delete</i></button>
                             
                            </div>
                            <form method="POST" enctype="multipart/form-data"> <!--Início Form-->
                            <!--input do título -->
                            <div class="row">
                            
                                <div class="input-field col s12">
                                    <input id="titulo" type="text" class="validate" name="titulo" value="<?= $info->titulo ?>" required>
                                    <input type="hidden" name="id" value="<?= $info->id?>">
                                    <label for="titulo">Título do Filme</label>
                                </div>
                            </div>

                            <!--input da sinopse -->
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea id="sinopse" class="materialize-textarea" name="sinopse" value=""><?= $info->sinopse ?></textarea>
                                    <label for="textarea1">Sinopse</label>
                                </div>
                            </div>

                            <!--input da nota -->
                            <div class="row">
                                <div class="input-field col s4">
                                    <input id="nota" type="number" step=".1" min=0 max=10 
                                        class="validate" required name="nota" value="<?= $info->nota ?>">
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
                                        <input class="file-path validate" type="text" name="poster" value="<?= $info->poster ?>">
                                </div>
                            </div>

                            <!--input do trailer -->
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="trailer" type="text" class="validate" name="trailer" value="<?= $info->trailer ?>" required>
                                    <label for="titulo">ID do Trailer</label>
                                </div>
                            </div>

                            <!--input do player -->
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="player" type="text" class="validate" name="player" value="<?= $info->url ?>" required>
                                    <label for="titulo">URL do Player</label>
                                </div>
                            </div>
                            <?php endforeach ?>
                            <!--Botões de ação-->
                            <div class="card-action">
                                <a class="waves-effect waves-light btn grey" href="/">Cancelar</a>
                                <button type="submit" class="waves-effect waves-light btn purple">Confirmar Edições</a>
                            </div>
                        </form>
                        
                    </div>
                    
                </div>
            </div>
       
                        
    </div>
    
    <script>
            //Delete
            
            document.querySelectorAll(".btn-delete").forEach(btn=>{
                btn.addEventListener("click", e =>{ 
                    var confirmar = confirm("Tem ceteza que deseja excluir?");
                    if(confirmar == true){
                        const id = btn.getAttribute("data-id") //informação do id do botão clicado
                        const requestConfig = {method:"DELETE", headers: new Headers()}
                        fetch(`/filmes/${id}`, requestConfig) //faz a solicitação de deletar
                        .then(response => response.json()) //quando tiver a resposta, converto para json
                        .then(response =>{ //após a conversão
                        
                            if(response.success === "ok"){ //Verifico o atributo success, se OK
                                <?php $_SESSION["msg"] = "Conteúdo excluído com sucesso!";?>
                                window.location.href = "/novo";
                                //M.toast({html: "Excluído com sucesso!"})
                            }
                        })
                        .catch(error => {
                            M.toast({html: "Erro ao excluir"})
                        })   
                    }
                    
                });
            });
    </script>

<?php 
include "rodape.php";
?>
</body>

