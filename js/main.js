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
			$('#content').html(data.content);
			
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
			$('#content').html("");

			var menu = window.location.hash.replace('#','');
			menu = menu.length?menu:'Home';
			get_content(menu,'',0);
			$('#logout-container').remove();
			$('#show-login-btn').remove();
			$('header').before(data.logout);
			$(".logout").slideDown({	}, { duration: 200, queue: false});
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

	if ( $('.banner-image').length ) 
		$('.banner-image').after(title);
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


				case linked_to + 'StoryWrite' + instanceOf:
				case linked_to + 'Story' + instanceOf:
					url = url + '&page=showStory';
				break;

				case linked_to + 'StorySaveBtn' + instanceOf:

					if($('#storyEditorTabs').length)
					{

						var data = [];
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "story_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm.document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showStory';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'Story' + instanceOf;
						url = url + '&page=showStory';
					}
				break;

				case linked_to + 'StoryDelete' + instanceOf:
					if($("#time-combobox").length && $("#time-combobox").val() != '-1')
					{
						$.confirm({
								title: 'Törlés',
								boxWidth: '30%',
								useBootstrap: false,
								content: 'Biztosan törli a bejegyzést?',	
								buttons: {
									confirm: {
										text: 'Igen',
										action: function(){
											var data = [];
											data.push({name: "story_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'StoryDeleteConfirmed' + instanceOf,$.param(data),1);
										}
									},
									cancel: {
										text: 'Mégsem',
										
									}
								}
							});
					} else showMessage('empty_selected_in_list');
					return;
				break;

				case linked_to + 'StoryDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'StoryDelete' + instanceOf;
					url = url + '&page=showStory';
				break;

	// ******************************************************************************
	// *                             Akadályok menü                               *
	// ******************************************************************************


				case linked_to + 'CompetitionObstaclesWrite' + instanceOf:
				case linked_to + 'CompetitionObstacles' + instanceOf:
					url = url + '&page=showCompetitionObstacles';
				break;

				case linked_to + 'CompetitionObstaclesSaveBtn' + instanceOf:

					if($('#obstaclesEditorTabs').length)
					{

						var data = [];
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "obstacles_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm.document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showCompetitionObstacles';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'CompetitionObstacles' + instanceOf;
						url = url + '&page=showCompetitionObstacles';
					}
				break;

				case linked_to + 'CompetitionObstaclesDelete' + instanceOf:
					if($("#time-combobox").length && $("#time-combobox").val() != '-1')
					{
						$.confirm({
								title: 'Törlés',
								boxWidth: '30%',
								useBootstrap: false,
								content: 'Biztosan törli a bejegyzést?',	
								buttons: {
									confirm: {
										text: 'Igen',
										action: function(){
											var data = [];
											data.push({name: "obstacles_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'CompetitionObstaclesDeleteConfirmed' + instanceOf,$.param(data),1);
										}
									},
									cancel: {
										text: 'Mégsem',
										
									}
								}
							});
					} else showMessage('empty_selected_in_list');
					return;
				break;

				case linked_to + 'CompetitionObstaclesDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'CompetitionObstaclesDelete' + instanceOf;
					url = url + '&page=showCompetitionObstacles';
				break;


	// ******************************************************************************
	// *                             Pályavázlat menü                               *
	// ******************************************************************************


				case linked_to + 'CompetitionFieldDescriptionWrite' + instanceOf:
				case linked_to + 'CompetitionFieldDescription' + instanceOf:
					url = url + '&page=showCompetitionFieldDescription';
				break;

				case linked_to + 'CompetitionFieldDescriptionSaveBtn' + instanceOf:

					if($('#fieldDescriptionEditorTabs').length)
					{

						var data = [];
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "field_description_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm.document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showCompetitionFieldDescription';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'CompetitionFieldDescription' + instanceOf;
						url = url + '&page=showCompetitionFieldDescription';
					}
				break;

				case linked_to + 'CompetitionFieldDescriptionDelete' + instanceOf:
					if($("#time-combobox").length && $("#time-combobox").val() != '-1')
					{
						$.confirm({
								title: 'Törlés',
								boxWidth: '30%',
								useBootstrap: false,
								content: 'Biztosan törli a bejegyzést?',	
								buttons: {
									confirm: {
										text: 'Igen',
										action: function(){
											var data = [];
											data.push({name: "field_description_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'CompetitionFieldDescriptionDeleteConfirmed' + instanceOf,$.param(data),1);
										}
									},
									cancel: {
										text: 'Mégsem',
										
									}
								}
							});
					} else showMessage('empty_selected_in_list');
					return;
				break;

				case linked_to + 'CompetitionFieldDescriptionDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'CompetitionFieldDescriptionDelete' + instanceOf;
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


	// ******************************************************************************
	// *                             Mappakezelés                                   *
	// ******************************************************************************

				case linked_to + 'FolderDeleteConfirmed' + instanceOf:
					menu = linked_to +'FolderDeleteBtn' + instanceOf;
					url = url + '&page=showUploadManagerDeleteFolders';
				break;

				
				case linked_to + 'Folders' + instanceOf:
					menu = linked_to + 'FolderAdd' + instanceOf;
				case linked_to + 'FolderAdd' + instanceOf:
					url = url + '&page=showUploadManagerAddFolderForm';
				break;

				case linked_to + 'FolderModifyForm' + instanceOf:
					url = url + '&page=showUploadManagerFolderList';
				break;

				case linked_to + 'FolderModify' + instanceOf:
					menu = linked_to + 'FolderModifyForm' + instanceOf;
					url = url + '&page=showUploadManagerAddFolderForm';
				break;


	// ******************************************************************************
	// *                             Fájlkezelés                                    *
	// ******************************************************************************

				case linked_to + 'Files' + instanceOf:
					menu = linked_to + 'FileUploadForm' + instanceOf;				
				case linked_to + 'FileUploadForm'+ instanceOf:
					url += '&page=showUploadManagerFileUpload';
				break;

				case linked_to + 'FileModifyForm' + instanceOf:
					url += '&page=showUploadManagerFileModify';
				break;
				
				case linked_to + 'FileUploadBtn' + instanceOf:
					return;
				break;

				case linked_to + 'FileDeleteForm' + instanceOf:
					url = url + '&page=showUploadManagerDeleteFiles';
				break;

				case linked_to + 'FileDeleteBtn' + instanceOf:
					if ( $('.file_selector:checked').length > 0 ){
						$.confirm({
							title: 'Törlés',
							boxWidth: '30%',
							useBootstrap: false,
							content: 'Biztosan törli a képe(ke)t?',	
							buttons: {
								confirm: {
									text: 'Igen',
									action: function(){
										var data = $('#fileListForm').serializeArray();
										data.push({name:'folderId', value: $('#folder_selector').val()});
										
										get_content(linked_to + 'FileDeleteConfirmed' + instanceOf,$.param(data),1);
									}
								},
								cancel: {
									text: 'Mégsem',
									
								}
							}
						});
					}						
					return;
				break;

				case linked_to + 'FileDeleteConfirmed' + instanceOf:
					menu = linked_to + 'FileDeleteBtn' + instanceOf;
					url = url + '&page=showUploadManagerDeleteFiles';
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
							});
						break;

					}
				break;

				case 'main':
					
					if(data.content && data.append) { $('#content').append(data.content); }	
					else if(data.content) 
					{ 
							$('#content').html(data.content);
							show_article_title(menu);
							if (data.document) 
							{
								$('#content').append('<div id="right-side-bar"><span>'+$.validator.messages["attached_documents"]+'</span>'+data.document+'</div>')	
								if ($('#hamburger-menu').is(':visible')) $('.sub-content').width('90%');
								else $('.sub-content').width('70%');
							}

							$('#content').slideDown();
					}	

					if (data.news_search_form)
					{
						$('#content .sub-content').prepend(data.news_search_form);
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
								$('#content').slideUp();
							}
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

						case 'showDocuments':

						break;

						case 'showGallery':
							
							$(".album-container" ).click(function(e) {
								get_content(linked_to + "GalleryAlbum" + instanceOf,"albumID="+$(this).attr("albumID"),0);
								e.preventDefault(); 						 		
							});
							$('#content').css({"background-image":"url(img/common/military-wallpaper.jpg"});
						break;
						case 'showGalleryAlbum':
							

							$('#content').empty();
							$('#content').html(data.content);
							
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
							
							$('#content').slideDown("slow");	
						break;

						case 'showStory':
							if (!data.story_combobox.length) return;
							$('#content').empty();

							if (!$('#time-combobox').length) 
							{
								$('#content').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.story_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.story_combobox);
							}

							$("#time-combobox").change(function(e){
								get_content(linked_to + "StorySaveBtn" + instanceOf,"action=set_other&story_id=" + $("option:selected", this).val(),1);	
								e.preventDefault();
							});

							if(!$('#storyEditorTabs').length)
							{
								var $storyEditorTabs = $('<div>', {'id': 'storyEditorTabs'});
								$('#content').append($storyEditorTabs);
								var tabTitle = "<ul>";
								$.each(data.languages, function(key, name){
									tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
								});
								tabTitle += "</ul>";
								$storyEditorTabs.append(tabTitle);

								$.each(data.languages, function(key, name){
									if (!$('#storyEditor-' + key).length)
									{
										var $aTab = $('<div>', {'id': key});
										var $storyEditor = $('<textarea>', {'id': 'storyEditor-' + key, 'class': 'editor'});								
										$storyEditorTabs.append($storyEditor);
										CKEDITOR.replace( 'storyEditor-' + key, {
											extraPlugins: 'autogrow',
											extraPlugins: 'imageuploader',
											autoGrow_minHeight: 300,
											autoGrow_maxHeight: 600,
											autoGrow_bottomSpace: 50,
											language: 'hu'
										} );
										
										$aTab.append($storyEditor);
										$storyEditorTabs.append($aTab);
									}
								});
								$storyEditorTabs.tabs();
							}
							else
							{
								$('#storyEditorTabs').show();
							}

							if(data.story_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("storyEditor-" + key)].setData(data.story_content[key]);
								});
							}
						break;

						case 'showCompetitionObstacles':
							if (!data.obstacles_combobox.length) return;
							$('#content').empty();

							if (!$('#time-combobox').length) 
							{
								$('#content').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.obstacles_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.obstacles_combobox);
							}

							$("#time-combobox").change(function(e){
								get_content(linked_to + "CompetitionObstaclesSaveBtn" + instanceOf,"action=set_other&obstacles_id=" + $("option:selected", this).val(),1);	
								e.preventDefault();
							});

							if(!$('#obstaclesEditorTabs').length)
							{
								var $obstaclesEditorTabs = $('<div>', {'id': 'obstaclesEditorTabs'});
								$('#content').append($obstaclesEditorTabs);
								var tabTitle = "<ul>";
								$.each(data.languages, function(key, name){
									tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
								});
								tabTitle += "</ul>";
								$obstaclesEditorTabs.append(tabTitle);

								$.each(data.languages, function(key, name){
									if (!$('#obstaclesEditor-' + key).length)
									{
										var $aTab = $('<div>', {'id': key});
										var $obstaclesEditor = $('<textarea>', {'id': 'obstaclesEditor-' + key, 'class': 'editor'});								
										$obstaclesEditorTabs.append($obstaclesEditor);
										CKEDITOR.replace( 'obstaclesEditor-' + key, {
											extraPlugins: 'autogrow',
											extraPlugins: 'imageuploader',
											autoGrow_minHeight: 300,
											autoGrow_maxHeight: 600,
											autoGrow_bottomSpace: 50,
											language: 'hu'
										} );
										
										$aTab.append($obstaclesEditor);
										$obstaclesEditorTabs.append($aTab);
									}
								});
								$obstaclesEditorTabs.tabs();
							}
							else
							{
								$('#obstaclesEditorTabs').show();
							}

							if(data.obstacles_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("obstaclesEditor-" + key)].setData(data.obstacles_content[key]);
								});
							}
						break;

						case 'showCompetitionFieldDescription':
							if (!data.field_description_combobox.length) return;
							$('#content').empty();

							if (!$('#time-combobox').length) 
							{
								$('#content').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.field_description_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.field_description_combobox);
							}

							$("#time-combobox").change(function(e){
								get_content(linked_to + "CompetitionFieldDescriptionSaveBtn" + instanceOf,"action=set_other&field_description_id=" + $("option:selected", this).val(),1);	
								e.preventDefault();
							});

							if(!$('#fieldDescriptionEditorTabs').length)
							{
								var $fieldDescriptionEditorTabs = $('<div>', {'id': 'fieldDescriptionEditorTabs'});
								$('#content').append($fieldDescriptionEditorTabs);
								var tabTitle = "<ul>";
								$.each(data.languages, function(key, name){
									tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
								});
								tabTitle += "</ul>";
								$fieldDescriptionEditorTabs.append(tabTitle);

								$.each(data.languages, function(key, name){
									if (!$('#fieldDescriptionEditor-' + key).length)
									{
										var $aTab = $('<div>', {'id': key});
										var $fieldDescriptionEditor = $('<textarea>', {'id': 'fieldDescriptionEditor-' + key, 'class': 'editor'});								
										$fieldDescriptionEditorTabs.append($fieldDescriptionEditor);
										CKEDITOR.replace( 'fieldDescriptionEditor-' + key, {
											extraPlugins: 'autogrow',
											extraPlugins: 'imageuploader',
											autoGrow_minHeight: 300,
											autoGrow_maxHeight: 600,
											autoGrow_bottomSpace: 50,
											language: 'hu'
										} );
										
										$aTab.append($fieldDescriptionEditor);
										$fieldDescriptionEditorTabs.append($aTab);
									}
								});
								$fieldDescriptionEditorTabs.tabs();
							}
							else
							{
								$('#fieldDescriptionEditorTabs').show();
							}

							if(data.field_description_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("fieldDescriptionEditor-" + key)].setData(data.field_description_content[key]);
								});
							}
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
											required: true,
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
										$('#content').animate({scrollTop: 1000}, 2000);
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
	var start_page = window.location.hash.replace('#','');
	get_content('LoadMenuItems','&start_page='+start_page,1,'nav');

	if($('#information-box .content').html())
	{
		$('#information-box').show("slide", { direction: "left" }, 100);
		get_content('CheckOpenCompetitions','',1,'information-box');
	}

	
	$('body').on('click', '.tile', function(e) {	

		if ($(this).hasClass("selected") && $( "#content" ).is(":visible"))
		{
			$( "#content" ).slideUp( "slow");
			$('.tile').removeClass('selected');	
		}
		else
		{
			$('#content').fadeOut(800);
			$(this).addClass("selected");
			get_content($(this).attr('menu'),'',0);
			$('#content').css({"background-image":""});
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

	$('body').on('click', '.open-close', function(e) {
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
		$('#content').css({"background-image":""});	
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
		if (!$('.logout').length)
		{
			$('#login-container').slideToggle({},{duration: 200, queue:false});
			$('#login-message').hide();
		}
	});
	
	$('body').on('click', '.flag', function(e) {
		$('#lang').val($(this).attr('id'));
		$('#content').css({"background-image":""});
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

		$('#content').css({"background-image":""});
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
		$('#content').css({"background-image":""});
		get_content('UsersEdit','action=showRegisterForm',0,'main');	
	});

	$('body').on('click', '#hamburger-menu', function(e) {	
		$('#menu-box').slideToggle('slow');
		if ($('#logout-container').length)
		{
			$('#logout-container').toggle();
		}
		else $('#show-login-btn').toggle();
	});

	$('body').on('click', '.logout', function(e) {	
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
								}
							},
							cancel: {
								text: $.validator.messages["msg_cancel"],	

							}
						}
				});
	});
});