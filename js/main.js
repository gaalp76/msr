var ImgGallery;

$.fn.selectRange = function(start, end) {
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

function getUrlParameter(sParam,url) 
{

	var sPageURL = url.split('?'),
    sURLVariables = sPageURL[1].split('&'),
    sParameterName,
    i;
   
	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');
		if (sParameterName[0] === sParam) {

			return sParameterName[1] === undefined ? true : sParameterName[1];
		}
	}
}	

$(window).bind('popstate', function(e) {

		var state = e.originalEvent.state;
		if(state) {
  		var menu = getUrlParameter("menu",state.url);
  		var param = state.url.split("?");
		get_content(menu,param[1],1);
	}
});

function initPage()
{
	$.get("php/main.php",{ menu:"login" }, function(data) {

		data = jQuery.parseJSON(data);
		if(data.state == 'login')
		{
			$('#content-container').html(data.content);
			
			$("#loginForm").validate({
				rules: {
						username: {
							required: true
						},

						password: {
							required: true
						}
					},
					submitHandler: function() {	
						var data = $('#loginForm').serializeArray();
						data.push({name: "login", value: "1"});

						$.get("php/main.php",data, function(data) {
							data = jQuery.parseJSON(data);
							if(data.message == 'success_login')
							{
								initPage();
								if(story.pushState) {
								    story.pushState(null, null, '#Home');
								}
								else {
								    location.hash = '#Home';
								}
								showMessage(data.message);
							}
							else 
							{ 
								$('#loginForm').find("input[type=text], input[type=password]").val("");
								showMessage(data.message); 
							}
						});
							
					},
					messages: {
			                username: {
			                	required: $.validator.messages.empty_login_username
			                },
			                password: {
			                	required: $.validator.messages.empty_login_password
			                }		                
			        }
						
			});
		}
		else
		{
			$('nav').html(data.content);
			$('#content-container').html("");

			var menu = window.location.hash.replace('#','');
			menu = menu.length?menu:'Home';
			get_content(menu,'',0);
			$('#logout-container').remove();
			$('#show-login-btn').remove();
			$('header').before(data.logout);
			$(".logout-btn").slideDown({	}, { duration: 200, queue: false});
		}
		
	});	
}

function getLinkedToFromMenu(menu)
{
	var i = 0;
	while (menu.charAt(i) !== menu.charAt(i++).toUpperCase());
	return menu.substring(0, i - 1 );
}

function getInstanceNameFromMenu(menu)
{
	var i = 0;
	var instanceStart = menu.lastIndexOf('Of');
	if(instanceStart != -1 ) return menu.substring(instanceStart);
	return '';
}



function goTop()
{
	$('html, body').animate({
        scrollTop: 0
    }, 200);
}

function showMessage(msg,onTop=1)
{	
	var msgType;	
	
	if ( msg.indexOf("failed") >= 0 ) 
	{
		msgType = "msg-error";
	}
	if ( msg.indexOf("empty") >= 0 || msg.indexOf("warning") >= 0 ) 
	{
		msgType = "msg-info";
	}
	if ( msg.indexOf("success") >= 0 ) 
	{
		msgType = "msg-success";
	}

	$('#message').html($.validator.messages[msg]);
	$('#message').removeClass();
	$('#message').addClass(msgType);

	$('#message').show();
}

function show_article_title(menu)
{
	
	if (!$(".tile[menu=" + menu + "]").length) {return}
	var title = "<h1>"+$(".tile[menu=" + menu + "]").find('.caption').html() +"</h1>";
}

function show_menu(menu)
{

	if($(".menu-container[menu=" + menu + "]").is(":hidden") || !$(".menu-container[menu=" + menu + "]").length	)
	{
		if($(".menu-container[menu=" + menu + "]").length)
		{
			$(function () {
				
				$(".menu-container").slideUp({
					
			    }, { duration: 200, queue: false });

			   	$(".menu-container[menu=" + menu + "]").slideDown({
			       
			    }, { duration: 200, queue: false});
			});
		}	
		else if($(".tile[menu=" + menu + "]").is(":hidden") )
		{
			$(".menu-container").slideUp({
					
			    }, { duration: 200, queue: false });

			$(".tile[menu=" + menu + "]").parents(".menu-container").slideDown({
			       
			}, { duration: 200, queue: false});
		}
	}
}

function setEnvironment(compId){
	if (compId == '') 
	{
		compId = "default";
		$('#competition-name').hide();
		if ($('#information-box').is(':hidden')) $('#information-box').show();
	}
	else
	{
		$('#competition-name').show();
		if ($('#information-box').is(':visible')) $('#information-box').hide();
	}	
	var competitionName = "";
	var facebook, mailto, logo, promoMedia;
	
	$.each(competitionsArray, function(index, competition){
		if (competition.id == compId){
			competitionName = competition.name;
			facebook = competition.facebookLink;
			mailto = competition.mailto;
			logo = competition.logo;
			promoMedia = competition.promoMedia;
			return;
		} 
	});

	$("#competition-name").html(competitionName);
	$("#facebook a").prop("href", facebook);
	$("#mailto a").prop("href", "mailto:"+mailto);
	$('#logo').css("background-image", "url("+logo+")");

	for (var i = 0; i < 3; i++) {
		var i2 = i+1;
		var filename =  promoMedia[i];
		var extension = filename.substr( (filename.lastIndexOf('.') +1) );

		switch(extension){
			case "jpg":
			case "png":
			case "gif":
				$("#box"+i2).html('<img src="'+filename+'">');
			break;
			case "mp4":
				$("#box"+i2).html('<video autoplay loop muted><source src="'+filename+'" type="video/mp4"></video>');
		}
	}

}

