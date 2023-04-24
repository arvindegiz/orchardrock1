jQuery(document).ready(function() {
          jQuery("#search_default").click(function(){
          var search_val =   jQuery('#product_search').val();
          alert(search_val);
         if(search_val != "") {
            jQuery.ajax({
                type: "post",
		dataType : 'json',
                url: my_ajax_object.ajax_url,
                data : {
                    action : 'my_action_callback',
                    search : search_val
                },
                success : function( response ) {
                    console.log('hhhhh');
                }
            });
          
           
        }

    })
});