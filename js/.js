document.addEventListener('DOMContentLoaded', function() {
    var nvFilme = document.getElementById('modal-novo-filme');
    var importEmMassa = document.getElementById('modal-import-em-massa');
    var edtSlide = document.getElementById('modal-edit-slides');
    var sidenav = document.querySelectorAll('.sidenav');
    var carrossel_destaques = document.querySelectorAll('.carousel');
    var parallax = document.querySelectorAll('.parallax');
    var dropdown = document.querySelectorAll('.dropdown-trigger');
    var tabs     = document.querySelectorAll('.tabs');
    var redefinirSenha = document.getElementById('modal-redefinir-senha');

    var instances_nvFilme = M.Modal.init(nvFilme);
    var instances_importEmMassa = M.Modal.init(importEmMassa);
    var instances_edtSlide = M.Modal.init(edtSlide);
    var instances_sidenav = M.Sidenav.init(sidenav);
    var instances_carrossel_destaques = M.Carousel.init(carrossel_destaques);
    var instances_parallax = M.Parallax.init(parallax);
    var instances_dropdown = M.Dropdown.init(dropdown, {
        coverTrigger:false,
        hover:true
    });
    var instance_tabs = M.Tabs.init(tabs);
    var redefinir_senha = M.Modal.init(redefinirSenha); //modal redefinir senha
});

// Sair
function sair() {
    var confirmar = confirm("Deseja Sair?");
    if (confirmar == true) {
        window.location.href = "/sair";
    }
}

//Login
$(document).ready(function(){
    $("#btn_login").click(function(){
        var dados = new FormData();
        dados.append('usuario', $("#usuario").val());
        dados.append('senha', $("#senha").val());
        $.ajax({
            url: 'controller/auxControllerComAjax.php', //Caminho script
            method: 'POST',
            data: dados,
            processData: false,
            contentType: false,
            success: function(resposta){
                console.log(resposta);
                if(resposta['dados'] != false){
                    if(resposta['situacao'] === 'ativo'){
                        window.location.href = '/';
                    }else if (resposta['situacao'] === 'aguardando confirma????o'){
                        M.toast({html: 'Ops, precisamos que confirme seu email!'});
                    }else{
                        M.toast({html: 'Seu usu??rio est?? inativo! Redefina a senha para ativ??-lo.'});
                    }
                }else{
                    c
                }
             }
        })
    });
    $("#btn-redefinir").click(function(){
        var dados = new FormData();
        dados.append('usuario', $("#usuario").val());
        dados.append('senha1', $("#senha1").val());
        dados.append('senha2', $("#senha2").val());
        //if(dados['senha1'] != dados['senha2']){
            M.toast({html: 'Senhas n??o conferem'});
        //}
        // $.ajax({
        //     url: 'controller/auxControllerComAjax.php', //Caminho script
        //     method: 'POST',
        //     data: dados,
        //     processData: false,
        //     contentType: false,
        //     success: function(resposta){
        //         console.log(resposta);
        //         if(resposta['dados'] != false){
        //             if(resposta['situacao'] === 'ativo'){
        //                 window.location.href = '/';
        //             }else if (resposta['situacao'] === 'aguardando confirma????o'){
        //                 M.toast({html: 'Ops, precisamos que confirme seu email!'});
        //             }else{
        //                 M.toast({html: 'Seu usu??rio est?? inativo! Redefina a senha para ativ??-lo.'});
        //             }
        //         }else{
        //             M.toast({html: 'Email ou Senha Incorretos'});
        //         }
        //      }
        // })
    });

})

// Redefinir Senha
async function redefinirSenha(){
    let id_input_email_recupera = document.getElementById('id_input_email_recupera');
    let email = id_input_email_recupera.value;
    let dados = await fetch('/login?recupera='+ email);//, metodo);
    let resposta = await dados.json();
    M.toast({html: resposta});
}

async function editarFilme(id){
    var edtFilme = document.getElementById('modal-editar-filme');
    var dados = await fetch('/syscontrol?id-para-editar=' + id + '&tipo=pag_syscontrol')
    var resposta = await dados.json();
    console.log(resposta);

    var instances_edtFilme = M.Modal.init(edtFilme)

    document.getElementById("id-edt").value = resposta['dados'][0].id
    document.getElementById("titulo-edt").value = resposta['dados'][0].titulo
    document.getElementById("url-poster-edt").value = resposta['dados'][0].poster
    document.getElementById("sinopse-edt").value = resposta['dados'][0].sinopse
    document.getElementById("nota-edt").value = resposta['dados'][0].nota
    document.getElementById("url-edt").value = resposta['dados'][0].url
    document.getElementById("chave-trailer-edt").value = resposta['dados'][0].trailer
    document.getElementById("back-1-edt").value = resposta['dados'][0].img_wide_1
    document.getElementById("back-2-edt").value = resposta['dados'][0].img_wide_2
    document.getElementById("back-3-edt").value = resposta['dados'][0].img_wide_3

    instances_edtFilme.open()
}

