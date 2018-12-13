/*SIDEBAR DROPDOWN SUBMENU SWITCHER*/
$(document).ready(function(){
    $('ul.sidebar-menu').find('li.dropdown>a').click(function(){
        $(this).closest('.dropdown').find('a>i.fa-angle-right').toggleClass('down');
        $(this).siblings('ul.sidebar_submenu').slideToggle(200);
    });
});


/* HIDE / SHOW SIDEBAR */
$(document).ready(function(){
    var menuClicksCounter = 0;
    $('#hamburger_menu').click(function(){ 
        if ($(window).width() > 815 ) {
             
            if (menuClicksCounter == 0) {
                $('#sidebar-nav').animate({'left' : '-200px'},200);
                menuClicksCounter++;
            } else {
                $('#sidebar-nav').animate({'left' : '0px'},200);
                menuClicksCounter = 0;
            }
            
            $('#main-content').find('div.content').toggleClass('content_full_width');
        } else { 
            $('.sidebar-menu').slideToggle();            
        }
    });
});



/*Connection to remote db via ajax*/

$(document).ready(function(){
    $('#check_connection').click(function(e) {
        e.preventDefault();
        var formData = new FormData($('#db_connection_form')[0]);

        if (formData.get('ip_address') == false) {
            $('#ip_span').text('Field with db. address can\'t be empty!');
        } else if (formData.get('db_user') == false) {
            $('#db_user_span').text('Field with user name can\'t be empty!');
        } else if (formData.get('db_name') == false) {
            $('#db_name_span').text('Field with database name can\'t be empty!');
        } else {
            $.ajax({
                url: 'includes/ajax_db_connection.php',
                type: 'POST',
                data: formData,
                success: function (data) {
                    $('#connection_response').html(data);
                },
                cache: false,
                contentType: false,
                processData: false
            });

            $('#ip_span').text('');
            $('#db_user_span').text('');
            $('#db_name_span').text('');
        }

    });

});



/*Send data to remote DB via ajax*/

$(document).ready(function(){
    $('#upload_to_rem_db').click(function(e) {
        e.preventDefault();


        $.ajax({
            url: "./includes/ajax_transfer_data_to_remote_db.php?send_data=true",
            type: "get",
            beforeSend: function(){
                $('#upload_to_rem_db').css('display', 'none');
                $('#preloader').show();
            },
            complete: function(){
                $('#preloader').hide();
            },
            success: function(data){
                if(!data.error){
                    $('#upl_to_rem_db_butt_div').html(data);
                    //console.log(data);
                }
            }
        })


    });

});


