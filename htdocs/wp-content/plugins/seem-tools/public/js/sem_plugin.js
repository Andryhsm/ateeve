String.prototype.replaceAll = function(search, replacement){
	var target = this;
	return target.split(search).join(replacement);
}
var $ = jQuery.noConflict();
jQuery(document).ready(function($){
    //$('.sem_modal_url_multiple').select2({width:'100%'});
    
    $('#wpbody-content h1').css('margin-bottom','30px');
    var last = "";
    var val = "";
    
    var modal_url = $('.sem_modal_url').val();
    $('.temporary_url').val(get_temporary_url(modal_url));
    
    $('.sem_modal_url').keyup(function(){
        change_temporary_url();
    });
    
    $( 'td' ).on('keyup', '.sem_table', function() {
        change_temporary_url();
    });
    
    $(".all_check").click(function(){
        if($(this).is(':checked')){
            $(".check").attr("checked", true);
        }else{
            $(".check").attr("checked", false);
        }
    });
    
    if($(".meta_title").val() != '' || $("textarea.meta_description").val() != '' || $(".meta_keywords_url").val()){
        add_text_in_area();
    }
   
    $(".meta_title").keyup(function() {
        add_text_in_area();
    });
    
    $(".meta_description").keyup(function() {
        add_text_in_area();
    });
    
    $(".meta_keywords_url").keyup(function() {
        add_text_in_area();
    });
    
    $(".sem_area").keyup(function() {
        var sRegTitle = /meta name=\"title\" content=\"((?:.|\n)*?).>/;
        var sResultTitle = $("textarea.sem_area").val().match(sRegTitle);
        var sRegDescription = /meta name=\"description\" content=\"((?:.|\n)*?).>/;
        var sResultDescription = $("textarea.sem_area").val().match(sRegDescription);
        var sRegKeyword = /meta name=\"keywords\" content=\"((?:.|\n)*?).>/;
        var sResultKeyword = $("textarea.sem_area").val().match(sRegKeyword);
        
        $(".meta_title").val(sResultTitle[1]);
        $("textarea.meta_description").val(sResultDescription[1]); 
        $(".meta_keywords_url").val(sResultKeyword[1]);
    });
    
    var items = ["[variable1]","[variable2]","[variable3]","[variable4]","[variable5]"];
    sem_autocomplete($( ".sem_modal_url" ), items);
    sem_autocomplete($( ".meta_title" ), items);
    sem_autocomplete($( ".meta_description" ), items);
    sem_autocomplete($(".meta_keywords_url"), items);
    
    function sem_autocomplete(box, liste){
        box
           .autocomplete({
            minLength: 0,
            source: function( request, response ) {
              response( jQuery.ui.autocomplete.filter(
                liste, extractLast( request.term ) ) );
            },
            focus: function() {
              return false;
            },
            select: function( event, ui ) {
              var terms = split( this.value );
              // remove the current input
              removeCurrent(terms);
              terms.pop();
              // add the selected item
              terms.push( ui.item.value );
              // add placeholder to get the comma-and-space at the end
              terms.push( "" );
              this.value = terms.join( "" );  // ", "
              change_temporary_url();
              return false;
            }
        });
    }
    
    function add_text_in_area() {
        var text_area_balise = "<head>\n";
    	text_area_balise += "<meta name=\"title\" content=\""+ $(".meta_title").val() +"\">\n";
    	text_area_balise += "<meta name=\"description\" content=\""+ $("textarea.meta_description").val() +"\">\n";
    	text_area_balise += "<meta name=\"keywords\" content=\""+ $(".meta_keywords_url").val() +"\">\n";
    	text_area_balise += "</head>";
    	$(".sem_area").val(text_area_balise);
    }
    
    change_temporary_url();
    
    function change_temporary_url(){
        var modal_url = $('.sem_modal_url').val();
        var variables = modal_url.match(/\[variable[0-9]{1,3}\]/g);
        $('.temporary_url').each(function(index, el){
            var current_td = $(el).parent('td');
            var temporary_url = modal_url;
            while(current_td.next().length){
                current_td = current_td.next();
                var data_var = current_td.find('.sem_table').attr('data_var');
                if(variables != null && variables.indexOf(data_var) != -1){
                    var variable = variables[variables.indexOf(data_var)];
                    temporary_url = temporary_url.replaceAll(variable, current_td.find('.sem_table').val());
	                $(this).val(temporary_url);
                }
            }
			temporary_url = temporary_url.replace(/\$.{0,5}\D/g, '');
            $(this).val(temporary_url);
        });
    }
    
    $('[data-toggle="tooltip"]').tooltip();  
    
    $('#remove_line').click(function(event){
        event.preventDefault();
        var lp_item = new Array();
        $(".check:checked").each(function() {
           lp_item.push($(this).val());
        });
        //console.log(lp_item);
        var data = {'action': 'delete_keyword_by_row_number', 'lp_item': lp_item};
        //console.log(data);
        $.ajax({
			type: 'post',
			url: ajaxurl,
			data: data,
			success: function (response, status) {
			    console.log(response + '+++++++++');
				$.each(lp_item, function(key, value){
                    $('.row-tab[data-line="'+value+'"]').remove();
                })
			}
		});
    });
    
    $('.all_check').click(function(){
        $(this).toggleClass('1');
        if($(this).hasClass('1')){
            $(".check").each(function(index, element) {
               $(element).prop('checked', true);
            });
        }
        else {
            $(".check").each(function(index, element) {
               $(element).prop('checked', false);
            })
        }
    });
   
});

function last_caractere(chaine){
    return chaine[chaine.length-1];
}

function get_temporary_url(modal_url, Jquery){
   
}

function split( val ) {
  return val.split('');
}
function extractLast( term ) {
  return split( term ).pop();
}
function removeCurrent(term) {
  console.log(term[term.length -1])
  while(term[term.length -1] != '[') term.pop();
}

function copyVariable(box, val){
    var input_copy = document.createElement('input');
    input_copy.setAttribute("type", "text");
    input_copy.setAttribute("value", val);
    document.body.appendChild(input_copy);
  
    try {    
        input_copy.select();
        var success = document.execCommand('copy');
        document.body.removeChild(input_copy);
        
    } catch(err) {  
        console.log('Oops, unable to copy');  
    }  
    
}