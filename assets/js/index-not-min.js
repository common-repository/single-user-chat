jQuery( function ( $ ){
	
	var count = 0;
	var xhr = null;
	var timecounter = 0;
	var user_url = ajaxurl+'?action=get_chat_history';
	var user_send_chat_url = ajaxurl+'?action=send_chat_message';
	var auto_refresh = setInterval(
		function(){ 
					get_new_mssgs();
					getuserstatus();
				}, 5000);
		function get_new_mssgs(){
			 var admin_ajax_url = ajaxurl+"?action=get_new_mssgs";
			 $.ajax({
					type:'post',
					datatype:'json',
					url:admin_ajax_url,
					success:function(response){
						data = $.parseJSON(response);
						if (data != null) {
							for(var i =0;i < data.length;i++){
								 var item = data[i];
								 if(item[0] != null){
								 	 $('.chat-msg-notification-'+item[0].sender_id).addClass('active');
								 	if ($('.chat-msg-notification-'+item[0].sender_id)[0]){
									    $('#multichatuserstitle').addClass('blink_me');
									}
								 }
							
							}
						}else{

						}
						
					}
				});

		}

		var auto_refresh = setInterval(
			function()
				{  
					var check_chat_box = $('.chat-head').css("display");
					if(check_chat_box == 'block'){
						user_id = $('#send-chat-button').attr('data-usertosend');
						user_url = $('#send-chat-button').attr('data-getuserhistory');
						if($.active >= 1){
							return 0;
						}else{
							
							getchat(user_id, user_url);
						}
						
					}else{
						
					}
					
					}, 3000);
	$('.open-chat-box').click(function(e){
			e.preventDefault();
    		$('.chat-head').hide(500);
    		var user_id = $(this).attr('id');
			var user_name = $(this).attr('data-providername');
			var profile_pic = $(this).find('img').attr('src');
			 
			
			
			$('.chat-msg-notification-'+user_id).removeClass('active');
			$('#multichatuserstitle').removeClass('blink_me');
			if( xhr != null ) {
		                xhr.abort();
		                xhr = null;
      			  }
			open_chat_box(user_id,user_url,user_send_chat_url,user_name,profile_pic);
			count = 0;
    		getchat(user_id, user_url);
			
		});
	$("h3#multichatuserstitle").click(function(){
			$('#allusers').toggle(500);
			if($('span#multichathub').hasClass('minus')){
				$('span#multichathub').removeClass('minus');
			}else{
				$('span#multichathub').addClass('minus');
			}
			
	});
	function open_chat_box(user_id,user_url,user_send_chat_url,user_name="contact-me",profile_pic){
				var chat_html = '<div class="chat-head" id = "chat-head-'+user_id+'">';
                    chat_html+= '<div class="cross-chat"><i class="fa fa-times shut-off-chat" aria-hidden="true"></i></div>';
					chat_html+=	'<div class="chat-content-section">';	
					chat_html+=	'<div class="chat-contact-info"><a class="open-single-chat-box" href ="#"><img src="'+profile_pic+'" class="avatar avatar-32 photo" width="50" height="50"/><span class="single-username">'+user_name+'</span></a></div>';
					chat_html+= '<div class="previous-message-chat-section">Start chat</div></div>';
					chat_html+= '<div class="writting-section">';
					chat_html+= '<textarea id="chat-mssgs"></textarea><button id="send-chat-button" class="btn btn-primary" data-getuserhistory="'+user_url+'" data-usertosend="'+user_id+'" data-urlprovider="'+user_send_chat_url+'">Send</button></div></div>';			
						
							
					$('#chat-space').html(chat_html);
		}
		function getchat(user_id, user_url){
			var check_chat_box = $('.chat-head').css("display");
    			if( xhr != null ) {
		                xhr.abort();
		                xhr = null;
      			  }
    			$('.chat-head').show(1000);

    			xhr = $.ajax({
					type: 'post',
					datatype: 'json',
					url:user_url,
					data:{user_id:user_id},	
					success:function(response){
													
						if (response && response.length > 0) {
							data = $.parseJSON(response);
							
							if (count< data.length) {
								$('.previous-message-chat-section').html('');
							
								for(var i =0;i < data.length;i++){
									 var item = data[i];
									  if(item.error == 'not-found'){
									  	$('.previous-message-chat-section').html('<span>Let us Chat :)</span>');
									  }
									  if (item.sender_id != user_id) {
										$('#chat-head-'+user_id+' .previous-message-chat-section').append('<div class="chat-message-right">'+decodeHtml(item.message)+'</div><span class="chat-mssg-time-right">'+item.date_time+'</span>');
									  }else{
										$('#chat-head-'+user_id+' .previous-message-chat-section').append('<div class="chat-message-left">'+decodeHtml(item.message)+'</div><span class="chat-mssg-time-left">'+item.date_time+'</span>'); 
										}
									  }
									  $('.previous-message-chat-section').append('<div class="spinner"></div>');
									var isHovered = $('.previous-message-chat-section').is(":hover");
									if(!isHovered){
										$(".previous-message-chat-section").scrollTop($(".previous-message-chat-section")[0].scrollHeight);
									}	
									$('.spinner').hide();	
									count=data.length;
							}else{
								//$('.previous-message-chat-section').html(' no history found say hi..');
							}
							
								 
						
					}else{
						$('.previous-message-chat-section').html('<span>Error occured :(</span>');
					}
					}
				});
				return 0;
    		//}
		}
		function escapeHtml(str)
			{
			    var map =
			    {
			        '&': '&amp;',
			        '<': '&lt;',
			        '>': '&gt;',
			        '"': '&quot;',
			        "'": '&#039;'
			    };
			    return str.replace(/[&<>"']/g, function(m) {return map[m];});
			}
			function decodeHtml(str)
			{
			    var map =
			    {
			        '&amp;': '&',
			        '&lt;': '<',
			        '&gt;': '>',
			        '&quot;': '"',
			        '&#039;': "'"
			    };
			    return str.replace(/&amp;|&lt;|&gt;|&quot;|&#039;/g, function(m) {return map[m];});
			}

		function sendchat(mssg,user_id){
			mssgcheck = mssg.trim();
			mssg  = escapeHtml(mssg);
			if(mssgcheck != ""){
				if( xhr != null ) {
		                xhr.abort();
		                xhr = null;
      			  }
				$.ajax({
					type:'post',
					datatype:'json',
					url:user_send_chat_url,
					data:{user_id:user_id,mssg:mssg},
					beforeSend: function(){
				        $('#chat-head-'+user_id+' .previous-message-chat-section').append('<div class="chat-message-right">'+mssg+'</div><span class="chat-mssg-time-right error-mssg-chat" style="display:none;">can\'t send empty mssg</span><span class="chat-mssg-time-right sending-mssg">Sending....</span>');
				        $(".previous-message-chat-section").scrollTop($(".previous-message-chat-section")[0].scrollHeight);
				    },
					success:function(response){
						$('.sending-mssg').css('display','none');
						getchat(user_id, user_url);
					}

				});
			}else{

			}
		}
		$(document).ajaxStop(function(){
			$('.shut-off-chat').click(function(){
			$('.chat-head').hide(500);
			$('.chat-head').css('display','none');
			});

			$('#send-chat-button').unbind().click(function(){
				var mssgs  = $('#chat-mssgs').val();
				var usertosend = $(this).attr('data-usertosend');
				sendchat(mssgs, usertosend);
				$('#chat-mssgs').val('');
			});

			$("#chat-mssgs").keypress(function (e) {
		        if(e.which == 13) {
		            var usertosend = $(this).siblings('button').attr('data-usertosend');
		            var mssg = $(this).val();
		            sendchat(mssg, usertosend);
		            $(this).val("");
		        }
		    });

		});


		function getuserstatus(){
			 var admin_ajax_url = ajaxurl+"?action=get_user_status";
			 $.ajax({
					type:'post',
					datatype:'json',
					url:admin_ajax_url,
					success:function(response){
						data = $.parseJSON(response);
						if (data != null) {
							for(var i =0;i < data.length;i++){
								if (data[i].activeuser) {
									$('.chat-msg-status-'+data[i].activeuser).removeClass('offline');
									$('.chat-msg-status-'+data[i].activeuser).addClass('active');				
								}
								if (data[i].offline){
									$('.chat-msg-status-'+data[i].offline).removeClass('active');
									$('.chat-msg-status-'+data[i].offline).addClass('offline');
								}
							}
								}
						}
					
				});
		}
		
		 

		 
		
});