function get_content(menu,param,isHistory, target='main')
{
	
	menu = (menu == 'up')?$(".tile[menu=" + $(".menu-container:visible").attr("menu") + "]").parents(".menu-container").attr("menu"):menu;
	var linked_to = getLinkedToFromMenu(menu);
	var instanceOf = getInstanceNameFromMenu(menu);

	var upload_root_folder = $(".tile[menu=" + menu + "]").attr("upload_root_folder");	
	var url = 'php/main.php?target='+target+'&upload_root_folder=' + 
	(typeof upload_root_folder=='undefined'?'':upload_root_folder) + '&linked_to=' + linked_to + '&instanceOf=' + instanceOf;

	if ((menu != "LoadMenuItems" && 
		menu != 'UsersAdd' && 
		menu != "UsersEdit" && 
		menu != "ForgotPasswordForm" &&
		menu != "ChangePassword" &&
		menu != "ConfirmRegistration" &&
		menu != "ConfirmCompetitionRegistration" &&
		menu != "NewsView" &&
		menu != "GalleryAlbum") || $(".menu-container[menu=" + menu + "]").is(':visible')
		)
	{
		show_menu(menu);
	}
	else
	{
		show_menu('Home');
	}

	$('#message').hide();

	switch (menu)
			{
				case linked_to + 'Login' + instanceOf:	
					url = url + '&page=showLogin';
				break;

				case 'CheckOpenCompetitions':	
					url = url + '&page=showCheckOpenCompetitions';
				break;

				case linked_to + 'ForgotPassword' + instanceOf:	
					url = url + '&page=showForgotPassword';
				break;

				case linked_to + 'ForgotPasswordForm' + instanceOf:	
					url = url + '&page=showForgotPasswordForm';
				break;

				case linked_to + 'ChangePassword' + instanceOf:	
					url = url + '&page=showChangePassword';
				break;

				case linked_to + 'Home' + instanceOf:	
					url = url + '&page=showHome';
				break;

				case linked_to + 'ChangeLanguage' + instanceOf:	
					url = url + '&page=ShowChangeLanguage';
				break;

				case linked_to + 'Msr' + instanceOf:
					
					url = url + '&page=showMsr';
				break;

				case linked_to + 'Users' + instanceOf:
					url = url + '&page=showUsersInfo';
				break;

				case linked_to + 'UsersAdd' + instanceOf:
					url = url + '&page=showUsersAdd';			
				break;

				case linked_to + 'UserRemove' + instanceOf:
					url = url + '&page=showUserRemove';			
				break;

				case linked_to + 'UsersDelete' + instanceOf:
					url = url + '&page=showUsersDelete';
				break;

				case linked_to + 'UsersEdit' + instanceOf:
					url = url + '&page=showUsersEdit';
				break;

				case linked_to + 'UsersAuthority' + instanceOf:
					url = url + '&page=showUsersAuthority';
				break;

	// ******************************************************************************
	// *                             Támogatók kezelése                             *
	// ******************************************************************************
				
				case linked_to + 'Donation' + instanceOf:
					url = url + '&page=showDonation';
				break;

	// ******************************************************************************
	// *                             Hírek kezelése                                 *
	// ******************************************************************************
				
				case linked_to + 'News' + instanceOf:
					url = url + '&page=showNews';
				break;

				case linked_to + 'NewsView' + instanceOf:
					url = url + '&page=showNewsView';
				break;

				case linked_to + 'NewsSearch' + instanceOf:
					url = url + '&page=showNewsSearch';
				break;

				

	// ******************************************************************************
	// *                     Dokumentumok csatolása                                 *
	// ******************************************************************************

				case linked_to + 'Documents' + instanceOf:
					url = url + '&page=showDocuments';
				break;

	// ******************************************************************************
	// *                              Galéria                                       *
	// ******************************************************************************

				case linked_to + 'Gallery' + instanceOf:
					url = url + '&page=showGallery';
				break;

				case linked_to + "GalleryAlbum" + instanceOf:
					url = url + '&page=showGalleryAlbum';
				break;

	// ******************************************************************************
	// *                             Rólunk                                         *
	// ******************************************************************************

				case linked_to + 'AboutUs' + instanceOf:
					url = url + '&page=showAboutUs';
				break;

	// ******************************************************************************
	// *                      Versenyszabályok menü                                 *
	// ******************************************************************************

				case linked_to + 'Rules' + instanceOf:
					url = url + '&page=showRules';
				break;

	// ******************************************************************************
	// *                             Kapcsolat menü                                 *
	// ******************************************************************************


				case linked_to + 'Contacts' + instanceOf:
					url = url + '&page=showContacts';
				break;

				
	// ******************************************************************************
	// *                             Történetünk menü                               *
	// ******************************************************************************


				case linked_to + 'Story' + instanceOf:
					url = url + '&page=showStory';
				break;

				
	// ******************************************************************************
	// *                             Akadályok menü                               *
	// ******************************************************************************


				case linked_to + 'CompetitionObstacles' + instanceOf:
					url = url + '&page=showCompetitionObstacles';
				break;


	// ******************************************************************************
	// *                             Pályavázlat menü                               *
	// ******************************************************************************


				case linked_to + 'CompetitionFieldDescription' + instanceOf:
					url = url + '&page=showCompetitionFieldDescription';
				break;

	// ******************************************************************************
	// *                             Versenytérkép menü                             *
	// ******************************************************************************


				case linked_to + 'CompetitionMap' + instanceOf:
					url = url + '&page=showCompetitionMap';
				break;

				
	// ******************************************************************************
	// *                             Versenyinfó menü                               *
	// ******************************************************************************

				case linked_to + 'CompetitionInfo' + instanceOf:
					url = url + '&page=showCompetitionInfo';
				break;
				

	// ******************************************************************************
	// *                         Verseny megközelíthetőség menü                     *
	// ******************************************************************************

				case linked_to + 'CompetitionApproach' + instanceOf:
					url = url + '&page=showCompetitionApproach';
				break;

	// ******************************************************************************
	// *                         Nevezés menü 					                    *
	// ******************************************************************************

				case linked_to + 'Entry' + instanceOf:
					url = url + '&page=showEntry'; 
				break;

	
				default:			
					
				break;
			}
		

	if(param.length)
	{	
		url = url + '&' + param;
	}

	url = url + '&menu=' + menu;

	if(!isHistory) 
	{
		
		history.pushState({ url: url },'','#' + menu);
	}

	load_content(url);
	return false;
}

