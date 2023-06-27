<?php

function js_search_entreprise(){

echo "<script>
    $(document).ready(function(){
        $('#search').keyup(function(){
            var query = $(this).val();
            if(query != '')            {                $.ajax({
                    url:'librairie_php/search_entreprise.php',
                    method:'POST',
                    data:{query:query}, success:function(data)
                    {                        $('#userList').fadeIn();
                        $('#userList').html(data);
                    }
                });
            }
        });
        $(document).on('click', 'li', function(){
            $('#search').val($(this).text());
            $('#userList').fadeOut();
        });
    });
</script>";

}
