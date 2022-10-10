jQuery(function($){
    setInterval(function(){
        var fields = ['field_imagem[0][title]'];
        $.each( fields, function( key, value ) {
            var parent = $('input[name="'+value+'"]').parent();
            parent.find('label').text('Nome do fotógrafo');
            parent.find('.description').text('Campo para destacar os crédito ao fotógrafo.');
        });
    }, 100);
    
});