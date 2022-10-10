/*
 * FUNÇÕES NECESSÁRIAS
 * Projeto: Casa Firjan
 * Desenvolvimento: GM5
 */

 var Util = {
	menuMobile: function(){
        
		menu = $('header nav ul');
			
        $('header .mobile button').on('click touchstart', function(e){
			var self = $(this);
			$('html').toggleClass('activeMenu');
			if ( self.hasClass('active') ){
				self.removeClass('active');
				menu.removeClass('active');
				
				// menu.removeClass('d-block');
				// menu.addClass('d-none');
			}else{
				self.addClass('active');
				menu.addClass('active');
				
				// menu.removeClass('d-none');
				// menu.addClass('d-block');
			}
			e.preventDefault();
		});
		
        // Fecha Menu se fizer resize acima de resolução Tablet
        $(window).resize(function() {
            if ($(window).width() > 992 && $('.btnMobile').hasClass('active')){                
                $('.btnMobile').trigger('click');
            }
        });
        
        // Fecha Menu se clicar em link do menu
        menu.on('click touch', 'li a', function(){
            $('.btnMobile').trigger('click');
           
        }) 
    },
    countdown: function(){
        if($('.countdown')[0]){
            
            var dataEvento     = $('.countdown').data('evento'),
            countDownDate   = new Date(dataEvento).getTime();
            
            setInterval(function() {
        
              // Get today's date and time
              var now = new Date().getTime();
            
              // Find the distance between now and the count down date
              var distancia = countDownDate - now;
            
              // Time calculations for days, hours, minutes and seconds
              var dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
              var horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
              var minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
              var segundos = Math.floor((distancia % (1000 * 60)) / 1000);
            
              $('.countdown .dias p:first-child').html(dias);
              $('.countdown .horas p:first-child').html(horas);
              $('.countdown .minutos p:first-child').html(minutos);
              $('.countdown .segundos p:first-child').html(segundos);
            
              // If the count down is finished, write some text
              if (distancia < 0) {
                clearInterval(x);
                document.getElementById("demo").innerHTML = "EXPIRED";
              }
            }, 1000);
        } 
    },
    solidHeader: function(){
        
        var positionScroll = document.body.scrollWidth < 768 ? 100 : 200;
        
        // Define o bacground ao carregar a página
        if ($(window).scrollTop() > positionScroll) {
            $('header, header .mobile').addClass('solid');
        }
          
        // Define o background do header ao rolar o scroll
        $(window).scroll(function() {
          if ($(this).scrollTop() > positionScroll) {
              $('header, header .mobile').addClass('solid');
          } else {
              $('header, header .mobile').removeClass('solid');
          }
        });
        
    },
    tabsAgenda: function(){
        var agenda = $('.programacao .agenda');
    
        agenda.on('click touch', '.datasTab li', function(){
            
            //Oculta todos os ContentItens
            agenda.find('.contentsTab .item').removeClass('active');
              
            //Retira aa classea active
            agenda.find('.datasTab li').removeClass('active');
              
            //Adiciona a classe active
            $(this).addClass('active');
              
            //Item clicado
            var item = $(this).data('item');
              
            //Atica o respectivo contentTab
            agenda.find('.contentsTab .item').each( function (){
                if($(this).data('item') == item){
                    $(this).addClass('active');;
                }
            });
            
            //Corrige Bug de espaço no BG devido ao Parallax - ajuste aqui
            // window.dispatchEvent(new Event('resize'));
      
        });
    },
    modalInscricao: function(){
        //
        // Validações e troca de steps
        //
        var form    = $('.formInscricao'),
        firstStep   = form.find('.firstStep'),
        secondStep  = form.find('.secondStep'),
        thirdStep   = form.find('.thirdStep'),
        finishStep  = form.find('.finishStep');
        
        //
        // Nacionalidade e Documentação
        //
        form.on('change', 'input[name="nacionalidade"]', function(){
            form.find('input[name="nacionalidade"]').removeAttr('required');
            if($(this).prop('id') == 'brasileiro'){
                form.find('#cpf').attr('required', '').parent().show('slow');
                form.find('#passaporte').removeAttr('required').parent().hide('slow');
            } else {
                form.find('#passaporte').attr('required', '').parent().show('slow');
                form.find('#cpf').removeAttr('required').parent().hide('slow');
            }
        });

        //
        // Como ficou sabendo?
        //
        form.on('change', 'select[name="como_ficou_sabendo"]', function(){
            if($(this).val() == 'outro'){
                
                form.find('#outro_como_ficou_sabendo').attr('required', '').parent().show('slow');
            } else { 
                           
                form.find('#outro_como_ficou_sabendo').removeAttr('required').parent().hide('slow');
            }
        });

        form.on('click touch', '.btnNext', function(){
            
            var self = $(this);
            
            if($(this).hasClass('first')) // Primeiro Step
            {
                form.removeClass('was-validated');
                form.find('.firstStep input[required], .firstStep select[required]').each(function(){

                  if($(this).attr('type') == 'radio'){
              
                    // console.log($(this).is(":checked"))
                  }


                  if($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio' && $(this).val() == ''){ //$(this).attr('type') != 'checkbox'
                   
             
                        form.addClass('was-validated');
                  
                  }

                  if($(this).attr('type') == 'checkbox' &&  $(this).is(":checked") == false){
                   
                    form.addClass('was-validated');
                  }

                  if($(this).attr('type') == 'radio' &&  $(this).is(":checked") == false){
                   
                    form.addClass('was-validated');
                  }
                })
                
                // Testa CPF
                if(form.find('#brasileiro').is(':checked') && Util.testaCPF(form.find('#cpf').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cpf').addClass('is-invalid');
                } else {
                    form.find('#cpf').removeClass('is-invalid');
                }
                
                // Testa CNPJ
                if(form.find('#cnpj').val().length > 0 && Util.testaCNPJ(form.find('#cnpj').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cnpj').addClass('is-invalid');
                } else {
                    form.find('#cnpj').removeClass('is-invalid');
                }

                //Valida Senha
                if( form.find('#senha').val().length < 6 ){
                  
                    form.addClass('was-validated');
                    form.find('#senha').addClass('is-invalid');
                }else{
                    form.find('#senha').removeClass('is-invalid');
                }
                
                if( form.find('#senha2').val() != form.find('#senha').val() ){
                    form.addClass('was-validated');
                    form.find('#senha2').addClass('is-invalid');
                }else{
                    form.find('#senha2').removeClass('is-invalid');
                }
                
                // if(!form.hasClass('was-validated')){
                //   firstStep.hide("slow");
                //   secondStep.show("slow");
                // }
                
                if(!form.hasClass('was-validated')){
                    var NomeCompleto = $("#nome").val();
                    var Nacionalidade = $("#brasileiro").is(':checked') ? '&Nacionalidade=brasileiro' : '&Nacionalidade=estrangeiro';
                    var Documento = $("#brasileiro").is(':checked') ? '&CPF=' + $("#cpf").val() : '&Passaporte=' + $("#passaporte").val();
                    var Email = $("#email").val();
                    var CNPJ = $("#cnpj").val();
                    var NomeEmpresa = $("#empresa").val();
                    var Nascimento = $("#nascimento").val();
                    var Telefone = $("#telefone").val();
                    var CEP = $("#cep").val();
                    var Endereco = $("#endereco").val();
                    var Numero = $("#numero").val();
                    var Complemento = $("#complemento").val();                     
                    var FicouSabendo = $("#como_ficou_sabendo").val();                     
                    var OutroFicouSabendo = $("#outro_como_ficou_sabendo").val();                    
                    var Password = $("#senha2").val(); 
                    var Oficinas = [];
                    form.find('.secondStep input[type="checkbox"]:checked').each(function() {
                         Oficinas.push($(this).val());
                    })
                    var dataString = 'NomeCompleto='+ NomeCompleto + Nacionalidade + Documento + '&Email='+ Email + '&CNPJ='+ CNPJ+ '&NomeEmpresa='+ NomeEmpresa +'&Password=' + Password + '&Nascimento=' + Nascimento + '&Telefone=' + Telefone + '&Endereco=' + Endereco + '&Numero=' + Numero + '&Complemento=' + Complemento + '&FicouSabendo=' + FicouSabendo  + '&OutroFicouSabendo=' + OutroFicouSabendo  + '&CEP=' + CEP + '&Oficinas=' + Oficinas.join('|');
                   
                    $(this).prop( "disabled", true ).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Aguarde...');
                    form.find('.btnBack, input').prop( "disabled", true );
                
                    $.ajax({
                        type: "POST",
                        url: "/process/cadastro",
                        data: dataString,
                        cache: false,
                        success: function(result) {
                            
                            if(result.error)
                            {
                                self.parent().find('.alert-danger').show('slow').html(result.error);
                            } 
                            else
                            {
                                //Mostra mensagem de obriagdo
                                firstStep.hide("slow");
                                finishStep.show("slow");
                                
                                //limpa os campos e informa cadastro relizado com sucesso
                                $('.form-control').val('');
                                $('.form-check-input').prop('checked', false);
                            }
                        },
                        error: function(error) {
                            alert("Ocorreu um erro ao enviar os dados.");
                            // console.log(error);
                        }
                    }).done( function(data){
                        form.find('.btnNext').prop( "disabled", false ).html('Finalizar Inscrição');
                        form.find('.btnBack, input').prop( "disabled", false );
                        
                    });
                }
                
            }
            else // Segundo Step
            {
                if(!form.hasClass('was-validated')){
                  secondStep.hide("slow");
                  thirdStep.show("slow");
                }
            }
            
        });
        
        //
        // Validação do CEP e Autocomplete do endereço
        //
        form.find('#cep').focusout(function(){
            var self = $(this),
            cep = $(this).val().replace(/\_|\-|\//g,''),
            urlAPI = '//viacep.com.br/ws/'+cep+'/json/';
            
            if(cep.length == 8){
                $.get(urlAPI, function( data ) {
                    if(data.erro == true){
                        form.addClass('was-validated');
                        self.addClass('is-invalid');
                    } else {
                        self.removeClass('is-invalid');
                        form.find('#endereco').val(data.logradouro + ', ' + data.bairro + ', ' + data.localidade + ' - ' + data.uf);
                    }
                });
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        //
        // Caso selecione alguma oficina altera o botão
        //
        form.on('click touch', '.secondStep input[type="checkbox"]', function() {
            
            var arrayFields = [];
            form.find('.thirdStep .oficinasEscolhidas .target').text('');
            
            form.find('.secondStep input[type="checkbox"]:checked').each(function(e) {
                arrayFields.push(e); 
                form.find('.thirdStep .oficinasEscolhidas .target').append('<p>'+$(this).next('label').text()+'</p>');
            })
            if(arrayFields.length){
                form.find('.secondStep button[type="submit"]').hide();
                form.find('.secondStep a.btnNext').show();
                form.find('.thirdStep .oficinasEscolhidas').show();
                form.find('.thirdStep .required').attr('required', '');
            }
            else
            {
                form.find('.secondStep .btnSubmit').show();
                form.find('.secondStep .btnNext').hide();
                form.find('.thirdStep .oficinasEscolhidas').hide();
                form.find('.thirdStep .required').removeAttr('required')
            }
        })
        
        //
        // botão voltar
        //
        form.on('click touch', '.btnBack', function(){
            
            if($(this).hasClass('second')) // Segundo Step
            {
                secondStep.hide("slow");
                firstStep.show("slow");
            }
            else if($(this).hasClass('third')) // Terceiro Step
            {
                thirdStep.hide("slow");
                secondStep.show("slow");
            }
            else // Último Step
            {
                finishStep.hide("slow");
                firstStep.show("slow");
            }
            
            form.find('.alert-danger').hide();
        })
        
        //
        // Mascaras
        //
        form.find('#cpf').mask("999.999.999-99");
        form.find('#cnpj').mask("99.999.999/9999-99");
        form.find('#nascimento').mask("99/99/9999");
        form.find('#telefone').mask("(99) 99999999?9");
        form.find('#cep').mask("99999-999");
        
        //
        // Oficinas do Modal
        //
        form.on('click touch', '.detalheOfinica .btn', function() {
            var expand = $(this).parent().hasClass('expand');
            $('#modalInscricao .detalheOfinica').removeClass('expand');
            $('#modalInscricao .detalheOfinica .btn span').html('+');
            if(expand){
              $(this).parent().removeClass('expand');
              $(this).find('span').html('+')
            } else {
              $(this).parent().addClass('expand');
              $(this).find('span').html('-')
            }
        })
        
        //
        // Abrindo o form de inscrição a partir da agenda de oficinas
        //
        $('.programacao').on('click touch', '.contentsTab .oficinas .cta', function() {
            form.find('.secondStep input[type="checkbox"]#'+$(this).data('oficina')).trigger('click');
        })
        
        
        //
        // POST dos dados
        //
        form.on('click touch', '.btnSubmit', function(e) {
            e.preventDefault();
            var self = $(this);
            
            // Verifica os campos requiridos
            form.removeClass('was-validated');
            form.find('input[required]').each(function(){
              if($(this).attr('type') != 'checkbox' && $(this).val() == ''){
                form.addClass('was-validated');
              }
              if($(this).attr('type') == 'checkbox' && $(this).is(":checked") == false){
                form.addClass('was-validated');
              }
              if($('input').hasClass('is-invalid')){
                 form.addClass('was-validated'); 
              }
            })
            
            // Se os campos tiverem preenchidos envia o formulário
            if(!form.hasClass('was-validated')){
                var NomeCompleto = $("#nome").val();
                var Nacionalidade = $("#brasileiro").is(':checked') ? '&Nacionalidade=brasileiro' : '&Nacionalidade=estrangeiro';
                var Documento = $("#brasileiro").is(':checked') ? '&CPF=' + $("#cpf").val() : '&Passaporte=' + $("#passaporte").val();
                var Email = $("#email").val();
                var CNPJ = $("#cnpj").val();
                var NomeEmpresa = $("#empresa").val();
                var Nascimento = $("#nascimento").val();
                var Telefone = $("#telefone").val();
                var CEP = $("#cep").val();
                var Endereco = $("#endereco").val();
                var Numero = $("#numero").val();
                var Complemento = $("#complemento").val();
                var Oficinas = [];
                form.find('.secondStep input[type="checkbox"]:checked').each(function() {
                     Oficinas.push($(this).val());
                })
                var dataString = 'NomeCompleto='+ NomeCompleto + Nacionalidade + Documento + '&Email='+ Email + '&CNPJ='+ CNPJ+ '&NomeEmpresa='+ NomeEmpresa + '&Nascimento=' + Nascimento + '&Telefone=' + Telefone + '&Endereco=' + Endereco + '&Numero=' + Numero + '&Complemento=' + Complemento + '&CEP=' + CEP + '&Oficinas=' + Oficinas.join('|');
                // console.log(dataString);
                $(this).prop( "disabled", true ).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Aguarde...');
                form.find('.btnBack, input').prop( "disabled", true );
            
                $.ajax({
                    type: "POST",
                    url: "/process/cadastro",
                    data: dataString,
                    cache: false,
                    success: function(result) {
                        
                        if(result.error)
                        {
                            self.parent().find('.alert-danger').show('slow').html(result.error);
                        } 
                        else
                        {
                            //Mostra mensagem de obriagdo
                            firstStep.hide("slow");
                            secondStep.hide("slow");
                            thirdStep.hide("slow");
                            finishStep.show("slow");
                            
                            //limpa os campos e informa cadastro relizado com sucesso
                            $('.form-control').val('');
                            $('.form-check-input').prop('checked', false);
                        }
                    },
                    error: function(error) {
                        alert("Ocorreu um erro ao enviar os dados.");
                        // console.log(error);
                    }
                }).done( function(data){
                    form.find('.btnSubmit').prop( "disabled", false ).html('Finalizar Inscrição');
                    form.find('.btnBack, input').prop( "disabled", false );
                    
                });
            }
                
            
        })
        
    },

   

    
    modalTransmissao: function(){
        var item = $('.itemPalestra'),
        modal = $('#modalTransmissao');
       
        
        item.on('click touch', 'a', function(){
                
            var vid     = $(this).closest('.itemPalestra').data('vid'),
            viden       = $(this).closest('.itemPalestra').data('viden'),
            nid         = $(this).closest('.itemPalestra').data('nid'),
            title       = $(this).closest('.itemPalestra').find('.inf h2').html(),
            urlPlayer   = 'https://player.vimeo.com/video/'+vid+'?title=0&byline=0&portrait=0',
            urlChat     = 'https://vimeo.com/live-chat/'+vid+'/';
            // urlChat     = 'https://vimeo.com/live-chat/'+vid+'/interaction/';
            modal.find('.iframePlayer').attr('src', urlPlayer);
            // modal.find('.iframeChat').attr('src', urlChat);
            modal.find('.modal-title').html(title);
            
            modal.data('vid', vid);
            modal.data('viden', viden);
        
            // Refresh para o script de captura de eventos do Vimeo
            window.__vimeoRefresh();
            
            //Tracking Palestra
            
            $.get( '/tracking/video?nid='+nid);
            
        });
        
        modal.on('click touch', '.language', function(){
            
            if($(this).hasClass('pt')){
                urlPlayer   = 'https://player.vimeo.com/video/'+modal.data('viden')+'?title=0&byline=0&portrait=0';
                modal.find('.iframePlayer').attr('src', urlPlayer);
                $(this).removeClass('pt').addClass('en').html('Veja a transmissão em português');
            } else {
                urlPlayer   = 'https://player.vimeo.com/video/'+modal.data('vid')+'?title=0&byline=0&portrait=0';
                modal.find('.iframePlayer').attr('src', urlPlayer);
                $(this).removeClass('en').addClass('pt').html('View broadcast in english');
            }
        
            // Refresh para o script de captura de eventos do Vimeo
            window.__vimeoRefresh();
            
        });
        
        modal.on('hidden.bs.modal', function () {
            modal.find('.iframePlayer').attr('src', '');
            modal.find('.language').removeClass('en').addClass('pt').html('View broadcast in english');
            
        });
        
    },
    modalEdicoesAnteriores: function(){
        modal = $('#modalEdicoesAnteriores');
        var videoItem = $('.movie-item');

        modal.on('hide.bs.modal', function () {
           videoItem.css('display', 'none');
           videoItem.slice(0, 3).show();
        });
        var dispachP = [];
        dispachP = $('.playDispach');
        var viewP =$('#playView');
        dispachP.on("click", function(e){
            e.preventDefault();
            $(viewP).attr("src", $(this).attr('data-movie')).fadeIn();
        });
    },
    modalQuemPassou: function(){
        modal = $('#modalQuemPassou');
        var videoItem = $('.movie-item');

        modal.on('hide.bs.modal', function () {
            $(viewPlay).attr("src", " ");
        });

        var dispachBtn = [];
        dispachBtn = $('.setVideo');
        var viewPlay = $('#getVideo');
        dispachBtn.on("click", function(e){
            e.preventDefault();
            $(viewPlay).attr("src", $(this).attr('data-video')).fadeIn();
            modal.find('.modal-body .title h3').html($(this).attr('data-title'));
            modal.find('.modal-body .description p').html($(this).attr('data-description'));
        });
    },
    loadmoreItens: function(){
        $(".movie-item").slice(0, 3).show();
    
        $("#loadMore").on("click", function(e){
            e.preventDefault();
            $(".movie-item:hidden").slice(0, 3).slideDown();
                    if($(".movie-item:hidden").length == 0) {
                    $("#loadMore").text("Itens Carregados").addClass("noContent");
            }
            e.preventDefault();
        });
    
    },

    minhaConta: function(){
        
        var cpf = $('#edit-field-cpf-0-value'),
        cnpj    = $('#edit-field-cnpj-0-value'),
        form    = $('form.user-form');
        
        // form.find('input#edit-field-nome-0-value').attr('disabled','disabled');
        // form.find('input#edit-field-cpf-0-value').attr('disabled','disabled');
        // form.find('input#edit-field-passaporte-0-value').attr('disabled','disabled');
        // form.find('input#edit-mail').attr('disabled','disabled');
        //
        // Mascaras
        //
        cpf.mask("999.999.999-99");
        cnpj.mask("99.999.999/9999-99");
        
        //
        // Nacionalidade e Documentação
        //
        form.addClass('firstChange');
        
        form.on('change', 'input[name="field_nacionalidade"]', function(){
            form.removeClass('firstChange');
            if($(this).prop('id') == 'edit-field-nacionalidade-brasileiro'){
                form.find('#edit-field-cpf-wrapper input').attr('required', '').closest('.form-wrapper').show('slow');
                form.find('#edit-field-passaporte-wrapper input').removeAttr('required').closest('.form-wrapper').hide('slow');
            } else {
                form.find('#edit-field-passaporte-wrapper input').attr('required', '').closest('.form-wrapper').show('slow');
                form.find('#edit-field-cpf-wrapper input').removeAttr('required').closest('.form-wrapper').hide('slow');
            }
        });
        
        if( form.hasClass('firstChange') && form.find('input#edit-field-nacionalidade-brasileiro').is(':checked')){
            form.find('#edit-field-cpf-wrapper').attr('required', 'required').show('slow');
        } else if(form.hasClass('firstChange') && form.find('input#edit-field-nacionalidade-estrangeiro').is(':checked')) {
            form.find('#edit-field-passaporte-wrapper').attr('required', 'required').show('slow');
        }
        
    
        
        
        //
        // Validações dos campos
        //
        form.on('click touch', 'input#edit-submit', function(e){
            form.find('.errorField').remove();
            if($('input#edit-field-nacionalidade-brasileiro').is(':checked') && Util.testaCPF(cpf.val()) == false){
                $('html, body').animate({
                        scrollTop: form.offset().top
                }, 200);
                $('<div class="errorField text-danger">Insira um CPF válido.</div>').insertAfter(cpf);
                cpf.focus();
                e.preventDefault();         
            } 
            if (cnpj.val().length > 0 && Util.testaCNPJ(cnpj.val()) == false){
                $('html, body').animate({
                        scrollTop: form.offset().top
                }, 200);
                $('<div class="errorField text-danger">Insira um CNPJ válido.</div>').insertAfter(cnpj);
                cnpj.focus();
                e.preventDefault();
            }
          
        }); 
        
        //
        // Mensagem de sucesso do form
        //
        var url = new URL(window.location.href);
        if(url.searchParams.get("sucesso") == 'true'){
            // console.log('sucesso')
            $('.helper .alert').removeClass('d-none');
            $('#user-form').remove();
        }
        
    },



    oficinas: function(){
        
        var form = $('.formOficinas');
        
        //
        // Mascaras
        //
        form.find('#nascimento').mask("99/99/9999");
        form.find('#telefone').mask("(99) 99999999?9");
        form.find('#cep').mask("99999-999");
        
        
        //
        // Tabs de detalhes das oficinas
        //
        form.on('click touch', '.detalheOfinica .btn', function() {
            var expand = $(this).parent().hasClass('expand');
            $('.formOficinas .detalheOfinica').removeClass('expand');
            $('.formOficinas .detalheOfinica .btn span').html('+');
            if(expand){
              $(this).parent().removeClass('expand');
              $(this).find('span').html('+')
            } else {
              $(this).parent().addClass('expand');
              $(this).find('span').html('-')
            }
        })  
        
        //
        // Mensagem de sucesso do form
        //
        if(window.location.hash == '#sucesso'){
            form.find('.alert').removeClass('d-none');
        }
        
        
        //
        // Validação do CEP e Autocomplete do endereço
        //
        form.find('#cep').focusout(function(){
            // console.log('out')
            var self = $(this),
            cep = $(this).val().replace(/\_|\-|\//g,''),
            urlAPI = '//viacep.com.br/ws/'+cep+'/json/';
            
            if(cep.length == 8){
                $.get(urlAPI, function( data ) {
                    if(data.erro == true){
                        form.addClass('was-validated');
                        self.addClass('is-invalid');
                    } else {
                        self.removeClass('is-invalid');
                        form.find('#endereco').val(data.logradouro + ', ' + data.bairro + ', ' + data.localidade + ' - ' + data.uf);
                    }
                });
            } else {
                $(this).removeClass('is-invalid');
            }
        });
    },
    testaCPF: function(strCPF){
        var Soma = 0,
        Resto,
        strCPF = strCPF.replace(/\.|\-|\//g,'');
        if (strCPF == "00000000000" || strCPF == "11111111111" || strCPF == "22222222222" || strCPF == "33333333333" || strCPF == "44444444444" || strCPF == "55555555555" || strCPF == "66666666666" || strCPF == "77777777777" || strCPF == "88888888888" || strCPF == "99999999999") return false;
           
        for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
        Resto = (Soma * 10) % 11;
         
          if ((Resto == 10) || (Resto == 11))  Resto = 0;
          if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;
         
        Soma = 0;
          for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
          Resto = (Soma * 10) % 11;
         
          if ((Resto == 10) || (Resto == 11))  Resto = 0;
          if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
          return true; 
    },
    testaCNPJ: function(strCNPJ){
        cnpj = strCNPJ.replace(/[^\d]+/g,'');
 
        if(cnpj == '') return false;
         
        if (cnpj.length != 14) return false;
     
        if (cnpj == "00000000000000" || cnpj == "11111111111111" || cnpj == "22222222222222" || cnpj == "33333333333333" || cnpj == "44444444444444" || cnpj == "55555555555555" || cnpj == "66666666666666" || cnpj == "77777777777777" || cnpj == "88888888888888" || cnpj == "99999999999999") return false;
             
     
        tamanho = cnpj.length - 2
        numeros = cnpj.substring(0,tamanho);
        digitos = cnpj.substring(tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
          soma += numeros.charAt(tamanho - i) * pos--;
          if (pos < 2)
                pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(0))
            return false;
             
        tamanho = tamanho + 1;
        numeros = cnpj.substring(0,tamanho);
        soma = 0;
        pos = tamanho - 7;
        for (i = tamanho; i >= 1; i--) {
          soma += numeros.charAt(tamanho - i) * pos--;
          if (pos < 2)
                pos = 9;
        }
        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado != digitos.charAt(1)) return false;
        return true;
    },
    testaCEP: function(strCEP){
        var cep = strCEP.replace(/\.|\-|\//g,''),
        urlAPI = '//viacep.com.br/ws/'+cep+'/json/';
        $.get(urlAPI, function( data ) {
              if(data.erro == true){
                  var obj = false;
              } else {
                  var obj = data;
              }
              
        });
        return
    },

    // 
    // HOTSITE SCRIPTS
    // 



    hotsiteCarouselSchedule: function(){
        let paginate = '.swiper-pagination';
        let dayH = [];
        let dateH = [];                      
        $('.event-itens').each(function(){
            dayH.push( $(this).find('span').html() );
            dateH.push( $(this).find('p').html() );
        });           
        setTimeout(() => {
            const swiper = new Swiper('.swiper-speaker', {
                // If we need pagination
                pagination:
                {
                    el: paginate,
                    clickable: true,
                    renderBullet: function (index, className)
                    {
                        // return `<li class="${className} date-pagination"> <div class="date-content"> Dia 01 <span> 17/11/2021</span> </div></li> `;
                        return ` <li class="${className} date-pagination"> <div class="date-content"> ${dayH[index]}  <span> ${dateH[index]}</span> </div></li>`;
    
                    },
                },
                // Navigation arrows
                navigation: {
                  nextEl: '.swiper-button-next',
                  prevEl: '.swiper-button-prev',
                },
                // Optional parameters
                direction: 'horizontal',
                loop: false,
                speed: 1000,
                paginationClickable: true,
                spaceBetween: 130,
                touchRatio: 0,
                allowTouchMove: false,
                autoHeight: true,
                
            });
                
        }, 100);
        $(function() {
            var owl = $('.owl-carousel-responsive'),
            
            owlOptions = {
                loop:false,
                autoHeightClass: 'owl-height col-12',
                autoHeight:true,
                // margin: -30,
                nav:true,
                // dots: false,            
                // smartSpeed: 700,
                // items: 1,
                responsive : {
                    
                    0 : {
                        items: 1,
                        margin: -30
                    },
                    
                    400 : {
                        items: 1,
                        margin: -30
                    },
                    
                    401 : {
                        items: 1,
                        margin: -120
                    }
                }
               
            };
            if ( $(window).width() < 768 ) {
                var owlActive = owl.owlCarousel(owlOptions);
            } else {
                owl.addClass('off');
            }
            $(window).resize(function() {
                if ( $(window).width() < 768 ) {
                    if ( $('.owl-carousel-responsive').hasClass('off') ) {
                        var owlActive = owl.owlCarousel(owlOptions);
                        owl.removeClass('off');
                    }
                } else {
                    if ( !$('.owl-carousel-responsive').hasClass('off') ) {
                        owl.addClass('off').trigger('destroy.owl.carousel');
                        owl.find('.owl-stage-outer').children(':eq(0)').unwrap();
                    }
                }
            });
        });

    },
    cutReadMore: function(){
        var contentRead = $('.read-more-container');
        $("#hotLoadMore").click(function(e){
            if(!$(contentRead).hasClass('read-more-container--hidden-content')){
                $(contentRead).addClass('read-more-container--hidden-content');
                $("#hotLoadMore").html("<span class='show-text-content'>LEIA MAIS</span>");
            } else{
                $(contentRead).removeClass('read-more-container--hidden-content');
                $("#hotLoadMore").html("<span class='show-text-content show-text-content--complete'>LER MENOS</span>").addClass("noHotContent");
            }
            e.preventDefault();
        });           
    },

    hotsiteCrouselWorkshop: function(){
        $('.owl-carousel-workshop').owlCarousel({
            margin: 20,
            nav:true,
            responsive:{
                0:{
                    items:1,
                    margin:15
                },
                768:{
                    items:2
                },
                1024:{
                    items:3
                },
                1025:{
                    items:3
                }
            }
        });
    },

    modalPalestrante: function(){        
        var speakerInfo = [];
        speakerInfo = $('.speakerInfo');        
        speakerInfo.on("click", function(e){
            e.preventDefault();
            modal = $('#modalPalestrante'); 
            
            var miniBio = $(this).parent().find('.mini-bio').html();
            var descPalestra = $(this).parent().find('.palestra-desc').html();
            var linkedinLink = $(this).attr('data-linkedin');
            var avatarPalestrante = $(this).attr('data-avatar');
            modal.find('#speakerName span').html($(this).attr('data-palestrante-nome'));
            modal.find('#modalTitle h2').html($(this).attr('data-title'));
            modal.find('#socialLinkedin a.cta-body').attr("href", linkedinLink);
            modal.find('#palestranteAvatar img').attr("src", avatarPalestrante);
            modal.find('#miniBio').html(miniBio);    
            modal.find('#descPalestra').html(descPalestra);
            
        });
    },

    modalExperienciaImersiva: function(){
           
        modal = $('#modalExperienciaImersiva'); 

        modal.on('hide.bs.modal', function () {
            $('.formXpImersiva')[0].reset();
            $('.form-check-input').prop('checked', false);
            $('.formXpImersiva .finishStep').hide();
            $('.formXpImersiva .firstStep').show();                    
        });

        var form    = $('.formXpImersiva'),
        firstStep   = form.find('.firstStep'),
        finishStep  = form.find('.finishStep');
        finishStep.hide();

        form.on('change', 'input[name="nacionalidade"]', function(){
            form.find('input[name="nacionalidade"]').removeAttr('required');
            if($(this).prop('id') == 'brasileiro'){
                form.find('#cpf').attr('required', '').parent().show('slow');
                form.find('#passaporte').removeAttr('required').parent().hide('slow');
            } else {
                form.find('#passaporte').attr('required', '').parent().show('slow');
                form.find('#cpf').removeAttr('required').parent().hide('slow');
            }
        });



        form.on('click touch', '.btnNext', function(){
            
            var self = $(this);
            
            if($(this).hasClass('first')) // Primeiro Step
            {
                form.removeClass('was-validated');
                form.find('.firstStep input[required], .firstStep select[required]').each(function(){

                  if($(this).attr('type') == 'radio'){
              
                    // console.log($(this).is(":checked"))
                  }


                  if($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio' && $(this).val() == ''){ //$(this).attr('type') != 'checkbox'
                   
             
                        form.addClass('was-validated');
                  
                  }

                  if($(this).attr('type') == 'checkbox' &&  $(this).is(":checked") == false){
                   
                    form.addClass('was-validated');
                  }

                  if($(this).attr('type') == 'radio' &&  $(this).is(":checked") == false){
                   
                    form.addClass('was-validated');
                  }
                })
                
                // Testa CPF
                if(form.find('#brasileiro').is(':checked') && Util.testaCPF(form.find('#cpf').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cpf').addClass('is-invalid');
                } else {
                    form.find('#cpf').removeClass('is-invalid');
                }
                
                // Testa CNPJ
                if(form.find('#cnpj').val().length > 0 && Util.testaCNPJ(form.find('#cnpj').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cnpj').addClass('is-invalid');
                } else {
                    form.find('#cnpj').removeClass('is-invalid');
                }

               
                
                if(!form.hasClass('was-validated')){
                    var NomeCompleto = form.find("#nome").val();
                    var Nacionalidade = form.find("#brasileiro").is(':checked') ? '&Nacionalidade=brasileiro' : '&Nacionalidade=estrangeiro';
                    var Documento = form.find("#brasileiro").is(':checked') ? '&CPF=' + form.find("#cpf").val() : '&Passaporte=' + form.find("#passaporte").val();
                    var Email = form.find("#email").val();
                    var CNPJ = form.find("#cnpj").val();
                    var NomeEmpresa = form.find("#empresa").val();
                    var ExperienciaImersiva = [];

                form.find(".check-data :checkbox").each(function () {
                     var ischecked = $(this).is(":checked");
                     if (ischecked) {
                        ExperienciaImersiva.push($(this).val()) ;
                        //  ExperienciaImersiva += $(this).val() + "|";
                        //  ExperienciaImersiva += $(this).val() ; 
                        // console.log('exp', ExperienciaImersiva);                       
                     }
                 });
                 
                var dataString = 'NomeCompleto='+ NomeCompleto + Documento + Nacionalidade + '&Email='+ Email + '&CNPJ='+ CNPJ+ '&NomeEmpresa='+ NomeEmpresa + '&ExperienciaImersiva=' + ExperienciaImersiva;
                   
                    $(this).prop( "disabled", true ).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Aguarde...');
                    form.find('.btnBack, input').prop( "disabled", true );                    
                  
                    $.ajax({
                        type: "POST",
                        url: "/process/experiencia_imersiva",
                        data: dataString,
                        cache: false,
                        success: function(result) {
                            
                            if(result.error)
                            {
                                self.parent().find('.alert-danger').show('slow').html(result.error);
                            } 
                            else
                            {
                                //Mostra mensagem de obriagdo
                                firstStep.hide("slow");
                                finishStep.show("slow");
                                
                                //limpa os campos e informa cadastro relizado com sucesso
                                $('.form-control').val('');
                                $('.form-check-input').prop('checked', false);
                                form.find('.alert.alert-danger').remove();
                            }
                        },
                        error: function(error) {
                            // alert("Ocorreu um erro ao enviar os dados.");
                            // console.log(error);

                            //Mostra mensagem de obriagdo
                            firstStep.hide("slow");
                            finishStep.show("slow");
                            
                            //limpa os campos e informa cadastro relizado com sucesso
                            $('.form-control').val('');
                            $('.form-check-input').prop('checked', false);
                            form.find('.alert.alert-danger').remove();

                        }
                    }).done( function(data){
                        form.find('.btnNext').prop( "disabled", false ).html('Finalizar Inscrição');
                        form.find('.btnBack, input').prop( "disabled", false );
                        
                    });
                }
                
            }
            else // Segundo Step
            {
                if(!form.hasClass('was-validated')){
                  secondStep.hide("slow");
                  thirdStep.show("slow");
                }
            }
            
        });
        form.on('click touch', '.btnNext', function(){
            var self = $(this);
            if($(this).hasClass('first')) // Primeiro Step
            {
                form.removeClass('was-validated');
                form.find('.firstStep input[required], .firstStep select[required]').each(function(){
                    if($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio' && $(this).val() == ''){ //$(this).attr('type') != 'checkbox'
                        form.addClass('was-validated');
                    }

                    if($(this).attr('type') == 'checkbox' &&  $(this).is(":checked") == false){
                    // console.log($(this));
                    form.addClass('was-validated');
                    }

                    if($(this).attr('type') == 'radio' &&  $(this).is(":checked") == false){
                    // console.log($(this));
                    form.addClass('was-validated');
                    }
                })
                    
                // Testa CPF
                if(form.find('#cpf').val().length > 0 && Util.testaCPF(form.find('#cpf').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cpf').addClass('is-invalid');
                } else {
                    form.find('#cpf').removeClass('is-invalid');
                }
                
                // Testa CNPJ aqui
                if(form.find('#cnpj').val().length > 0 && Util.testaCNPJ(form.find('#cnpj').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cnpj').addClass('is-invalid');
                } else {
                    form.find('#cnpj').removeClass('is-invalid');
                }                         
                
            }            
        });
            
    
            //
            // Mascaras
            //
            form.find('#cpf').mask("999.999.999-99");
            form.find('#cnpj').mask("99.999.999/9999-99");
            form.find('#nascimento').mask("99/99/9999");
            form.find('#telefone').mask("(99) 99999999?9");
            form.find('#cep').mask("99999-999");
    
            //
            // POST dos dados
            //
            form.on('click touch', '.btnSubmit', function(e) {
                e.preventDefault();
                var self = $(this);
                
                // Verifica os campos requiridos
                form.removeClass('was-validated');
                form.find('input[required]').each(function(){
                  if($(this).attr('type') != 'checkbox' && $(this).val() == ''){
                    form.addClass('was-validated');
                  }
                  if($(this).attr('type') == 'checkbox' && $(this).is(":checked") == false){
                    form.addClass('was-validated');
                  }
                  if($('input').hasClass('is-invalid')){
                     form.addClass('was-validated'); 
                  }
                })                
                
            })
            
    },


    // modal oficina
    modalOficina: function(){
        var oficinaInfo = [];
        oficinaInfo = $('.oficinaInfo');

        oficinaInfo.on("click", function(e){
            e.preventDefault(); 
           
            //Preencher a tabela 
           
            // let thisParent = [];
            // thisParent = $(oficinaInfo).parents().find('div.conteudo-oficina-card div.preco-list');
            // console.log('this', thisParent[0]);
            // let listapreco = [];
            // // listapreco = $('.preco-list').find('.preco-item');
            // let precosItem = [];
            // precosItem = $('#precosItem').find('td');
           
            // $.each(listapreco, function(index){
            //     let listafeita = [];
            //     listafeita = listapreco[index];
            //     // $(listafeita);
            //     // console.log('lista preco 1', listafeita);
            //     // $(precosItem).append(` <td> ${listafeita} </td>`)  ; 
            //     // $(precosItem).append(listafeita) ; 
            // });


            modal = $('#modalOficina'); 
            var 
            oficinaTitle = $(this).attr('data-title'),            
            fullPrice = $(this).attr('data-full-price'),
            idOficina = $(this).attr('data-oficina'),            
            diaOficina = $(this).attr('data-oficina-dia'),      
            nomeOficina = $(this).closest('.owl-item').find('.titulo-oficina').text(),
            nregPrice = $(this).attr('data-price-nreg'),
            cirjPrice = $(this).attr('data-price-cirj'),
            regPrice = $(this).attr('data-price-reg'),
            sindPrice = $(this).attr('data-price-sind');

          
           
            if(regPrice.length == 0){
                modal.find('#precosItem #regPrice').hide();
                modal.find('th#regLabel').hide(); 
            }else{
                modal.find('#precosItem #regPrice').show();
                modal.find('th#regLabel').show(); 
            }

            if(cirjPrice.length == 0){
                modal.find('#precosItem #cirjPrice').hide();
                modal.find('th#cirjLabel').hide(); 
            }else{
                modal.find('#precosItem #cirjPrice').show();
                modal.find('th#cirjLabel').show(); 
            }

            if(nregPrice.length == 0){
                modal.find('#precosItem #nregPrice').hide();
                modal.find('th#nregLabel').hide(); 
            }else{
                modal.find('#precosItem #nregPrice').show();
                modal.find('th#nregLabel').show(); 
            }
            if(sindPrice.length == 0){
                modal.find('#precosItem #sindPrice').hide();
                modal.find('th#sindLabel').hide(); 
            }else{
                modal.find('#precosItem #sindPrice').show();
                modal.find('th#sindLabel').show();
            }

            if(cirjPrice.length == 0 && regPrice.length == 0 && sindPrice.length == 0 && nregPrice.length == 0){
                $('.preco-title').hide();
            }else{
                $('.preco-title').show();
            }
           
            modal.find('#oficinaTitle h3').html(oficinaTitle);
            modal.find('#oficinaFullPrice h3').html(fullPrice);
            modal.find('#precosItem #nregPrice').html(nregPrice);
            modal.find('#precosItem #cirjPrice').html(cirjPrice);
            modal.find('#precosItem #regPrice').html(regPrice);
            modal.find('#precosItem #sindPrice').html(sindPrice);
            modal.find('#oficina').attr('value', idOficina);             
            modal.find('#oficina_dia').attr('value', diaOficina);       
            modal.find('#oficina_nome').attr('value', nomeOficina.trim()); 
           

            modal.on('hide.bs.modal', function () {
                // $('.formOficina').val('');
                $('.formOficina')[0].reset();
                // $('.formOficina input#comprovante')[0].reset();
                $('.form-check-input').prop('checked', false);

                $("div.file-name p").html(''); 
                $('.formOficina .finishStep').hide();
                $('.formOficina .firstStep').show();
                $('div.display-associado').hide();
                modal.find('#oficina').attr('value', "");
                
            });
            

        });

        var form    = $('.formOficina'),
        firstStep   = form.find('.firstStep'),
        finishStep  = form.find('.finishStep'),
        labelComporvante = form.find('label.comprovante-label'),
        inputFile = form.find('input#comprovante');
        finishStep.hide();
        // Mostra nome comprovante
        form.on('change', labelComporvante, function(){
           let file = $(inputFile)[0].files[0];            
            if(file){
                $("div.file-name p").html('Nome do Comprovante: ' +  file.name); 
            }
        });


        // Mostra campos para associados
        form.on('change', 'input[name="associado"]', function(){
            // form.removeClass('firstChange');
            if($(this).prop('id') == 'affiliated'){                
                form.find('div.display-associado').show('slow');
                form.find('#noAffiliated').removeAttr('required');
                form.find('#cnpj').attr("required", "true");
                form.find('#comprovante').attr("required", "true");
                
            } else {
                form.find('div.display-associado').hide('slow');
                form.find('#affiliated').removeAttr('required');
                form.find('#cnpj').removeAttr('required');
                form.find('#comprovante').removeAttr('required');
                

            }
        });


        form.on('click touch', '.btnNext', function(){
            var self = $(this);
            if($(this).hasClass('first')) // Primeiro Step
            {
                form.removeClass('was-validated');
                form.find('.firstStep input[required], .firstStep select[required]').each(function(){
                    if($(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio' && $(this).val() == ''){ //$(this).attr('type') != 'checkbox'
                        form.addClass('was-validated');
                  }

                  if($(this).attr('type') == 'checkbox' &&  $(this).is(":checked") == false){
                    // console.log($(this));
                    form.addClass('was-validated');
                  }

                  if($(this).attr('type') == 'radio' &&  $(this).is(":checked") == false){
                    // console.log($(this));
                    form.addClass('was-validated');
                  }
                })


                // Testa Arquivo
                var inputEl = form.find('#comprovante');
                if(form.find('#comprovante').is(':invalid')){
                    $('label.comprovante-label').addClass('is-invalid');
                }
                $(inputEl).on('change', function(){
                    setTimeout(() => {
                       
                       if(form.find('#comprovante').is(':valid')){    
                            $('label.comprovante-label').removeClass('is-invalid');
                            $('label.comprovante-label').addClass('valido');
                        }else{
                            $('label.comprovante-label').removeClass('is-invalid');
                            $('label.comprovante-label').removeClass('valido');
                        
                        }                                              
                    
                    }, 100);
                });


                
                  // Testa CPF
                if(form.find('#cpf').val().length > 0 && Util.testaCPF(form.find('#cpf').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cpf').addClass('is-invalid');
                } else {
                    form.find('#cpf').removeClass('is-invalid');
                }
                
                // Testa CNPJ aqui
                if(form.find('#cnpj').val().length > 0 && Util.testaCNPJ(form.find('#cnpj').val()) == false){
                    form.addClass('was-validated');
                    form.find('#cnpj').addClass('is-invalid');
                } else {
                    form.find('#cnpj').removeClass('is-invalid');
                }
                
                
                if(!form.hasClass('was-validated')){
                    // console.log('ok1')
                    // form.on('click touch', '.btn.btnNext.first', function() {
                        var form_data = new FormData();                 
                        form_data.append('comprovante', form.find('input#comprovante').prop("files")[0])            
                        form_data.append('nome', form.find('input#nome').val())         
                        form_data.append('email', form.find('input#email').val())         
                        form_data.append('telefone', form.find('input#telefone').val())         
                        form_data.append('cpf', form.find('input#cpf').val())             
                        form_data.append('nascimento', form.find('input#nascimento').val())               
                        form_data.append('endereco', form.find('input#endereco').val())               
                        form_data.append('cep', form.find('input#cep').val())                     
                        form_data.append('formacao', form.find('input#formacao').val())           
                        form_data.append('cnpj', form.find('input#cnpj').val())          
                        form_data.append('empresa', form.find('input#empresa').val())          
                        form_data.append('associado', form.find('input[name="associado"]:checked').val())          
                        form_data.append('oficina', form.find('input#oficina').val())          
                        form_data.append('oficina_dia', form.find('input#oficina_dia').val())                          
                        form_data.append('oficina_nome', form.find('input#oficina_nome').val()) 
                    
                        $(this).prop( "disabled", true ).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Aguarde...');
                        form.find('.btnBack, input').prop( "disabled", true );
                        $.ajax({

                            url: "/process/oficinas",
                            dataType: 'script',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: form_data,                         
                            type: 'post',
                            success: function(result) {
                                var result = JSON.parse(result);
                                if(result.error)
                                {
                                    self.parent().find('.alert-danger').show('slow').html(result.error);
                                } 
                                else
                                {
                                    //Mostra mensagem de obriagdo
                                    firstStep.hide("slow");
                                    finishStep.show("slow");
                                    
                                    //limpa os campos e informa cadastro relizado com sucesso
                                    $('.form-control').val('');
                                    $('.form-check-input').prop('checked', false);                                    
                                    form.find('.alert.alert-danger').remove();
                                }
                            },
                            error: function(error) {
                                alert("Ocorreu um erro ao enviar os dados.");
                                // console.log(error);
                            }
                        }).done( function(data){
                            form.find('.btnNext').prop( "disabled", false ).html('Finalizar Inscrição');
                            form.find('.btnBack, input').prop( "disabled", false );                        
                        });                
                    // });  
                
                }
        
                // if(!form.hasClass('was-validated')){
                //     var NomeCompleto = $("#nome").val();
                //     var Associado = $("#affiliated").is(':checked') ? '&Associado=sim' : '&Associado=nao';
                //     var Email = $("#email").val();
                //     var Telefone = $("telefone").val();                    
                //     var CNPJ = $("#cnpj").val();
                //     var NomeEmpresa = $("#empresa").val();                   
                //     var Oficinas = [];
                //     form.find('.secondStep input[type="checkbox"]:checked').each(function() {
                //          Oficinas.push($(this).val());
                //     })
                //     var dataString = 'NomeCompleto='+ NomeCompleto + Associado +'&Email='+ Email + '&Telefone='+ Telefone + '&CNPJ='+ CNPJ+ '&NomeEmpresa='+ NomeEmpresa +  '&Oficinas=' + Oficinas.join('|');
                //     // console.log(dataString);
                //     $(this).prop( "disabled", true ).html('<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Aguarde...');
                //     form.find('.btnBack, input').prop( "disabled", true );
                
                //     $.ajax({
                //         type: "POST",
                //         // url: "/process/ofinas",
                //         data: dataString,
                //         cache: false,
                //         success: function(result) {
                            
                //             if(result.error)
                //             {
                //                 self.parent().find('.alert-danger').show('slow').html(result.error);
                //             } 
                //             else
                //             {
                //                 //Mostra mensagem de obriagdo
                //                 firstStep.hide("slow");
                //                 finishStep.show("slow");
                                
                //                 //limpa os campos e informa cadastro relizado com sucesso
                //                 $('.form-control').val('');
                //                 $('.form-check-input').prop('checked', false);
                //             }
                //         },
                //         error: function(error) {
                //             alert("Ocorreu um erro ao enviar os dados.");
                //             console.log(error);
                //         }
                //     }).done( function(data){
                //         form.find('.btnNext').prop( "disabled", false ).html('Finalizar Inscrição');
                //         form.find('.btnBack, input').prop( "disabled", false );
                        
                //     });
                // }
                
            }            
        });
        

        //
        // Mascaras
        //
        form.find('#cpf').mask("999.999.999-99");
        form.find('#cnpj').mask("99.999.999/9999-99");
        form.find('#nascimento').mask("99/99/9999");
        form.find('#telefone').mask("(99) 99999999?9");
        form.find('#cep').mask("99999-999");

        //
        // POST dos dados
        //
        form.on('click touch', '.btnSubmit', function(e) {
            e.preventDefault();
            var self = $(this);
            
            // Verifica os campos requiridos
            form.removeClass('was-validated');
            form.find('input[required]').each(function(){
              if($(this).attr('type') != 'checkbox' && $(this).val() == ''){
                form.addClass('was-validated');
              }
              if($(this).attr('type') == 'checkbox' && $(this).is(":checked") == false){
                form.addClass('was-validated');
              }
              if($('input').hasClass('is-invalid')){
                 form.addClass('was-validated'); 
              }
            })                
            
        })
        
    },

    lgpdView: function(){
        $('.collapse').collapse();
        console.log('lgpd')
    },

    videoTransmissao: function(){
        let tramissaoDiv = $('#tramissaoDiv');
        let toggleLannguage = [];
        toggleLannguage = $('.toggle-language');

        $(toggleLannguage).each(function () {
            $($(this).on('click', function(){
                $(toggleLannguage).removeClass("toggle-language__selected");                
                let urlVevo = $(this).find('a').data('video');
                let iframe = $(tramissaoDiv).find('.iframePlayer');
                $(iframe).attr('src', urlVevo);
                $(this).addClass("toggle-language__selected");
            })
            
        );})
    },
    urlShare: function(){
        $("#urlShare").click(function(){
            // $(this).select();
        
            $(this).execCommand('copy');
          })
          console.log('copiou');
    },
}