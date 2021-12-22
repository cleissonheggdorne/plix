<?php include "cabecalho.php" ?>

<body>

    <nav class="nav-extended purple lighten-3">
        <!-- Define a cor da NavBar compreendendo o título central-->
        <div class="nav-wrapper">
            <ul id="nav-mobile" class="right">
                <!--Responsividade, esconde a barra quando atlea for média ou pequena-->
                <li><a href="/">Galeria</a></li>
                <li class="active"><a href="cadastrar.php">Cadastrar</a></li>
            </ul>
        </div>
        <div class="nav-header center">
            <h1> CLOROCINE</h1>
        </div>
    </nav>
    <div class="row">

        <form method="POST" enctype="multipart/form-data"> <!--Início Form-->
            <div class="col s12 m6 l4 offset-l4 offset-m3">
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

                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea id="sinopse" class="materialize-textarea" name="sinopse"></textarea>
                                    <label for="textarea1">Sinopse</label>
                                </div>
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

                        <div class="card-action">
                            <a class="waves-effect waves-light btn grey" href="/">Cancelar</a>
                            <button type="submit" class="waves-effect waves-light btn purple">Confirmar</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>

</html>