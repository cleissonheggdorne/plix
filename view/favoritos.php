<?php include "cabecalho.php";
?>

<?php
//session_destroy();
require "./util/mensagem.php";
session_start();


$controller = new FilmesController();
$filmes = $controller->fav();

?>

<body>

    <nav class="nav-extended purple lighten-3">
        <!-- Define a cor da NavBar compreendendo o título central-->
        <div class="nav-wrapper">
            <ul id="nav-mobile" class="right">
                <!--Responsividade, esconde a barra quando atlea for média ou pequena-->
                <li class="active"><a href="/">Galeria</a></li>
                <?php if ($_SESSION['usuario'] != "") { ?>
                    <li><a href="/novo">Cadastrar</a></li>
                    <li><a href="/sair"><?= $_SESSION['usuario'] ?></a></li>
                    
                <?php }else {?>
                    <li><a href="/login">Entrar</a></li>
                <?php }?>
            </ul>
        </div>
        <div class="nav-header center">
            <h1>CLOROCINE</h1>
        </div>
        <div class="nav-content">
            <ul class="tabs tabs-transparent purple darken-1">
                <!--Define a cor da segunda barra de navegação com-->
                <li class="tab"><a href="/">TODOS</a></li>
                <li class="tab"><a class="active" href="/favoritos">FAVORITOS</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
    <div class="row">
        <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Nenhum filme favoritado</p>"?>
        <?php
            foreach($filmes as $filme): ?>
            <div class="col s7 m4 l4 xl3"> <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
               
                <div class="card">
                    <div class="card-image">
                        <img class="activator" src="<?= $filme->poster ?>">
                        <button class="btn-fav btn-floating halfway-fab waves-effect waves-light red"
                            data-id="<?= $filme->id ?>">
                            <i class="material-icons"><?= ($filme->favorito)?"favorite":"favorite_border" ?></i></button> <!--ícone Favorito-->
                    </div>
                    <div class="card-content">
                        <p class="valign-wrapper"> <!--Classe para alinhamento de elementos-->
                            <i class="material-icons amber-text">star</i><?= $filme->nota ?></p>
                        <span class="card-title"><strong><?= $filme->titulo ?></strong></span>
                    </div>
                        <div class="card-reveal">
                            <span class="card-title grey-text text-darken-4"><?= $filme->titulo ?><i class="material-icons right">close</i></span>
                        <p><?= substr($filme->sinopse, 0, 500)."..." ?></p>
                        <button class="waves-effect waves-light btn-small right red accent-2 btn-delete" data-id="<?=$filme->id?>">
                            <i class="material-icons">delete</i></button>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    </div>
 <?= Mensagem::mostrar();?>
 <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9897683100163895" crossorigin="anonymous"></script>
    <!-- Coluna anuncios -->
    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-9897683100163895" data-ad-slot="8869853545" data-ad-format="auto" data-full-width-responsive="true"></ins>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>

    <script>
        //DESFavoritar
        document.querySelectorAll(".btn-fav").forEach(btn=>{
            btn.addEventListener("click", e =>{ //Mudança do texto do html para modificar o ícone
                const id = btn.getAttribute("data-id") //informação do id do botão clicado
                fetch(`/favoritar/${id}`) //faz a solicitação delete
                .then(response => response.json()) //quando tiver a resposta, converto para json
                .then(response =>{ //após a conversão
                    if(response.success === "ok"){ //Verifico o atributo success, se OK
                        btn.querySelector("i").innerHTML="favorite_border" 
                        const card = btn.closest(".col") //pegar elemento mais próximo do botão
                        card.classList.add("fadeOut")//Adiciona classe com efeito de FadeOut
                        setTimeout(()=>card.remove(), 1000) //executa a função após certo tempo  
                    }
                })
                .catch(error => {
                    M.toast({html: "Erro ao Favoritar"})
                })
            });
        });
    </script>

    <script>
            //Delete
            document.querySelectorAll(".btn-delete").forEach(btn=>{
                btn.addEventListener("click", e =>{ 
                    const id = btn.getAttribute("data-id") //informação do id do botão clicado
                    const requestConfig = {method:"DELETE", headers: new Headers()}
                    fetch(`/filmes/${id}`, requestConfig) //faz a solicitação de deletar
                    .then(response => response.json()) //quando tiver a resposta, converto para json
                    .then(response =>{ //após a conversão
                        if(response.success === "ok"){ //Verifico o atributo success, se OK
                            const card = btn.closest(".col") //pegar elemento mais próximo do botão
                            card.classList.add("fadeOut")//Adiciona classe com efeito de FadeOut
                            setTimeout(()=>card.remove(), 1000) //executa a função após certo tempo
                        }
                    })
                    .catch(error => {
                        M.toast({html: "Erro ao Apagar"})
                    })
                });
            });
    </script>
<?php 
include "rodape.php";
?>
</body>


