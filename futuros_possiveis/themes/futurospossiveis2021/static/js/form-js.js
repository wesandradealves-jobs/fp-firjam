(function () {
  "use strict";

  const form = $(".formXpImersiva"),
    firstStep = form.find(".firstStep"),
    secondStep = form.find(".secondStep"),
    thirdStep = form.find(".thirdStep"),
    finishStep = form.find(".finishStep");

  $('select[name="nacionalidade"]').on("change", function () {
    let $thisSelected = $(this).find(":selected").val();
    if ($(this).prop("id") == "brasileiro") {
      form.find("#cpf").attr("required", "").parent().show("slow");
      form.find("#passaporte").removeAttr("required").parent().hide("slow");
    } else {
      form.find("#passaporte").attr("required", "").parent().show("slow");
      form.find("#cpf").removeAttr("required").parent().hide("slow");
    }
  });

  form.find('#cpf').mask("999.999.999-99");
  form.find('#cnpj').mask("99.999.999/9999-99");

  form.on("change", 'select[name="nacionalidade"]', function () {
    let $thisSelected = $(this).find(":selected").val();

    form.find('select[name="nacionalidade"]').removeAttr("required");

    if ($thisSelected == "brasileiro") {
      form.find("#cpf").attr("required", "").parent().show("slow");
      form.find("#passaporte").removeAttr("required").parent().hide();
    } else if ($thisSelected == "estrangeiro") {
      form.find("#passaporte").attr("required", "").parent().show("slow");
      form.find("#cpf").removeAttr("required").parent().hide();
    } else {
      form.find('select[name="nacionalidade"]').attr("required", "");
      form.find("#passaporte").removeAttr("required").parent().hide();
      form.find("#cpf").removeAttr("required").parent().hide();
    }

    // form.find('select[name="nacionalidade"]').removeAttr("required");
    // if ($(this).prop("id") == "brasileiro") {
    //   form.find("#cpf").attr("required", "").parent().show("slow");
    //   form.find("#passaporte").removeAttr("required").parent().hide("slow");
    // } else {
    //   form.find("#passaporte").attr("required", "").parent().show("slow");
    //   form.find("#cpf").removeAttr("required").parent().hide("slow");
    // }
  });

  form.on("click touch", ".btnSubmit", function (e) {
    e.preventDefault();
    var self = $(this);

    form.removeClass("was-validated");

    form.find(".firstStep input[required]").each(function () {
      if ($(this).attr("type") != "checkbox" && $(this).val() == "") {
        form.addClass("was-validated");
      }
      if (
        $(this).attr("type") == "checkbox" &&
        $(this).is(":checked") == false
      ) {
        form.addClass("was-validated");
      }
    });

    form.find(".firstStep select[required]").each(function () {
      if ($(this).attr("type") != "checkbox" && $(this).val() == "") {
        form.addClass("was-validated");
      }
      if (
        $(this).attr("type") == "checkbox" &&
        $(this).is(":checked") == false
      ) {
        form.addClass("was-validated");
      }
    });


    // Testa CNPJ
    if(form.find('#cnpj').val().length > 0 && Util.testaCNPJ(form.find('#cnpj').val()) == false){
      form.addClass('was-validated');
      form.find('#cnpj').addClass('is-invalid');
    } else {
        form.find('#cnpj').removeClass('is-invalid');
    }

    // Testa CPF
    if(form.find('#cpf').val().length > 0 && Util.testaCPF(form.find('#cpf').val()) == false){
      form.addClass('was-validated');
      form.find('#cpf').addClass('is-invalid');
    } else {
        form.find('#cpf').removeClass('is-invalid');
    }
    // if(form.find('#brasileiro').is(':checked') && Util.testaCPF(form.find('#cpf').val()) == false){
    // if(form.find('select[name="nacionalidade"]').find(":selected").val() == "brasileiro" && Util.testaCPF(form.find('#cpf').val()) == false){
    //     form.addClass('was-validated');
    //     form.find('#cpf').addClass('is-invalid');
    // } else {
    //     form.find('#cpf').removeClass('is-invalid');
    // }

    // Se os campos tiverem preenchidos envia o formulário
    if (!form.hasClass("was-validated")) {
      var nomeNome = $("#nome").val();
      var sobreNome = $("#sobrenome").val();
      var NomeCompleto = nomeNome + " " + sobreNome;
      //   var Nacionalidade = $("#brasileiro").is(":checked")
      // ? "&Nacionalidade=brasileiro"
      // : "&Nacionalidade=estrangeiro";
      // var Documento = $("#brasileiro").is(":checked")
      // ? "&CPF=" + $("#cpf").val()
      // : "&Passaporte=" + $("#passaporte").val();
      var CPF = $("#cpf")
        .val()
        .replace(/[^\d]+/g, "");
      var cargo = $("#cargo").val();
      var Email = $("#email").val();
      var CNPJ = $("#cnpj")
      .val()
      .replace(/[^\d]+/g, "");
      var NomeEmpresa = $("#empresa").val();
      var ComoSoube = $("#como_ficou_sabendo").val();
      var senha = $("#senha").val();
      // var Nascimento = $("#nascimento").val();
      // var Telefone = $("#telefone").val();
      // var CEP = $("#cep").val();
      // var Endereco = $("#endereco").val();
      // var Numero = $("#numero").val();
      // var Complemento = $("#complemento").val();
      // var Oficinas = [];
      // form.find('.secondStep input[type="checkbox"]:checked').each(function() {
      //      Oficinas.push($(this).val());
      // })
      // var dataString = 'NomeCompleto='+ NomeCompleto + Nacionalidade + Documento + '&Email='+ Email + '&CNPJ='+ CNPJ+ '&NomeEmpresa='+ NomeEmpresa + '&Nascimento=' + Nascimento + '&Telefone=' + Telefone + '&Endereco=' + Endereco + '&Numero=' + Numero + '&Complemento=' + Complemento + '&CEP=' + CEP + '&Oficinas=' + Oficinas.join('|');
      var dataString =
        "NomeCompleto=" +
        NomeCompleto +
        "&Email=" +
        Email +
        "&CPF=" +
        CPF +
        "&CNPJ=" +
        CNPJ +
        "&NomeEmpresa=" +
        NomeEmpresa +
        "&Cargo=" +
        cargo +
        "&FicouSabendo=" +
        ComoSoube +
        "&Password=" +
        CPF;
      // console.log(dataString);
      $(this)
        .prop("disabled", true)
        .html(
          '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Aguarde...'
        );
      form.find(".btnBack, input").prop("disabled", true);

      $.ajax({
        type: "POST",
        url: "/process/cadastro",
        data: dataString,
        cache: false,
        success: function (result) {
          if (result.error) {
            self.parent().find(".alert-danger").show("slow").html(result.error);
          } else {
            //Mostra mensagem de obriagdo
            firstStep.hide("slow");
            $(".webform-cadastro").addClass("send-message");
            // secondStep.hide("slow");
            // thirdStep.hide("slow");
            finishStep.show("slow");

            //limpa os campos e informa cadastro relizado com sucesso
            $(".form-control").val("");
            $(".form-check-input").prop("checked", false);
          }
        },
        error: function (error) {
          alert("Ocorreu um erro ao enviar os dados.");
          console.log(error);
        },
      }).done(function (data) {
        form
          .find(".btnSubmit")
          .prop("disabled", false)
          .html("Finalizar Inscrição");
        form.find(".btnBack, input").prop("disabled", false);
      });
    }
  });

  var Util = {
    testaCPF: function (strCPF) {
      var Soma = 0,
        Resto,
        strCPF = strCPF.replace(/\.|\-|\//g, "");
      if (
        strCPF == "00000000000" ||
        strCPF == "11111111111" ||
        strCPF == "22222222222" ||
        strCPF == "33333333333" ||
        strCPF == "44444444444" ||
        strCPF == "55555555555" ||
        strCPF == "66666666666" ||
        strCPF == "77777777777" ||
        strCPF == "88888888888" ||
        strCPF == "99999999999"
      )
        return false;

      for (var i = 1; i <= 9; i++)
        Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (11 - i);
      Resto = (Soma * 10) % 11;

      if (Resto == 10 || Resto == 11) Resto = 0;
      if (Resto != parseInt(strCPF.substring(9, 10))) return false;

      Soma = 0;
      for (i = 1; i <= 10; i++)
        Soma = Soma + parseInt(strCPF.substring(i - 1, i)) * (12 - i);
      Resto = (Soma * 10) % 11;

      if (Resto == 10 || Resto == 11) Resto = 0;
      if (Resto != parseInt(strCPF.substring(10, 11))) return false;
      return true;
    },

    testaCNPJ: function (strCNPJ) {
      var cnpj = strCNPJ.replace(/[^\d]+/g, "");

      if (cnpj == "") return false;

      if (cnpj.length != 14) return false;

      if (
        cnpj == "00000000000000" ||
        cnpj == "11111111111111" ||
        cnpj == "22222222222222" ||
        cnpj == "33333333333333" ||
        cnpj == "44444444444444" ||
        cnpj == "55555555555555" ||
        cnpj == "66666666666666" ||
        cnpj == "77777777777777" ||
        cnpj == "88888888888888" ||
        cnpj == "99999999999999"
      )
        return false;

      var tamanho = cnpj.length - 2;
      var numeros = cnpj.substring(0, tamanho);
      var digitos = cnpj.substring(tamanho);
      var soma = 0;
      var pos = tamanho - 7;
      for (var i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
      }
      var resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
      if (resultado != digitos.charAt(0)) return false;

      tamanho = tamanho + 1;
      numeros = cnpj.substring(0, tamanho);
      soma = 0;
      pos = tamanho - 7;
      for (i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
      }
      resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
      if (resultado != digitos.charAt(1)) return false;
      return true;
    },
    testaCEP: function (strCEP) {
      var cep = strCEP.replace(/\.|\-|\//g, ""),
        urlAPI = "//viacep.com.br/ws/" + cep + "/json/";
      $.get(urlAPI, function (data) {
        if (data.erro == true) {
          var obj = false;
        } else {
          var obj = data;
        }
      });
      return;
    },
  };
  console.log(Util);
})();
