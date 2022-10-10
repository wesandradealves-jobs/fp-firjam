/*
 * CONTROLE DE ESCOPO
 * Projeto: Casa Firjan
 * Desenvolvimento: GM5
 */

var attrController = $('main[data-controller]').attr('data-controller');

var Controller = {
  getController: function () {
    if ($('main[data-controller]').length > 0) {
      eval('Controller.' + attrController + '();');
    }
  },
  global: function(){

    //
    // Menu Mobile
    //
    Util.menuMobile();
    
    //
    // Background solid do header
    //
    Util.solidHeader();
    
    //
    // Parallax
    //
    new universalParallax().init({
      speed: 2.5
    });
    
    // 
    // HOTSITE SCRIPTS
    // 
    Util.hotsiteCarouselSchedule();
    Util.cutReadMore();
    Util.hotsiteCrouselWorkshop();
    //
    // MODAL PALESTRANTE
    //
    Util.modalPalestrante();
    
    Util.lgpdView();

    //
    // PAGINA TRANSMISSAO
    //
    Util.videoTransmissao();

    //
    // COPIAR URL
    //
   
    
  },
  home: function(){

    //
    // TESTE SUBMIT FORM OFICINA
    //
    var form = $('.formOficina');
    // form.on('click touch', '.btn.btnNext.first', function() {
      

    //   var form_data = new FormData();                 
    //   form_data.append('comprovante', form.find('input#comprovante').prop("files")[0])            
    //   form_data.append('nome', form.find('input#nome').val())         
    //   form_data.append('email', form.find('input#email').val())         
    //   form_data.append('telefone', form.find('input#telefone').val())         
    //   form_data.append('cpf', form.find('input#cpf').val())          
    //   form_data.append('cnpj', form.find('input#cnpj').val())          
    //   form_data.append('empresa', form.find('input#empresa').val())          
    //   form_data.append('associado', form.find('input[name="associado"]').val())          
    //   form_data.append('oficina', form.find('input#oficina').val()) 


    //   $.ajax({
    //     url: "/process/oficinas",
    //     dataType: 'script',
    //     cache: false,
    //     contentType: false,
    //     processData: false,
    //     data: form_data,                         
    //     type: 'post'
    //   });

    // });  

    //
    // COUNTDOWN
    //
    Util.countdown();
    
    //
    // TabsAgenda
    //
    Util.tabsAgenda();   
    
    //
    // Modal de Inscrição
    //
    Util.modalInscricao();

    //
    // Modal oficina
    //

    
    Util.modalOficina();
    
    //
    // Modal Experiencia Imersiva
    //

    Util.modalExperienciaImersiva();

    //
    // Modal Edicoes Anteriores
    //
    Util.modalEdicoesAnteriores();

    Util.modalQuemPassou();
    //
    // load more videos
    //
    Util.loadmoreItens();

    $('.owl-carousel').owlCarousel({
      stagePadding: 100,
      loop:false,
      margin:20,
      nav:true,
      lazyLoad:true,
      dots: false,
      responsive:{
          0:{
            margin:10,  
            items:1
          },
          300:{
            margin:10,
            items: 1
          },
          500:{
              margin:160,
              items:3
          },
          700:{
              margin:60,
              items:3
          },
          1025:{
            margin:20,
            items:4
        }
      
        }
    });

    $('.owl-carousel-publicacoes').owlCarousel({
      loop:false,
      center: false,
      stagePadding: 25,
      margin:30,
      nav:true,
      responsive:{
          0:{
              items:1
          },
          600:{
              items:3
          },
          1000:{
              items:3
          }
      }
    });
     
    
  },
  palestras: function(){
    
    
    //
    // Modal Transmissões
    //
    Util.modalTransmissao();
    
    //
    // Ajax para atualizar as tags de transmissão ao vivo
    //
    // setInterval(function(){ 
    //   console.log('debug')
    //   $.get( window.location.href, function( data ) {
    //     $(data).find('.itemPalestra').each(function( index ) {
    //       var vid = $(this).data('vid');
    //       if($(this).find('.tagOnLive').length){
    //         console.log('on '+vid)
    //         if(!$('.itemPalestra[data-vid="'+vid+'"]').find('.tagOnLive').length){
    //           $('.itemPalestra[data-vid="'+vid+'"]').find('.horario').append('<span class="tagOnLive">TRANSMITINDO AO VIVO</span>');
    //         }
    //       } else {
    //         $('.itemPalestra[data-vid="'+vid+'"]').find('.tagOnLive').remove();
    //       }
    //     });
    //   });
    // }, 4000);
    
    
    // setInterval(function(){ 
    //   $('.itemPalestra').find('.tagOnLive').remove();
    //   $.get( '/rest/aovivo?_format=json', function( data ) {
    //     $(data).each(function() {
    //       var vid = this.field_live_pt.split('/').slice(-1)[0];
    //       if(!$('.itemPalestra[data-vid="'+vid+'"]').find('.tagOnLive').length){
    //         $('.itemPalestra[data-vid="'+vid+'"]').find('.horario').append('<span class="tagOnLive">TRANSMITINDO AO VIVO</span>');
    //       }
    //     });
    //   });
    // }, 300000);
    
  },
  user: function(){
    
    //
    // Validações em Minha Conta
    //
    Util.minhaConta();

    
    //
    // JS para oficinas
    //
    Util.oficinas();
    
    
    if($('main').hasClass('login')){
      if($('div[role="alert"]').length){
        var mensagem = $('div[role="alert"]').clone();
        
        $('#block-futurospossiveis2020-content').prepend(mensagem);
      }
      
      if($('form.user-login-form').length){
        $('#edit-actions').after('<div class="textPassword"> <p class="mt-3">Esqueceu a sua senha? <a href="/user/password">Clique aqui</a></p></div>')
      }
    }
        
  },
  galeria: function(){
  }
};

jQuery(document).ready(function ($) {
  Controller.global();
  Controller.getController();
});

(console.info || console.log).call(console, "%c<Dev by 🐱 GM5 Team/>", "color: green; font-weight: bold;");
