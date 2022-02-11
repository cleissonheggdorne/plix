<?php


$filmesRepository = new FilmesRepositoryPDO();

$file = "F:/xampp/htdocs/scrapping/lista_com_filmes_de_animacao.txt";

if (file_exists($file)){
    if($filmesRepository->importarArquivo($file))
        echo "Arquivo importado com sucesso";
    else
        echo "Erro na importação do arquivo";
}else{
    echo "Arquivo Não Existe!";
}
?>

