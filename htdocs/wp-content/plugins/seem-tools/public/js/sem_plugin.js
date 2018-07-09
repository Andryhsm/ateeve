jQuery(document).ready(function($){
    $('.sem_modal_url_multiple').select2({width:'100%'});
    
    $( ".sem_table" ).change(function() {
        var modal_url = $('.sem_modal_url_multiple').val();
        var id_keyword = $(this).attr('id');
        var url_temporaire;
        
        /*$.each(modal_url, function(index, value){
            console.log(index+ ' - ' +value);
            url_temporaire += value;
            console.log(url_temporaire);
        });*/
        
        for(var i=0; i<modal_url.length; i++)
        {
            url_temporaire += modal_url[i];
            console.log(url_temporaire);
        }
        
        $('.url1').val(modal_url);
        
    });
});

function last_caractere(chaine){
    return chaine[chaine.length-1];
}