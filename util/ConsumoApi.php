<?php
// require "api.php";

ini_set('max_execution_time', 0); //Arquivo executará até finalzar tempo de processamento


// $filmesRepository = new FilmesRepositoryPDO;
// $titulos = $filmesRepository->retornaTitulos();
// imprimeInfo($titulos);


class ConsumoApi{
    
    private $BASE_URL = "https://api.themoviedb.org/3";
    private $SEARCH = "/search/movie?query=";
    private $API_KEY = "api_key=e315cdd022eef7722a561f93fc7bd107";
    private $LANGUAGE = "language=pt-BR";

    public function buscaResultados($title):array{
        $url = $this->BASE_URL.$this->SEARCH.$title.'&'.$this->API_KEY.'&'.$this->LANGUAGE;
        $resultado = json_decode(file_get_contents($url));
        foreach($resultado->results as $value){
              $retornoPelaApi[] = [
                  'id' => $value->id,
                  'titulo' => $value->title,
                  'poster' => "https://www.themoviedb.org/t/p/original/".$value->poster_path
                ];
        } 
        $dados = ['dados'=>$retornoPelaApi];
        return $dados;
    }

    public function buscaInfoFilmesApi($id){
        $filmePorId = $this->BASE_URL."/movie/".$id."?".$this->API_KEY."&".$this->LANGUAGE;
        $resultado = json_decode(file_get_contents($filmePorId));
        //foreach($resultado as $value){
        $retornoInfoFilme[] = [
            'title'=>$resultado->title,
            'sinopse'=>$resultado->overview,
            'back_1'=>'https://www.themoviedb.org/t/p/original'.$resultado->backdrop_path,
            'poster'=>'https://www.themoviedb.org/t/p/original'.$resultado->poster_path,
            'nota'=>$resultado->vote_average
        ];

        $trailerFilme = $this->BASE_URL."/movie/".$id."/videos?".$this->API_KEY."&".$this->LANGUAGE;
        $resultadoTrailer = json_decode(file_get_contents($trailerFilme));

        $retornoInfoFilme[] = ['trailer'=>$resultadoTrailer->results[0]->key];
       
        return $retornoInfoFilme;
        }


}


// function imprimeInfo($titulos){
//     echo "VALOR RETORNADO";
//     foreach($titulos as $value){
//         print_r($value);
//         echo "<br>";
//         echo "<hr>";
//     }
// }

// function imprimeInfoRetornada($titulos){
//     echo "VALOR RETORNADO";
//     foreach($titulos as $key => $value){
//         echo("Título: ".$value." || Background: ".$key);
//         echo "<br>";
//         echo "<hr>";
//     }
// }

// function buscaInfo( $titulo){
//     $titleRep = str_replace(" ", "%20", $titulo->titulo);
//     $url = "https://api.themoviedb.org/3/search/movie?query=$titleRep&api_key=$key&language=pt-BR";
//     $resultado = json_decode(file_get_contents($url));
//     foreach($resultado->results as $value){
//         if($value->backdrop_path != "" && ($value->backdrop_path != " ") && ($value->backdrop_path != NULL)) 
//             if(!in_array($titulo->titulo, $titulo_e_Back)){
//                 echo "https://www.themoviedb.org/t/p/original".$value->backdrop_path;
//                 echo "<br>";
//                 echo "<hr>";
//                 $titulo_e_Back["https://www.themoviedb.org/t/p/original".$value->backdrop_path] = $titulo->titulo;
//             }  
//     } 
// }

// $titulo_e_Back = array();
//  foreach($titulos as $titulo){
     
//      $titleRep = str_replace(" ", "%20", $titulo->titulo);
//      $url = "https://api.themoviedb.org/3/search/movie?query=$titleRep&api_key=$key&language=pt-BR";

//      $resultado = json_decode(file_get_contents($url));

//      foreach($resultado->results as $value){
//         if($value->backdrop_path != "" && ($value->backdrop_path != " ") && ($value->backdrop_path != NULL)) 
//             if(!in_array($titulo->titulo, $titulo_e_Back)){
//                 echo "https://www.themoviedb.org/t/p/original".$value->backdrop_path;
//                 echo "<br>";
//                 echo "<hr>";
//                 $titulo_e_Back["https://www.themoviedb.org/t/p/original".$value->backdrop_path] = $titulo->titulo;
//             }  
//      } 
//  }

//  imprimeInfoRetornada($titulo_e_Back);

//  $filmesRepository->atualizaInfoBack($titulo_e_Back);