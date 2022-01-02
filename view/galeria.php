<?php include "cabecalho.php";
?>

<?php

require_once "./util/mensagem.php";
session_start();


$controller = new FilmesController();
$filmes = $controller->index();

?>

<body>

    <nav class="nav-extended purple lighten-3">
        <!-- Define a cor da NavBar compreendendo o título central-->
        <div class="nav-wrapper">
            <ul id="nav-mobile" class="right">
                <!--Responsividade, esconde a barra quando atlea for média ou pequena-->
                <li class="active"><a href="/">Galeria</a></li>
                <li><a href="/novo">Cadastrar</a></li>
            </ul>
        </div>
        <div class="nav-header center">
            <h1>CLOROCINE</h1>
        </div>
        <div class="nav-content">
            <ul class="tabs tabs-transparent purple darken-1 active">
                <!--Define a cor da segunda barra de navegação com-->
                <li class="tab"><a class="active" href="/">TODOS</a></li>
                <li class="tab"><a class="" href="/favoritos">FAVORITOS</a></li>
            </ul>
        </div>
    </nav>
    <div class="container">
    <div class="row">
        <?php if (!$filmes) echo "<p class='card-panel red lighten-4'>Não há filmes cadastrados</p>"?>
        <?php
            foreach($filmes as $filme): ?>
            <div class="col s12 m6 l4 xl3"> <!--Define tamanho dos cards de acordo com o tamanho da coluna-->
              
                <div class="card">
                <a href="/assistir/<?= str_replace(' ', '-', $filme->titulo)."?id=".($filme->id) ?>">
                    <div class="card-image">
                        <img class="activator" src="<?= (str_contains($filme->poster,'imagens/posters'))?  '/'.$filme->poster : $filme->poster //Verifica se é url ou diretorio ?>">
                    </div>
                </a>
                    <div class="card-content">
                        <div>
                        <button class="btn-fav btn-floating halfway-fab waves-effect waves-light red"
                            data-id="<?= $filme->id ?>">
                            <i class="material-icons"><?= ($filme->favorito)?"favorite":"favorite_border" ?></i><!--ícone Favorito-->
                        </button> 
                        </div>
                        <div>
                        <a href="/editar/?id=<?= $filme->id ?>"> 
                        <button style="position: absolute; right: 10px; top: 70px; " class="btn-edit btn-floating halfway-fab waves-effect waves-light black">
                            <i class="material-icons">edit</i><!--ícone Edit-->
                        </button></a> 
                        </div>
                        <p class="valign-wrapper"> <!--Classe para alinhamento de elementos-->
                            <i class="material-icons amber-text">star</i><?= $filme->nota ?>
                        </p>
                       
                        <span class="card-title"><strong><?= $filme->titulo?></strong></span>
                        <!--<button class="waves-effect waves-light btn-small right red accent-2 btn-delete" data-id="<?=$filme->id?>">
                            <i class="material-icons">delete</i></button>-->
                    </div>
                  
                </div>
              
            </div>
        <?php endforeach ?>
    </div>
    </div>
 <?= Mensagem::mostrar();?>

    <script>
        //Favoritar
        document.querySelectorAll(".btn-fav").forEach(btn=>{
            btn.addEventListener("click", e =>{ //Mudança do texto do html para modificar o ícone
                const id = btn.getAttribute("data-id") //informação do id do botão clicado
                fetch(`/favoritar/${id}`) //faz a solicitação delete
                .then(response => response.json()) //quando tiver a resposta, converto para json
                .then(response =>{ //após a conversão
                    if(response.success === "ok"){ //Verifico o atributo success, se OK
                        if(btn.querySelector("i").innerHTML==="favorite"){ //Faço a troca
                            btn.querySelector("i").innerHTML="favorite_border" 
                        }else{
                           btn.querySelector("i").innerHTML="favorite"
                        }
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

</body>

</html>