//Auto Complete Tela Novo Filme
async function pesquisaTitulos(valor){
    if(valor.length >= 3 ){
        var dados = await fetch('/syscontrol?titulo-para-buscar=' + valor + '&tipo=api');

        var resposta = await dados.json();
        var options = {};
        var tituloEId = {}; //Utilizado no retorno do clique 
        for(i = 0; i<resposta['dados'].length; i++){
            options[resposta['dados'][i].titulo] = resposta['dados'][i].poster;
            tituloEId[resposta['dados'][i].titulo]       = resposta['dados'][i].id;
        }
        var elems = document.getElementById('titulobuscado');
        var instances = M.Autocomplete.init(elems,
            {data: options,
            minLength: 3,
            onAutocomplete:async function(res){ //Traz informa????es para a tela
                if(res in tituloEId){
                    var idTmdb = tituloEId[res];
                    var dadosInfoFilmesApi =  await fetch('/syscontrol?id-filme-tmdb=' + idTmdb);
                    dadosInfoFilmesApi = await dadosInfoFilmesApi.json();
                    document.getElementById("titulo").value = dadosInfoFilmesApi[0].title
                    document.getElementById("sinopse").innerHTML = dadosInfoFilmesApi[0].sinopse
                    document.getElementById("nota").value = dadosInfoFilmesApi[0].nota
                    document.getElementById("url-poster").value = dadosInfoFilmesApi[0].poster
                    document.getElementById("chave-trailer").value = dadosInfoFilmesApi[1].trailer
                    document.getElementById("back-1").value = dadosInfoFilmesApi[0].back_1
                }
            }
            }
        );  
    }
}

//Auto Complete
async function buscaDestaque(valor){

    if(valor.length >= 3 ){
        var dados = await fetch('/syscontrol?titulo-para-buscar=' + valor+ '&tipo=db');
         var resposta = await dados.json();
        
         var options = {};
         var tituloEId = {}; //Utilizado no retorno do clique 
         for(i = 0; i<resposta['dados'].length; i++){
             options[resposta['dados'][i].titulo] = resposta['dados'][i].poster;
             tituloEId[resposta['dados'][i].titulo]       = resposta['dados'][i].id;
         }
          
         var elems = document.getElementById('titulobuscadodestaque');
         var instances = M.Autocomplete.init(elems,
            {data: options,
             minLength: 3,
             onAutocomplete:async function(res){ //Traz informa????es para a tela
                 if(res in tituloEId){
                     var idSelecionado = tituloEId[res]
                     selecionaSlide(idSelecionado)
                 }
             }
            }
        );  
     }
}

async function selecionaSlide(idNovo){
    var input_modal_id_anterior = document.querySelector('#input_modal_id_anterior')
    var id_anterior = input_modal_id_anterior.value
    var dados = await fetch('/syscontrol?id-para-destaque=' + idNovo + '&id-anterior='+ id_anterior)
    var resposta = await dados.json()
    location.reload()
}


//Auto Complete Seleciona Slide
async function pesquisaParaSlide(valor){
    if(valor.length >= 3 ){
        var dados = await fetch('/syscontrol?titulo-para-salvar=' + valor);

        var resposta = await dados.json();
        var options = {};
        var tituloEId = {}; //Utilizado no retorno do clique 
        for(i = 0; i<resposta['dados'].length; i++){
            options[resposta['dados'][i].titulo] = resposta['dados'][i].poster;
            tituloEId[resposta['dados'][i].titulo]       = resposta['dados'][i].id;
        }

        var elems = document.getElementById('titulobuscadoslide');
        var instances = M.Autocomplete.init(elems,
            {data: options,
            minLength: 3,
            onAutocomplete:async function(res){ //Traz informa????es para a tela
                if(res in tituloEId){
                    // console.log('id tmdb; '+ tituloEId[res]);
                    var idTmdb = tituloEId[res];
                    var dadosInfoFilmesApi =  await fetch('/syscontrol?id-filme-tmdb=' + idTmdb);
                    dadosInfoFilmesApi = await dadosInfoFilmesApi.json();
                   // console.log(dadosInfoFilmesApi);
                    document.getElementById("titulo").value = dadosInfoFilmesApi[0].title
                    document.getElementById("sinopse").innerHTML = dadosInfoFilmesApi[0].sinopse
                    document.getElementById("nota").value = dadosInfoFilmesApi[0].nota
                    document.getElementById("url-poster").value = dadosInfoFilmesApi[0].poster
                    document.getElementById("chave-trailer").value = dadosInfoFilmesApi[1].trailer
                    document.getElementById("back-1").value = dadosInfoFilmesApi[0].back_1
                }
            }
            }
        );  
    }
}