function load_content(url)
{	
	if(url !== "")
	{	
		var waiting = $.confirm({
					icon: 'loading',
				    theme: 'supervan',
				    boxWidth: '30%',
				    lazyOpen: true,
					useBootstrap: false,
				    title: $.validator.messages.msg_loading,
				    content: '',
				    closeIcon: false
				});

		waiting.open();
		
		$.get(url, function(data) {
			waiting.close();
			var page = getUrlParameter('page',url);
			var target = getUrlParameter('target',url);
			var linked_to = getUrlParameter('linked_to',url);
			var instanceOf = getUrlParameter('instanceOf',url);
			var menu = getUrlParameter('menu',url);
			var start_page = getUrlParameter('start_page',url);
			var username = getUrlParameter('username',url);
			var plang = getUrlParameter('lang',url);
			var hash = getUrlParameter('hash',url);
			var comp_reg_id = getUrlParameter('comp_reg_id',url);
			var competitionID = getUrlParameter('competitionID',url);
			var teamID = getUrlParameter('teamID',url);
			var action = getUrlParameter('action',url);

			
			var envCompetitionId = linked_to;
			$.each(competitionsArray, function(index, competition){
				if (competition.id == menu.toLowerCase()) {
					envCompetitionId = menu.toLowerCase();
					return;
				}
			});

			setEnvironment(envCompetitionId);
			
			data = jQuery.parseJSON(data);
			
			if (data.logout && target == 'main')
			{
				$('#logout-container').remove();
				$('#show-login-btn').remove();
				$('#menu-box').after(data.logout);
				$('#login-container').slideUp();
			}

			if(data.message) { showMessage(data.message, data.ontop); }
			if(data.ontop) { goTop(); }

			switch (target) //target switching
			{
				case 'information-box':
					switch (page)
					{
						case 'showCheckOpenCompetitions':
							$("#information-box .content").append(data.content);
							$('.info-entry').click(function(){
								get_content($(this).attr("linkedto") + "Entry","",0);
								$('#information-box .open-close').removeClass('close');
								$('#information-box .open-close').addClass('open');
								$('#information-box').animate({left:"-=178"}, 100); 
							});

						break;

					}
				break;

				case 'main':

					$(".banner-image").remove();

					if(!$("#content").length)
					{
						var $content = $('<div>', {'id': 'content'});
						$('#content-container').append($content);
					}

					if(data.banner)
					{
						
						var $bannerImage = $('<div>', {'class': 'banner-image'+' ' + data.banner});
						$('#content').before($bannerImage);
					}

					if(data.content && data.append) { $('#content').append(data.content); }	
					else if(data.content) 
					{ 
							$('#content').html(data.content);
							show_article_title(menu);
							if (data.document) 
							{
								if ($('#content-container').length) 
								{
									$('#content-container').html('<div id="right-side-bar"><span>'+$.validator.messages["attached_documents"]+'</span>'+data.document+'</div>');
								}
								else{
									$('#content-container').append('<div id="right-side-bar"><span>'+$.validator.messages["attached_documents"]+'</span>'+data.document+'</div>');
								}
							}
							else
							{
								$('#right-side-bar').remove();
							}

							$('#content-container').slideDown();
					}	

					if (data.news_search_form)
					{
						$('#content-container .content').prepend(data.news_search_form);
					}		
	

					
					switch (page) //page switching
					{	
						case 'showHome':
							
						break;

						

						case 'showMsr':
							
						break;

						case 'showUsersInfo':
							
						break;

						case 'showForgotPasswordForm':

							$('#forgot_pw_form').validate({
									rules: {
										new_password: {
											required: true,
											minlength:8
										},
										confirm_new_password: {
											required: true,
											minlength: 8,
											equalTo: "#new_password"
										}
							            
									},
									messages: {
							                reg_username: {
							                   	remote: $.validator.messages.exist_username
							                }
							        },
									invalidHandler: function(event, validator) {
												
									},
									submitHandler: function() {	
										var data = $('#forgot_pw_form').serializeArray();
										get_content("ChangePassword",$.param(data),0);
									}
								});
						break;

						case 'showChangePassword':
							if (data.message=='success_password_change') 
							{
								$('#content-container').slideUp();
							}
						break;

						case 'showUserRemove':
							$('#logout-container').remove();						
							$('nav').append(data.login_container);
							$('#content-container').slideUp();
						break;

						case 'showUsersAdd':
					
							$('#login-container').slideUp();

							$('#phone, #er_phone').mask("99/999-9999");

							$('#born_date').mask("9999-99-99");
							
							$('#mailing-zip').blur(function(){
								if ( $('#mailing-zip').val() != '' ) {
									$.get('php/get_city_from_zip.php',{'zip':$('#mailing-zip').val()}, function(data){
										$('#mailing-city').val(data);
									});		
								}	
							});
							

							$('#sign-up-btn').click(function(){
								//if ($('#registration-form').valid()) {
									$('#registration-form').submit();
								//}

							});

							

							jQuery.validator.addMethod('phone', function (phone, element) {
									phone = phone.replace(/\s+/g, '');
									return this.optional(element) || phone.length > 9 &&
										  phone.match(/^\(?[\d\s]{2}\/[\d\s]{3}-[\d\s]{4}$/);
							},  $.validator.messages.failed_phone);

							jQuery.validator.addMethod('born_date', function (born_date, element) {
									born_date = born_date.replace(/\s+/g, '');
									return this.optional(element) || born_date.length > 10 &&
										  phone.match(/^\(?[\d\s]{4}-[\d\s]{2}-[\d\s]{2}$/);
							},  $.validator.messages.born_date);

							jQuery.validator.addMethod('email', function (email, element) {
									var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    								return re.test(String(email).toLowerCase());
									
							},  $.validator.messages.email);

							

          					$("#accept_rules").change(function(e){

								if($("#accept_rules").is(':checked'))
								{
									$("#sign-up-btn").removeClass('blue2');
									$("#sign-up-btn").addClass('blue1');
								}
								else
								{
									$("#sign-up-btn").removeClass('blue1');
									$("#sign-up-btn").addClass('blue2');
								}
							});

							$('#registration-form').validate({
									rules: {
										firstname: {
											required: true
										},
										lastname: {
											required: true
										},
										born_date: {
											required: true
										},
										mother_name: {
											required: true
										},
										email: {
											required: true,
											email: true,
											remote: {
										        url: "php/main.php",
										        type: "get",
										        data: {
											        	menu: "UsersAdd",
											        	action: "checkUserEmailExists",
														reg_username: function() {
												            return $( "#email" ).val();
												          }
													}
										        }
										},
										reg_username: {
											required: true,
											minlength:6,
											remote: {
										        url: "php/main.php",
										        type: "get",
										        data: {
											        	menu: "UsersAdd",
											        	action: "checkUserExists",
														reg_username: function() {
												            return $( "#reg_username" ).val();
												          }
													}
										        }
										      
										},
										reg_password: {
											required: true,
											minlength:8
										},
										confirm_reg_password: {
											required: true,
											minlength: 8,
											equalTo: "#reg_password"
										},
										phone: {
											required: false,
											phone:true
										},
										er_phone: {
											required: false,
											phone:true
										},
										er_name: {
											required: false
										}
							            
									},
									messages: {
							                reg_username: {
							                   	remote: $.validator.messages.exist_username
							                },
							                email: {
							                   	remote: $.validator.messages.exist_email
							                }
							        },
									invalidHandler: function(event, validator) {
												
									},
									submitHandler: function() {	
										if($("#accept_rules").is(':checked'))
										{
											var data = $('#registration-form').serializeArray();
											data.push({name: "action", value: "addCommonUser"});
											get_content("UsersAdd",$.param(data),0);
											$('#content-container').slideUp();
										}
									}
								});
						break;

						case 'showUsersEdit':
					
							$('#login-container').slideUp();

							$('#phone').mask("99/999-9999");

							$('#born_date').mask("9999-99-99");

							$("#accept_rules").change(function(e){

								if($("#accept_rules").is(':checked'))
								{
									$("#sign-up-btn").removeClass('blue2');
									$("#sign-up-btn").addClass('blue1');
								}
								else
								{
									$("#sign-up-btn").removeClass('blue1');
									$("#sign-up-btn").addClass('blue2');
								}
							});
							
							jQuery.validator.addMethod('born_date', function (born_date, element) {
									born_date = born_date.replace(/\s+/g, '');
									return this.optional(element) || born_date.length > 10 &&
										  phone.match(/^\(?[\d\s]{4}-[\d\s]{2}-[\d\s]{2}$/);
							},  $.validator.messages.born_date);

							$('#mailing-zip').blur(function(){
								if ( $('#mailing-zip').val() != '' ) {
									$.get('php/get_city_from_zip.php',{'zip':$('#mailing-zip').val()}, function(data){
										$('#mailing-city').val(data);
									});		
								}	
							});
							

							$('#sign-up-btn').click(function(){
								//if ($('#registration-form').valid()) {
									$('#registration-form').submit();
								//}

							});

							$('#remove-btn').click(function(){

								$.confirm({
								title: 'Törlés',
								boxWidth: '30%',
								useBootstrap: false,
								content: 'Biztosan törli magát a portálról? Törlés esetén az adatbázisból minden bejegyzését törölni fogjuk, beleértve a verseny nevezéseit is.',	
								buttons: {
									confirm: {
										text: 'Igen',
										action: function(){
											
											get_content("UserRemove","",0);
										}
									},
									cancel: {
										text: 'Mégsem',
										
									}
								}
								});
							});

							jQuery.validator.addMethod('phone', function (phone, element) {
									phone = phone.replace(/\s+/g, '');
									return this.optional(element) || phone.length > 9 &&
										  phone.match(/^\(?[\d\s]{2}\/[\d\s]{3}-[\d\s]{4}$/);
							},  $.validator.messages.failed_phone);

							jQuery.validator.addMethod('email', function (email, element) {
									var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    								return re.test(String(email).toLowerCase());
									
							},  $.validator.messages.email);

							$('#registration-form').validate({
									rules: {
										firstname: {
											required: true
										},
										lastname: {
											required: true
										},
										reg_password: {
											required: true,
											minlength:8
										},
										mother_name: {
											required: true
										},
										born_date: {
											required: true
										},
										confirm_reg_password: {
											required: true,
											minlength: 8,
											equalTo: "#reg_password"
										},
										phone: {
											required: false,
											phone:true
										}
							            
									},
									invalidHandler: function(event, validator) {
												
									},
									submitHandler: function() {	
										if($("#accept_rules").is(':checked'))
										{
											var data = $('#registration-form').serializeArray();
											data.push({name: "action", value: "editUser"});
											get_content("UsersEdit",$.param(data),0);
										}
									}
								});
						break;

						case 'showNews':
							
						case 'showNewsSearch':
						case 'showNewsView':
							$( "#start_date, #end_date" ).datepicker({
					  			dateFormat:"yy-mm-dd",
								prevText:"Előző hónap",
           						nextText:"Következő hónap",
          						dayNamesMin:[ "Va", "Hé", "K", "Sze", "Cs", "P", "Szo" ],	
          						dayNamesShort: [ "Va", "Hé", "Ke", "Sze", "Csüt", "Pé", "Szo" ],
          						dayNames: [ "Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat" ],
          						firstDay: 1,
          						monthNamesShort :[ "Jan", "Feb", "Már", "Ápr", "Máj", "Jún", "Júl", "Aug", "Szep", "Okt", "Nov", "Dec" ],
          						monthNames : [ "Január", "Február", "Március", "Április", "Május", "Június", "Július", "Augusztus", "Szeptember", "Október", "November", "December" ],
          						navigationAsDateFormat: true,
          						onSelect: function(){
          							$(this).css("background-color","#d4e2f9");
          						},
          						onClose: function(){      					
          						}
          					});


							$(".news-item-header" ).click(function(e) {
								e.preventDefault();
					  			get_content(linked_to + 'NewsView' + instanceOf,'newsID='+$(this).attr('newsID'),0);
							});

          					$( "#newsSearchForm" ).submit(function( e ) {
								var data = $('#newsSearchForm').serializeArray();
								data.push({name: "action", value: "showNewsResult"});
								e.preventDefault();
								get_content( linked_to + "NewsSearch" + instanceOf,$.param(data),1);
							});
							

							$(".news-search-icon" ).click(function(e) {
								$("#newsSearchForm").slideToggle( "slow" );	 						 		
							});

							$(".news-search-btn" ).click(function(e) {
								 						 		
							});

						break;

						case 'showDocumentAttachment':
						break;

						case 'showAboutUs':

						break;

						case 'showRules':							
							
						break;

						case 'showContacts':
					
						break;

						case 'showDonation':
					
						break;
						case 'showDocuments':

						break;

						case 'showGallery':
							
							$(".album-container" ).click(function(e) {
								get_content(linked_to + "GalleryAlbum" + instanceOf,"albumID="+$(this).attr("albumID"),0);
								e.preventDefault(); 						 		
							});
							$('#content-container').css({"background-image":"url(img/common/military-wallpaper.jpg"});
						break;
						case 'showGalleryAlbum':
							

							$('#content-container').empty();
							$('#content-container').html(data.content);
							
							if(!ImgGallery){
								ImgGallery = $("#gallery").unitegallery({
									gallery_theme: "tiles"
								});
							}
							else
							{
								
								ImgGallery.destroy();
								ImgGallery = $("#gallery").unitegallery({
									gallery_theme: "tiles",
									theme_autoplay: true
								  });
							}
							
							$('#content-container').slideDown("slow");	
						break;

						case 'showStory':
							
							
						break;

						case 'showCompetitionObstacles':
							
						break;

						case 'showCompetitionFieldDescription':
							
						break;

						case 'showCompetitionInfo':

						break;

						case 'showCompetitionMap':
							
						break;

						case 'showCompetitionApproach':
							
						break;

						case 'showEntry':

							$('#phone, #er_phone').mask("99/999-9999");
							$('#born_date').mask("9999-99-99");

							$('#mailing-zip').blur(function(){
								if ( $('#mailing-zip').val() != '' ) {
									$.get('php/get_city_from_zip.php',{'zip':$('#mailing-zip').val()}, function(data){
										$('#mailing-city').val(data);
									});		
								}	
							});

							$("#accept_rules").change(function(e){

								if($("#accept_rules").is(':checked'))
								{
									$("#sign-up-btn").removeClass('blue2');
									$("#sign-up-btn").addClass('blue1');
								}
								else
								{
									$("#sign-up-btn").removeClass('blue1');
									$("#sign-up-btn").addClass('blue2');
								}
							});

							$("#guest_number").change(function(){
								$('#guest-data-container').remove();
								if($(this).val())
								{
									var $guestDataContainer = $('<div>', {'id': 'guest-data-container'});
									var i;
									for (i = 0; i < $(this).val(); i++)
									{
										var $guestData 				 = $('<div>', {'class': 'a-guest-data-container'});
										var $guestDataHeader		 = $('<div>', {'class': 'a-guest-data-header','html':(i + 1) + '. kísérő adatai'});
										var $guestDataEmailLabel 	 = $('<label>', {'class': 'guest-data-label','html':'Email-cím:'});
										var $guestDataEmail 	 	 = $('<input>', {'id':'guest-data-email_' + i,'class': 'guest-data-email','type':'text','name':'guest_data['+ i +'][email]'});
										var $guestDataLastNameLabel	 = $('<label>', {'class': 'guest-data-label','html': 'Vezetéknév:'});
										var $guestDataLastName 		 = $('<input>', {'id':'guest-data-lastname_' + i,'class': 'guest-data-lastname','type':'text','name':'guest_data['+ i +'][lastname]'});
										var $guestDataFirstNameLabel = $('<label>', {'class': 'guest-data-label','html':'Keresztnév:'});
										var $guestDataFirstName 	 = $('<input>', {'id':'guest-data-firstname_' + i,'class': 'guest-data-firstname','type':'text','name':'guest_data['+ i +'][firstname]'});
										var $guestDataBornNameLabel	 = $('<label>', {'class': 'guest-data-label','html':'Születési név:'});
										var $guestDataBornName 		 = $('<input>', {'id':'guest-data-born-name_' + i,'class': 'guest-data-born-name','type':'text','name':'guest_data['+ i +'][bornname]'});
										var $guestDataMotherNameLabel= $('<label>', {'class': 'guest-data-label','html':'Anyja neve:'});
										var $guestDataMotherName 	 = $('<input>', {'id':'guest-data-mother_name_' + i,'class': 'guest-data-mothername','type':'text','name':'guest_data['+ i +'][mothername]'});
										var $guestDataBornDateLabel	 = $('<label>', {'class': 'guest-data-label','html':'Születési dátum:'});
										var $guestDataBornDate 	 	 = $('<input>', {'id':'guest-data-born-date_' + i,'class': 'guest-data-born-date','type':'text','name':'guest_data['+ i +'][borndate]'}); 
										var $guestDataGenderLabel 	 = $('<label>', {'class': 'guest-data-label','html':'Neme:'});
										var $guestDataGender 		 = $('<select>', {'class': 'guest-data','name':'guest_data['+ i +'][gender]'});
										var $guestDataGenderMale	 = $('<option>', {'class': 'guest-data','value':'1','html':'Férfi'});
										var $guestDataGenderFemale	 = $('<option>', {'class': 'guest-data','value':'2','html':'Nő'});
										var $guestDataNationalityLabel = $('<label>', {'class': 'guest-data-label','html':'Állampolgárság:'});
										var $guestDataNationality 	 = $('<input>', {'id':'guest-data-nationality_' + i,'class': 'guest-data-nationality','type':'text','name':'guest_data['+ i +'][nationality]'}); 
										var $guestDataPIDLabel = $('<label>', {'class': 'guest-data-label','html':'Igazolvány száma:'});
										var $guestDataPID 	 = $('<input>', {'id':'guest-data-pid_' + i,'class': 'guest-data-pid','type':'text','name':'guest_data['+ i +'][pid]'});
										var $guestDataPIDTypeLabel = $('<label>', {'class': 'guest-data-label','html':'Igazolvány típusa:'});
										var $guestDataPIDType 	 = $('<input>', {'id':'guest-data-pid-type_' + i,'class': 'guest-data-pid-type','type':'text','name':'guest_data['+ i +'][pid_type]'});
										var $guestDataPhoneLabel = $('<label>', {'class': 'guest-data-label','html':'Telefonszám:'});
										var $guestDataPhone 	 = $('<input>', {'id':'guest-data-phone_' + i,'class': 'guest-data-phone','type':'text','name':'guest_data['+ i +'][phone]'});
										var $guestDataAutoLabel = $('<label>', {'class': 'guest-data-label','html':'Gépjármű rendszáma, típusa:'});
										var $guestDataAuto 	 = $('<input>', {'id':'guest-data-auto_' + i,'class': 'guest-data-auto','type':'text','name':'guest_data['+ i +'][auto_data]'});
										var $guestDataZIPLabel = $('<label>', {'class': 'guest-data-label','html':'Irányítószám:'});
										var $guestDataZIP 	 = $('<input>', {'id':'guest-data-zip_' + i,'class': 'guest-data-zip','type':'text','name':'guest_data['+ i +'][zip]'});
										var $guestDataCityLabel = $('<label>', {'class': 'guest-data-label','html':'Település:'});
										var $guestDataCity 	 = $('<input>', {'id':'guest-data-city_' + i,'class': 'guest-data-city','type':'text','name':'guest_data['+ i +'][city]'});
										var $guestDataAddressLabel = $('<label>', {'class': 'guest-data-label','html':'Cím (utca,hszám):'});
										var $guestDataAddress 	 = $('<input>', {'id':'guest-data-address_' + i,'class': 'guest-data-address','type':'text','name':'guest_data['+ i +'][address]'});
										var $guestDataERNameLabel = $('<label>', {'class': 'guest-data-label','html':'Baleset esetén értesítendő neve:'});
										var $guestDataERName 	 = $('<input>', {'id':'guest-data-er-name_' + i,'class': 'guest-data-er-name','type':'text','name':'guest_data['+ i +'][er_name]'});
										var $guestDataERPhoneLabel = $('<label>', {'class': 'guest-data-label','html':'Baleset esetén értesítendő telefonszáma:'});
										var $guestDataERPhone 	 = $('<input>', {'id':'guest-data-er-phone_' + i,'class': 'guest-data-er-phone','type':'text','name':'guest_data['+ i +'][er_phone]'});

										$guestDataGender.append($guestDataGenderMale,$guestDataGenderFemale);
										$guestData.append(			
																	$guestDataHeader,
																	$guestDataEmailLabel,$guestDataEmail,
																	$guestDataLastNameLabel,$guestDataLastName,
																	$guestDataFirstNameLabel,$guestDataFirstName,
																	$guestDataBornNameLabel,$guestDataBornName,
																	$guestDataMotherNameLabel,$guestDataMotherName,
																	$guestDataBornDateLabel,$guestDataBornDate,
																	$guestDataGenderLabel,$guestDataGender,
																	$guestDataNationalityLabel,$guestDataNationality,
																	$guestDataPIDLabel,$guestDataPID,
																	$guestDataPIDTypeLabel,$guestDataPIDType,
																	$guestDataPhoneLabel,$guestDataPhone,
																	$guestDataAutoLabel,$guestDataAuto,
																	$guestDataZIPLabel,$guestDataZIP,
																	$guestDataCityLabel,$guestDataCity,
																	$guestDataAddressLabel,$guestDataAddress,
																	$guestDataERNameLabel,$guestDataERName,
																	$guestDataERPhoneLabel,$guestDataERPhone
																	);
										$guestDataContainer.append($guestData);
									}								
								}
								$(this).after($guestDataContainer);
								
								$('.guest-data-phone, .guest-data-er-phone').mask("99/999-9999");
								$('.guest-data-born-date').mask("9999-99-99");

								$('.guest-data-email').each(function() {
									
								    $(this).rules('add', {
								        required: true,
								        email: true
								    });
								});

								$('.guest-data-phone, .guest-data-er-phone').each(function() {
									
								    $(this).rules('add', {
								        required: true,
								        phone: true
								    });
								});

								$('.guest-data-firstname, .guest-data-lastname, ' +
									'.guest-data-mothername, .guest-data-born-date,' + 
									' .guest-data-pid, .guest-data-pid-type, .guest-data-phone,' + 
									' .guest-data-zip, .guest-data-city, .guest-data-address,' +
									' .guest-data-er-name, .guest-data-er-phone').each(function() {
									
								    $(this).rules('add', {
								        required: true
								    });
								});

								$('.guest-data-zip').blur(function(event){
								if ( $(this).val() != '' ) {
									var $this = $(this);
									$.get('php/get_city_from_zip.php',{'zip':$(this).val()}, function(data){
										var x = $this;
										var target = event.target;
										$(target).parent().find('.guest-data-city').val(data);
									});		
								}


							});

							});
							
							$('#resend_competition_registration_email').click(function(){
								get_content(linked_to + "Entry" + instanceOf,"&action=reSend&competition_id="+$(this).attr("competition_id"),0);

							});

							$('#sign-up-btn').click(function(){
								//if ($('#registration-form').valid()) {
									$('#registration-form').submit();
								//}

							});

							jQuery.validator.addMethod('phone', function (phone, element) {
									phone = phone.replace(/\s+/g, '');
									return this.optional(element) || phone.length > 9 &&
										  phone.match(/^\(?[\d\s]{2}\/[\d\s]{3}-[\d\s]{4}$/);
							},  $.validator.messages.failed_phone);

							jQuery.validator.addMethod('email', function (email, element) {
									var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    								return re.test(String(email).toLowerCase());
									
							},  $.validator.messages.email);

							if ($('#registration-form').length) 
							{
								$('#registration-form').validate({
									rules: {
										firstname: {
											required: true
										},
										lastname: {
											required: true
										},
										pid: {
											required: true
										},
										mother_name: {
											required: true
										},
										er_name: {
											required: true
										},
										'mailing-zip': {
											required: true
										},
										'mailing-city': {
											required: true
										},
										email: {
											required: true,
											email: true,
											remote: {
										        url: "php/main.php",
										        type: "get",
										        data: {
											        	menu: "UsersAdd",
											        	action: "checkEmailInCompetititon",
														email: function() {
												            return $( "#email" ).val();
												        },
												        competition_id: function() {
												            return $( "#competitionID" ).val();
												        }
													}
										        }
										},
										
										phone: {
											required: false,
											phone:true
										},
										er_phone: {
											required: false,
											phone:true
										}
							            
									},
									messages: {
							                email: {
							                   	remote: $.validator.messages.exist_email
							                }
							        },
									invalidHandler: function(event, validator) {
												
									},
									submitHandler: function() {	
										if($("#accept_rules").is(':checked'))
										{
											var data = $('#registration-form').serializeArray();
											data.push({name: "action", value: "addEntry"});
											get_content(linked_to + "Entry" + instanceOf,$.param(data),0);
										}
									}
								});
							}

							

							if ($('#add-team-form').length) 
							{
								$('#add-team-form').validate({
									rules: {
										team_name: {
											required: true,
											remote: {
										        url: "php/main.php",
										        type: "get",
										        data: {
											        	menu: "Entry",
											        	action: "checkTeamNameExists",
														team_name: function() {
												            return $( "#team_name" ).val();
												        },
												        competition_id: $("#competitionID").val()
													}
										        }
										},
										"teamates[]": {
											required:  function(element){
												return element.getAttribute('id') == 'teamate1';
											},
											remote: {
										        url: "php/main.php",
										        type: "get",
										        data: {
											        	menu: "Entry",
											        	action: "checkTeamateValidity",
														teamate_code: $('[name="teamate_code"]').val(),
												        competition_id: $( "#competitionID" ).val()
													}
										        }
										}
							            
									},
									messages: {
							                "teamates[]": {
							                   	remote: $.validator.messages.failed_teamamte
							                },
							                team_name: {
							                   	remote: $.validator.messages.failed_team_reg
							                }
							        },
									invalidHandler: function(event, validator) {
												
									},
									submitHandler: function() {	
										var data = $('#registration-form').serializeArray();
										data.push({name: "action", value: "addEntry"});
										get_content(linked_to + "Entry" + instanceOf,$.param(data),0);
									}
								});
							}
							
							

							$('#show-add-team-form-btn').click(function(e){
								e.preventDefault();
								
								$('#add-team-form').slideDown(
									function(){
										$('#content-container').animate({scrollTop: 1000}, 2000);
										$('#team-name').selectRange(0,0);
									}
								);								
							});

							$('#delete-team-btn').click(function(e){
								e.preventDefault();
								
								$('#add-team-form').slideUp();
								var data = $('#add-team-form').serializeArray();
								data.push({name: "action", value: "deleteTeam"});
								data.push({name: "teamID", value: $('#team_name').attr('team_id')});
								get_content(linked_to + "Entry" + instanceOf,$.param(data),0);
								$('name:teamates').val('');
								$('#team_name').val('');
								$('#show-add-team-form-btn').removeAttr('disabled').toggleClass('blue1');
							});

							if ($('#team_name').val() != '') 
							{
								$('#add-team-form').show();
							}

							$('#add-team-btn').click(function(e){
								e.preventDefault();
								if ( $('#add-team-form').valid())
								{
									var data = $('#add-team-form').serializeArray();
									data.push({name: "action", value: "addTeam"});
									get_content(linked_to + "Entry" + instanceOf,$.param(data),0);	
								}
								
							});
						break;

						case 'ShowChangeLanguage':
							
							location.reload(true);
						break;
					}
				break;

				case 'nav':
					menu = start_page.length ? start_page : 'Home';
					$('nav #menu-box').html(data.content);
					$(".tile[menu*=UploadManagerOfGallery" + "]").each(function(){
				        $(this).attr("menu",$(this).attr("menu").replace("UploadManagerOf",""));
				    });

				    $(".tile[menu*=UploadManagerOfFileHandler" + "]").each(function(){
				        $(this).attr("menu",$(this).attr("menu").replace("UploadManagerOfFileHandler","Documents"));
				    });
			
					if ( $('#show-login-btn').length)
					{
						$('#show-login-btn').remove();
						$('#login-container').remove();
					}
					$('nav').append(data.login_container);
					$('#password').keypress(function(event){
						
						var keycode = (event.keyCode ? event.keyCode : event.which);
						if (keycode == '13') $("#login-btn").trigger('click');
					});
					var param = username!='' ? '&username='+username : '';
					 	param = plang!='' ? param+'&lang='+plang : param;
					 	param = hash!='' ? param+'&hash='+hash : param;
						param = comp_reg_id!='' ? param+'&comp_reg_id='+comp_reg_id : param;
						param = action!='' ? param+'&action='+action : param;
						param = competitionID!='' ? param+'&competitionID='+competitionID : param;
						param = teamID!='' ? param+'&teamID='+teamID : param;
					get_content(menu,param,0, 'main');

				break;

				
				case 'login-container':
					switch (page){
						case 'showLogin':
							
							if (data.message == 'failed_login' || data.message == 'username_required')
							{
								$("#login-message").html($.validator.messages[data.message]);
								$('#login-message').show();
							}
							else
							{
								$('#login-container').slideUp({},{duration: 200, queue:false});
								$('#show-login-btn').remove();
								$('#menu-box').after(data.logout);
								if ($('#hamburger-menu').is(':visible')) 
								{
									$('#logout-container').show();
								}
							}
						break;
						case 'showForgotPassword':
							$('#login-container').slideUp();
							showMessage(data.message);
						break;
					}
				break;
			}
			
		});
	}
}

$(document).ready(function(){
	
	"use strict";

	/* TARGET Animation start */
	/*
	(function ($) { 
			$.BG_Map = function (element) {
				
		        this.element = (element instanceof $) ? element : $(element);
		    };
		       
		    $.BG_Map.prototype = {
		    	
		        InitEvents: function () {
		            var that = this;
		            var $target_ = $('<div>', {'class': 'city_marker_img'});

		            $(document).keydown(function (e) {
		                var key = e.which;
		                if (key == 39) {
		                    that.moveRight();
		                } else if (key == 37) {
		                    that.moveLeft();
		                }
		            });

		            this.element.css({
		                width: '100%',
		                minHeight: '100%',
		                margin: '0',
		                padding: '0',
		                backgroundImage : "url('img/map.jpg')",
		                backgroundSize: '110% auto',
		                backgroundRepeat: 'no-repeat',
		                backgroundPosition: '0px 0px'
		            });

		            $target_.css({
		            	width: '100%',
		                height:'100%',
		                margin: '0',
		                padding: '0',
		                backgroundImage : "url('img/target.png')",
		                backgroundSize: '110% auto',
		                backgroundRepeat: 'no-repeat',
		                backgroundPosition: '0px 0px',
		                position: 'absolute',
		                zIndex: '0'

		            });
	           		this.element.append($target_);
	           		this.place_marker();  
		        },
		        
		        place_marker: function () {
		        	var that = this;
		        	var backgroundPos = this.element.css('backgroundPosition').split(" ");
		        	var xPos = backgroundPos[0], yPos = backgroundPos[1];
		        	var city_koords = [
		        						//{"name": "Szombathely", "x": 55.052, "y": 42.151},
		        						{"name": "Celldömölk", "x": 58.437, "y": 42.658},
			        					{"name": "Pápa", "x": 60.572, "y": 40.886},
										//{"name": "Sárvár", "x": 57.083, "y": 41.645},
										//{"name": "Kőszeg", "x": 54.531, "y": 39.113}	
									];

		        	xPos = parseInt(xPos.replace("px",""));
		        	yPos = parseInt(yPos.replace("px",""));

	                var target = {	
	        					'top' : that.element.width() * 1.1 * (790 / 1920) * (36.32911 / 100) - 5, 
	        					'left' : that.element.width() * 1.1 * (53.28125 / 100) -5 
        					};
	        		
		            $('.city_marker').remove();
		            $('.target_marker').remove();
		            $.each(city_koords, function(i, city){

		            	var $marker  = $('<span>', {'class': 'city_marker'});
		            	var $target  = $('<span>', {'class': 'target_marker'});
		            	var $city_name = $("<span>");

		            	$marker.css({
			            	width:'10px',
			            	height:'10px',
			            	backgroundColor: '#bbb',
			            	borderRadius: '50%',
			            	display: 'block',
			            	cursor: 'pointer',
			            	position: 'absolute',
			            	zIndex:'20'
			            });

			            $target.css({
			            	width:'10px',
			            	height:'10px',
			            	backgroundColor: '#fd0000',
			            	borderRadius: '50%',
			            	display: 'block',
			            	position: 'absolute',
			            	zIndex:'30'
			            });
			           

			            $city_name.css({
							display: 'block',
							margin: '-8px',
							color: 'white',
			            	fontSize: '0.2em',
			            	textAlign: 'center'
			            });

		            	$marker.css({
			            	left: xPos + that.element.width() * 1.1 * (city.x / 100) + 'px',
			            	top: yPos + that.element.width() * 1.1 * (790 / 1920) * (city.y / 100) + 'px'
			            });

			           	$city_name.html(city.name);
			            $marker.append($city_name);
			            that.element.append($marker);

			            $target.css({
			            	left: target.left + 'px',
			            	top: target.top + 'px'
			            });
			            that.element.append($target);

		            });

		            $(".city_marker").click(function(e){
		            	var city_marker = $(this).position();

					    that.element.animate({
						  'background-position-x': target.left - city_marker.left + xPos  + 'px',
						  'background-position-y': target.top - city_marker.top + yPos + 'px'
						}, 
						{
					    	duration: 500,
					    	specialEasing: {
					     	width: "linear",
					     	height: "easeOutBounce"
					    },
						progress: function() {
						      that.place_marker();
						    }
						});

					});
		        },
		        moveLeft: function () {
		            this.element.css("left", '-=' + 10);
		        }
    		};

		    $.BG_Map.defaultOptions = {
		        playerX: 0,
		        playerY: 0
		    };

		}(jQuery));

		var bg_map = new $.BG_Map($("#wrapper"));
			bg_map.InitEvents();
			
		$(window).resize(function(){
			bg_map.InitEvents();
			location.reload();
		});	
	*/
	/* TARGET Animation end */

	if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
		$('#box-container').hide();
	}

	var start_page = window.location.hash.replace('#','');
	get_content('LoadMenuItems','&start_page='+start_page,1,'nav');

	if($('#information-box .content').html())
	{
		$('#information-box').animate({left:"-=178"}, 100); 
		//$('#information-box').show("slide", { direction: "left" }, 100);
		get_content('CheckOpenCompetitions','',1,'information-box');
	}

	
	$('body').on('click', '.tile', function(e) {	

		if ($(this).hasClass("selected") && $( "#content-container" ).is(":visible"))
		{
			$( "#content-container" ).slideUp( "slow");
			$('.tile').removeClass('selected');	
		}
		else
		{
			$('.tile').removeClass('selected');		
			$('#content-container').fadeOut(800);
			$(this).addClass("selected");
			get_content($(this).attr('menu'),'',0);
			$('#content-container').css({"background-image":""});
			if ( $('#hamburger-menu').is(':visible') && 
				!$(".menu-container[menu=" + $(this).attr('menu') + "]").length && 
				$(this).attr('menu') != "up") {
				$('#menu-box').slideUp("slow");
				if ($('#logout-container').length)
				{
					$('#logout-container').toggle();
				}
				else $('#show-login-btn').toggle();
			}
		}
	});

	$('body').on('click', '.open-close-container', function(e) {
		if ($('#information-box .open-close').hasClass('close'))
		{
			$('#information-box .open-close').removeClass('close');
			$('#information-box .open-close').addClass('open');
			$('#information-box').animate({left:"-=178"}, 100); 
		}
		else
		{
			$('#information-box .open-close').removeClass('open');
			$('#information-box .open-close').addClass('close');
			$('#information-box').animate({left:"+=178"}, 100); 
		}
		
	});
	
	$('body').on('click', '#message', function(e) {	
		$(this).slideUp({	}, { duration: 200, queue: false});
	});

	$('body').on('click', '#register-btn', function(e) {	
		get_content('UsersAdd','',0,'main');
		$('#content-container').css({"background-image":""});	
		if ( $('#hamburger-menu').is(':visible') ) 
		{
			$('#menu-box').slideUp("slow");
			if ($('#logout-container').length)
			{
				$('#logout-container').toggle();
			}
			else $('#show-login-btn').toggle();
		}
	});

	$('body').on('click', '#show-login-btn', function(e) {
		if (!$('.logout-btn').length)
		{
			$('#login-container').slideToggle({},{duration: 200, queue:false});
			$('#login-message').hide();
		}
	});

	$('body').on('click', '#setting-menu-btn', function(e) {
		if (!$('.login-btn').length)
		{
			$('#settings-container').slideToggle({},{duration: 200, queue:false});
		}
	});
	
	$('body').on('click', '.flag', function(e) {
		$('#lang').val($(this).attr('id'));
		$('#content-container').css({"background-image":""});
		var lang = $(this).attr('id');

		var start_page = window.location.hash.replace('#','');
		get_content('ChangeLanguage','lang='+lang,1);

	});

	$('body').on('click', '#login-btn', function(e) {
		if ($('#username').val() != '')
		{
			get_content('Login','username='+$('#username').val()+'&password='+$('#password').val(),1,'login-container');
		}
		else
		{
			$("#login-message").html($.validator.messages["username_required"]);
			$('#login-message').show();
		}
	});

	$('body').on('click', '#forgot-pw-btn', function(e) {

		$('#content-container').css({"background-image":""});
		if ($('#username').val() != '')
		{
			get_content('ForgotPassword','username='+$('#username').val(),1,'login-container');
		}
		else
		{
			$("#login-message").html($.validator.messages["username_required"]);
			$('#login-message').show();
		}
		
	});

	$('body').on('click', '#user-settings', function(e) {	
		$('#content-container').css({"background-image":""});
		get_content('UsersEdit','action=showRegisterForm',0,'main');	
		$('#settings-container').hide();
	});

	$('body').on('click', '#hamburger-menu', function(e) {	
		$('#menu-box').slideToggle('slow');
		/*if ($('#logout-container').length)
		{
			$('#logout-container').toggle();
		}
		else $('#show-login-btn').toggle();*/
	});

	$('body').on('click', '.logout-btn', function(e) {	
		$.confirm({
						title: $.validator.messages["confirm_logout_title"],
						boxWidth: 'calc(1.2rem + 50vw)',
						useBootstrap: false,
						content: $.validator.messages["confirm_logout_content"],	
						buttons: {
							confirm: {
								text: $.validator.messages["msg_confirm"],	
								action: function(){
									$.get("php/main.php",{ menu:"logout" }, function(data) {
										location.reload(true);
									});
									if ( $('#hamburger-menu').is(':visible') ) {
										$('#show-login-btn').show();
									}	
									$('#settings-container');											
								}
							},
							cancel: {
								text: $.validator.messages["msg_cancel"],	

							}
						}
				});
	});
});