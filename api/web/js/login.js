$(function(){
    $('.login button').click(function(e){ 
        // Get the url of the link 
        var toLoad = $(this).attr('href');  
 
        // Do some stuff 
        $(this).addClass("loading"); 
 
            // Stop doing stuff  
            // Wait 700ms before loading the url 
            setTimeout(function(){window.location = toLoad}, 10000);      
 
        // Don't let the link do its natural thing 
        e.preventDefault
    });
    
    $('input').each(function() {

       var default_value = this.value;

       $(this).focus(function(){
               if(this.value == default_value) {
                       this.value = '';
               }
       });

       $(this).blur(function(){
               if(this.value == '') {
                       this.value = default_value;
               }
       });

});
});