$(document).ready(function(){

    function init() {   
        $.ajax({
            url: "acoes.php",
            type: "post",
            dataType:"json",
            success: function(response){
                console.log(response);
                setTimeout(init,2000)
            }
        });
    };

    //inicia verificação de aquivos
    init();
})