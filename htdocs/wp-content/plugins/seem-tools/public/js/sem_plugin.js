jQuery(document).ready(function($){
    $('.sem_modal_url_multiple').select2({width:'100%'});
    
    $( ".sem_table" ).change(function() {
        var modal_url = $('.sem_modal_url_multiple').val();
        var id_keyword = $(this).attr('id');
        
        var array_url = modal_url.split(",");
        
        $('.url1').val(modal_url);
        
        console.log(array_url);
        
        
    });
});

function last_caractere(chaine){
    return chaine[chaine.length-1];
}