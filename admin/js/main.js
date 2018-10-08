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
	$.post("php/main.php",{ menu:"login" }, function(data) {

		data = jQuery.parseJSON(data);
		if(data.state == 'login')
		{
			$('main').html(data.content);
			
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

						$.post("php/main.php",data, function(data) {
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
			$('main').html("");

			var menu = window.location.hash.replace('#','');
			menu = menu.length?menu:'Home';
			get_content(menu,'',0);
			$('.logout').remove();
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
	if ( msg.indexOf("empty") >= 0 ) 
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

function get_title_path(menu, title)
{
	title = " / " + "<span class='title-path' menu='" + menu + "'>" + $(".tile[menu=" + menu + "]").find('.caption').html() + "</span>" + title;
	if(menu == "Home")
	{
		return title;
	}

	return get_title_path($(".tile[menu=" + menu + "]").parents('.menu-container').attr("menu"), title);

}

function show_title(menu)
{
	var title = ''; 
	$('header').html("<span class='title'>" + $('.title').html() + "</span>" + get_title_path(menu, title ) );
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

function get_content(menu,param,isHistory)
{
	
	menu = (menu == 'up')?$(".tile[menu=" + $(".menu-container:visible").attr("menu") + "]").parents(".menu-container").attr("menu"):menu;
	var linked_to = getLinkedToFromMenu(menu);
	var instanceOf = getInstanceNameFromMenu(menu);

	var upload_root_folder = $(".tile[menu=" + menu + "]").attr("upload_root_folder");	
	var url = 'php/main.php?target=main&upload_root_folder=' + 
	(typeof upload_root_folder=='undefined'?'':upload_root_folder) + '&linked_to=' + linked_to + '&instanceOf=' + instanceOf;
	show_menu(menu);


	$('#message').hide();

	switch (menu)
			{	
				case linked_to + 'Home' + instanceOf:	
					url = url + '&page=showHome';
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
	// *                             Versenykezelés                                 *
	// ******************************************************************************
				case linked_to + 'Entry' + instanceOf:
					url = url + '&page=showEntry';
				break;

				case linked_to + 'AddCompetition' + instanceOf:
					url = url + '&page=showAddCompetition';
				break;
				case linked_to + 'ModifyCompetitionBtn' + instanceOf:
				case linked_to + 'AddCompetitionBtn' + instanceOf:
					if($('#competitionForm').valid())
					{
						$('#competitionForm').submit();
					}
					return;
				break;

				case linked_to + 'ModifyCompetition' + instanceOf:
					url = url + '&page=showModifyCompetition';
				break;

				case linked_to + 'DeleteCompetition' + instanceOf:
					url = url + '&page=showDeleteCompetition';
				break;

				case linked_to + 'DeleteCompetitionBtn' + instanceOf:
					$.confirm({
								title: 'Törlés',
								boxWidth: '30%',
								useBootstrap: false,
								content: 'Biztosan törli a versenyt?',	
								buttons: {
									confirm: {
										text: 'Igen',
										action: function(){
											get_content(linked_to + "DeleteCompetition" + instanceOf,"competitionID="+$("#competition-combobox").val()+"&method=delete",1);											
										}
									},
									cancel: {
										text: 'Mégsem'
									}
								}
						});
					
					return;
				break;
				


	// ******************************************************************************
	// *                             Hírek kezelése                                 *
	// ******************************************************************************
				
				case linked_to + 'News' + instanceOf:	
					url = url + '&page=showNewsHome';
				break;

				case linked_to + 'NewsAdd' + instanceOf:
				case linked_to + 'NewsModifyMeta' + instanceOf:
				case linked_to + 'NewsDeleteMeta' + instanceOf:										
				case linked_to + 'NewsMeta' + instanceOf:		
					$('main').children().hide();
					if (!$('#newsMetaForm').length) url += '&page=showNewsMeta&action=create';
					else $('#newsMetaForm').parent().show();
				break;

				case linked_to + 'NewsDeleteContent' + instanceOf:
				case linked_to + 'NewsModify' + instanceOf:
				case linked_to + 'NewsWrite' + instanceOf:
					url += '&page=showNewsWrite&newsID='+$('#news-item').val();
				break;	

				case linked_to + 'NewsModifySave' + instanceOf:
				case linked_to + 'NewsSave' + instanceOf:
					
					$('main').children().hide();
					$('#newsMetaForm').parent().show();

					if(!$('#newsMetaForm').length)
					{
						if (menu.toLowerCase().indexOf('save'))	showMessage("empty_news_meta_data");
						else showMessage("empty_news_assign"); 
						menu = menu == linked_to + 'NewsSave' + instanceOf?linked_to + 'NewsMeta' + instanceOf:linked_to + 'NewsModifyMeta' + instanceOf;
						url += '&page=showNewsMeta&action=create';
					}
					else if(!$('#newsMetaForm').valid())
					{
						showMessage("empty_news_meta_data");
						$('#newsMetaForm').parent().show();
						return;
					}
					else if(!$('#newsEditorTabs').length )
					{
						switch (menu)
						{
							case linked_to + 'NewsModifySave' + instanceOf:
								menu = linked_to + 'NewsModify' + instanceOf;
								url += '&page=showNewsWriteBeforeSave&newsID='+$('#news-item').val();
							break;
							case linked_to + 'NewsSave' + instanceOf:
								menu = linked_to + 'NewsWrite' + instanceOf;
								url += '&page=showNewsWrite';
							break;
						}
					}
					else
					{
						var data = $('#newsMetaForm').serializeArray();
						var lang;
						for(name in CKEDITOR.instances)
						{
							lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}

						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						$("#documentForm .document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showNewsSave';
						param += $.param(data); 
					}
						
				break;
				
				case linked_to + 'NewsDeleteItem' + instanceOf:

					if(!$('#newsMetaForm').length)
					{
						showMessage("empty_news_assign");
					}
					else if(!$('#newsMetaForm').valid())
					{
						showMessage("empty_news_meta_data");
						$('#newsMetaForm').parent().show();
						return;
					}
					else if(!$('#news-item').val())
					{
						showMessage("empty_news_assign");
					}
					else
					{
						$.confirm({
								title: 'Törlés',
								boxWidth: '30%',
								useBootstrap: false,
								content: 'Biztosan törli a hírt?',	
								buttons: {
									confirm: {
										text: 'Igen',
										action: function(){
											get_content(linked_to + 'NewsDeleteItemConfirm' + instanceOf,'&newsID='+$('#news-item').val(),0);											
										}
									},
									cancel: {
										text: 'Mégsem'
									}
								}
						});
						return;
					}
				break;

				case linked_to + 'NewsDeleteItemConfirm' + instanceOf:
					menu = linked_to + 'NewsDeleteItem' + instanceOf;
					url += '&page=showNewsDeleteConfirm' + instanceOf + '&document_linked_to=' + linked_to 
						+'&document_instance_of=OfNews';
				break;

				case linked_to + 'NewsEdit' + instanceOf:
					menu = linked_to + 'NewsSearch' + instanceOf;
					url = url + '&page=showNewsSearch' + instanceOf;
				break;

				case linked_to + 'NewsDelete' + instanceOf:
				case linked_to + 'NewsDeleteSearch' + instanceOf:
					url = url + '&page=showNewsDeleteSearch';
				break;

				case linked_to + 'NewsSearch' + instanceOf:
					url = url + '&page=showNewsSearch';
				break;

	// ******************************************************************************
	// *                     Dokumentumok csatolása                                 *
	// ******************************************************************************

				case linked_to + 'DocumentEditAttachment' + instanceOf:
				case linked_to + 'DocumentAttachment' + instanceOf:
					var instance_id = '0';
					if (typeof ($('#news-item').val() != 'undefined') && $('#news-item').val()) instance_id = $('#news-item').val();

					$('main').children().hide();
					if (!$('#documentForm').length) url += '&page=showDocumentAttachment&action=create&instance_id='+instance_id;
					else $('#documentForm').show();
				break;

				case linked_to + 'FolderDeleteForm' + instanceOf:
					url = url + '&page=showUploadManagerDeleteFolders';
				break;

				case linked_to + 'FolderDeleteBtn' + instanceOf:
	
					if ( $('.delete_selector:checked').length > 0 ){
						$.confirm({
							title: 'Törlés',
							boxWidth: '30%',
							useBootstrap: false,
							content: 'Biztosan törli az foldero(ka)t?',	
							buttons: {
								confirm: {
									text: 'Igen',
									action: function(){
										var data = $('#folderListForm').serializeArray();
										
										get_content(linked_to + 'FolderDeleteConfirmed' + instanceOf,$.param(data),1);
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

	// ******************************************************************************
	// *                             Rólunk                                         *
	// ******************************************************************************

				case linked_to + 'AboutUsWrite' + instanceOf:
				case linked_to + 'AboutUs' + instanceOf:
					url = url + '&page=showAboutUs';
				break;

				case linked_to + 'AboutUsSaveBtn' + instanceOf:
					if($('#aboutusEditorTabs').length)
					{
						var data = [];
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "aboutus_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm .document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showAboutUs';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'AboutUs' + instanceOf;
						url = url + '&page=showAboutUs';
					}
				break;

				case linked_to + 'AboutUsDelete' + instanceOf:
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
											data.push({name: "aboutus_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'AboutUsDeleteConfirmed' + instanceOf,$.param(data),1);
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

				case linked_to + 'AboutUsDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'AboutUsDelete' + instanceOf;
					url = url + '&page=showAboutUs';
				break;

	// ******************************************************************************
	// *                      Versenyszabályok menü                                 *
	// ******************************************************************************

				case linked_to + 'RulesWrite' + instanceOf:
				case linked_to + 'Rules' + instanceOf:
					url = url + '&page=showRules';
				break;

				case linked_to + 'RulesSaveBtn' + instanceOf:
					if($('#rulesEditorTabs').length)
					{
						var data = [];
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "rules_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm .document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showRules';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'Rules' + instanceOf;
						url = url + '&page=showRules';
					}
				break;

				case linked_to + 'RulesDelete' + instanceOf:
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
											data.push({name: "rules_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'RulesDeleteConfirmed' + instanceOf,$.param(data),1);
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

				case linked_to + 'RulesDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'RulesDelete' + instanceOf;
					url = url + '&page=showRules';
				break;

	// ******************************************************************************
	// *                             Kapcsolat menü                                 *
	// ******************************************************************************


				case linked_to + 'ContactsWrite' + instanceOf:
				case linked_to + 'Contacts' + instanceOf:
					url = url + '&page=showContacts';
				break;

				case linked_to + 'ContactsSaveBtn' + instanceOf:

					if($('#contactsEditorTabs').length)
					{

						var data = [];
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "contacts_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm .document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showContacts';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'Contacts' + instanceOf;
						url = url + '&page=showContacts';
					}
				break;

				case linked_to + 'ContactsDelete' + instanceOf:
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
											data.push({name: "contacts_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'ContactsDeleteConfirmed' + instanceOf,$.param(data),1);
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

				case linked_to + 'ContactsDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'ContactsDelete' + instanceOf;
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


				case linked_to +  'ObstacleCourse' + instanceOf:
					url = url + '&page=showHome';
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
	// *                             Versenytérkép menü                               *
	// ******************************************************************************


				case linked_to + 'CompetitionMapWrite' + instanceOf:
				case linked_to + 'CompetitionMap' + instanceOf:
					url = url + '&page=showCompetitionMap';
				break;

				case linked_to + 'CompetitionMapSaveBtn' + instanceOf:

					if($('#competitionMapEditorTabs').length && $('#competition-combobox').val()!='0')
					{

						var data = [];
						data.push({name: "competitionID", value: $('#competition-combobox').val()});
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "competition_map_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm .document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showCompetitionMap';
						param += '&'+$.param(data);
					}
					else
					{
						showMessage('empty_selected_in_list');
						return ;
						
					}
				break;

				case linked_to + 'CompetitionMapDelete' + instanceOf:
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
											data.push({name: "competition_map_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'CompetitionMapDeleteConfirmed' + instanceOf,$.param(data),1);
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

				case linked_to + 'CompetitionMapDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'CompetitionMapDelete' + instanceOf;
					url = url + '&page=showCompetitionMap';
				break;

	// ******************************************************************************
	// *                             Versenyinfó menü                               *
	// ******************************************************************************


				case linked_to + 'CompetitionInfoWrite' + instanceOf:
				case linked_to + 'CompetitionInfo' + instanceOf:
					url = url + '&page=showCompetitionInfo';
				break;

				case linked_to + 'CompetitionInfoSaveBtn' + instanceOf:

					if($('#competitionInfoEditorTabs').length && $('#competition-combobox').val()!='0')
					{

						var data = [];
						data.push({name: "competitionID", value: $('#competition-combobox').val()});
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "information_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm .document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showCompetitionInfo';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'CompetitionInfo' + instanceOf;
						url = url + '&page=showCompetitionInfo';
					}
				break;

				case linked_to + 'CompetitionInfoDelete' + instanceOf:
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
											data.push({name: "information_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'CompetitionInfoDeleteConfirmed' + instanceOf,$.param(data),1);
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

				case linked_to + 'CompetitionInfoDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'CompetitionInfoDelete' + instanceOf;
					url = url + '&page=showCompetitionInfo';
				break;


	// ******************************************************************************
	// *                         Verseny megközelíthetőség menü                     *
	// ******************************************************************************

				case linked_to + 'CompetitionApproachWrite' + instanceOf:
				case linked_to + 'CompetitionApproach' + instanceOf:
					url = url + '&page=showCompetitionApproach';
				break;

				case linked_to + 'CompetitionApproachSaveBtn' + instanceOf:

					if($('#competitionApproachEditorTabs').length && $('#competition-combobox').val()!='0')
					{

						var data = [];
						data.push({name: "competitionID", value: $('#competition-combobox').val()});
						data.push({name: "document_linked_to", value: $("#documentForm").attr("linked_to")});
						data.push({name: "document_instance_of", value: $("#documentForm").attr("instance_of")});
						
						for(name in CKEDITOR.instances)
						{
							var lang = name.substr(name.indexOf('-') + 1);
							data.push({name: "competition_approach_content[" + lang + "]", value: CKEDITOR.instances[name].getData()});
						}
											

						$("#documentForm .document_attachment:checked").each(function(){
						    data.push({name: "file_id[]", value: $(this).attr('file_id')});
						});

						url += '&page=showCompetitionApproach';
						param += '&'+$.param(data);
					}
					else
					{
						menu =  linked_to + 'CompetitionApproach' + instanceOf;
						url = url + '&page=showCompetitionApproach';
					}
				break;

				case linked_to + 'CompetitionApproachDelete' + instanceOf:
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
											data.push({name: "information_id", value: $("#time-combobox").val()});
											get_content(linked_to + 'CompetitionApproachDeleteConfirmed' + instanceOf,$.param(data),1);
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

				case linked_to + 'CompetitionApproachDeleteConfirmed' + instanceOf:
					menu =  linked_to + 'CompetitionApproachDelete' + instanceOf;
					url = url + '&page=showCompetitionApproach';
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
		

	if ($("div[menu=" + menu + "]").length)
	{	
		show_title(menu);
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
	var sPageURL = url.split('?');
	if(url !== "")
	{	
		var waiting = $.confirm({
					icon: 'loading',
				    theme: 'supervan',
				    boxWidth: '30%',
				    lazyOpen: true,
					useBootstrap: false,
				    title: 'Betöltés',
				    content: '',
				    closeIcon: false
				});

		waiting.open();
		
		$.post(sPageURL[0],sPageURL[1], function(data) {
			waiting.close();
			var page = getUrlParameter('page',url);
			var target = getUrlParameter('target',url);
			var linked_to = getUrlParameter('linked_to',url);
			var instanceOf = getUrlParameter('instanceOf',url);
			
			data = jQuery.parseJSON(data);
		
			if(data.message) { showMessage(data.message, data.ontop); }
			if(data.ontop) { goTop(); }

			for(name in CKEDITOR.instances)
			{
			    CKEDITOR.instances[name].destroy(true);
			}

			switch (target) //target switching
			{
				case 'main':
					
					if(data.content && data.append) { $('main').append(data.content); }	
					else if(data.content) { $('main').html(data.content); }			
					
					switch (page) //page switching
					{	
						case 'showLogin':
						
						break;

						case 'showHome':
							$('main').html(data);
						break;

						case 'showMsr':
							$('main').html(data);
						break;

						case 'showUsersInfo':
							
						break;

						case 'showUsersAdd':
					
							$('#phone').mask("99/999-9999");

							jQuery.validator.addMethod('phone', function (phone, element) {
									phone = phone.replace(/\s+/g, '');
									return this.optional(element) || phone.length > 9 &&
										  phone.match(/^\(?[\d\s]{2}\/[\d\s]{3}-[\d\s]{4}$/);
							},  $.validator.messages.failed_phone);


							$("#userForm").validate({
									rules: {
										firstname: {
											required: true
										},
										lastname: {
											required: true
										},
										username: {
											required: true,
											minlength:6,
											remote: {
										        url: "php/main.php",
										        type: "post",
										        data: {
											        	menu: "UsersAdd",
											        	action: "checkUserExists",
														username: function() {
												            return $( "#username" ).val();
												          }
													}
										        }
										      
										},
										password: {
											required: true,
											minlength:8
										},
										confirm_password: {
											required: true,
											minlength: 8,
											equalTo: "#password"
										},
										phone: {
											required: false,
											phone:true
										}
							            
									},
									messages: {
							                username: {
							                   	remote: $.validator.messages.exist_username
							                }
							            },
									invalidHandler: function(event, validator) {
												
									},
									submitHandler: function() {	
										var data = $('#userForm').serializeArray();
										data.push({name: "action", value: "addUser"});
										get_content("UsersAdd",$.param(data),0);
									}
								});
						break;

						case 'showUsersEdit':

							$("#users-combobox-data").change(function(e){
								get_content("UsersEdit","userID=" + $("option:selected", this).attr("user_id"),0);
								e.preventDefault();
							});

							$('#phone').mask("99/999-9999");
							
							jQuery.validator.addMethod('phone', function (phone, element) {
									phone = phone.replace(/\s+/g, '');
									return this.optional(element) || phone.length > 9 &&
										  phone.match(/^\(?[\d\s]{2}\/[\d\s]{3}-[\d\s]{4}$/);
							},  $.validator.messages.failed_phone);

							$("#userForm").validate({
									rules: {
										firstname: {
											required: true
										},
										lastname: {
											required: true
										},
										username: {
											required: true,
											minlength:6,
											remote: {
										        url: "php/main.php",
										        type: "post",
										        data: {
										        		userID: $("option:selected", "#users-combobox-data").attr("user_id"),
											        	menu: "UsersEdit",
											        	action: "checkUserExists",
														username: function() {
												            return $( "#username" ).val();
												          }
													}
										        }
										      
										},
										password: {
											required: true,
											minlength:8
										},
										confirm_password: {
											required: true,
											minlength: 8,
											equalTo: "#password"
										},
										phone: {
											required: false,
											phone:true
										}  
									},
									messages: {
							                username: {
							                   	remote: $.validator.messages.exist_username
							                }
							            },
									invalidHandler: function(event, validator) {
												
									},
									submitHandler: function(e) {	
										var data = $('#userForm').serializeArray();
										data.push({name: "userID", value: $("option:selected", "#users-combobox-data").attr("user_id")});
										data.push({name: "action", value: "editUser"});
										get_content("UsersEdit",$.param(data),0);
									}
								});

						break;

						case 'showUsersDelete':

							$("#users-combobox-data").change(function(e){
								get_content("UsersDelete","userID=" + $("option:selected", this).attr("user_id"),0);
								e.preventDefault();
							});

							$( "#userForm" ).submit(function( e ) {
								e.preventDefault();	
								if($("option:selected", "#users-combobox-data").val())
								{	
									$.confirm({
												title: 'Törlés',
												boxWidth: '30%',
												useBootstrap: false,
												content: 'Biztosan törli a felhasználót?',	
												buttons: {
													confirm: {
														text: 'Igen',
														action: function(){
															get_content("UsersDelete","userID=" + $("option:selected", "#users-combobox-data").attr("user_id") + "&delete=1",0);
															e.preventDefault();												
														}
													},
													cancel: {
														text: 'Mégsem'
		
													}
												}
									});
								}
								else
								{
									showMessage('empty_user');
								}
							});

						break;

						case 'showUsersAuthority':

							$("#users-combobox-authority").change(function(e){
								get_content("UsersAuthority","userID=" + $("option:selected", this).attr("user_id"),0);
								e.preventDefault();
							});

							$('.users-authority').change(function(e) {
								get_content("UsersAuthority",
											"userID=" + $("option:selected","#users-combobox-authority").attr("user_id") + 
											"&menu_name=" + $(this).attr("menu") + 
											"&menuID=" + $(this).attr("menu_id") +
											"&authority=" + $(this).is(":checked"),0);
						        e.preventDefault();       
						    });

						break;	

						case 'showNewsHome':
						break;

						case 'showNewsWriteBeforeSave':
						case 'showNewsWrite':
							$('main').children().hide();
							if(!$('#newsEditorTabs').length)
							{
								var $newsEditorTabs = $('<div>', {'id': 'newsEditorTabs'});
								$('main').append($newsEditorTabs);
								var tabTitle = "<ul>";
								$.each(data.languages, function(key, name){
									tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
								});
								tabTitle += "</ul>";
								$newsEditorTabs.append(tabTitle);

								$.each(data.languages, function(key, name){
									if (!$('#newsEditor-' + key).length)
									{
										var $aTab = $('<div>', {'id': key});
										var $newsEditor = $('<textarea>', {'id': 'newsEditor-' + key, 'class': 'editor'});								
										$newsEditorTabs.append($newsEditor);
										CKEDITOR.replace( 'newsEditor-' + key, {
											extraPlugins: 'autogrow',
											extraPlugins: 'imageuploader',
											autoGrow_minHeight: 300,
											autoGrow_maxHeight: 600,
											autoGrow_bottomSpace: 50,
											language: 'hu'
										} );
										$aTab.append($newsEditor);
										$newsEditorTabs.append($aTab);
									}
								});
								$newsEditorTabs.tabs();
							}
							else
							{
								$('#newsEditorTabs').show();
							}
							if(data.news_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("newsEditor-" + key)].setData(data.news_content[key]);
								});
							}
							
							if(page == 'showNewsWriteBeforeSave')
							{
								get_content(linked_to + 'NewsModifySave' + instanceOf,'',0);
							}

						break;

						case 'showNewsSave':
							$("#newsMetaForm")[0].reset();
							$.each(data.languages, function(key, name){	
									CKEDITOR.instances[("newsEditor-" + key)].setData('');
							});	
						break;

						case 'showNewsDeleteConfirm':
							$('main').html('');
						break;

						case 'showNewsMeta':
							$( '#newsMetaTabs' ).tabs();
							
							$.each(data.languages, function(key, name){
								$( "#visiblity-" + key ).checkboxradio();
								if($("#main_visiblity-" + key ).attr('type') != 'hidden')
								{
									$( "#main_visiblity-" + key ).checkboxradio();															
								}
							});
							

							$( ".start_date, .end_date" ).datepicker({
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

							$("#newsMetaForm").validate({
									rules: {
										"title[hu]": {
											required: true
										},
										"author[hu]": {
											required: true
										},
										"start_date[hu]": {
											required: true								      
										},
										"end_date[hu]": {
											required: true
										}							            
									},
									
									submitHandler: function(e) {										
										e.preventDefault();
										get_content(linked_to + "NewsSave" + instanceOf,'',0);
									}
								});

						break;
						
						case 'showNewsDeleteSearch':
							$( "#visiblity" ).checkboxradio();

							$( "#newsSearchForm" ).submit(function( e ) {
								var data = $('#newsSearchForm').serializeArray();
								data.push({name: "action", value: "showNewsResult"});
								e.preventDefault();
								get_content(linked_to + "NewsDeleteSearch" + instanceOf,$.param(data),0);
							});
							
							$( ".news-item" ).click(function() {
							  	get_content(linked_to + "NewsDeleteMeta" + instanceOf,"newsid=" + $(this).attr("newsid"),0);
							});
						break;

						case 'showNewsSearch':

							$( "#visiblity" ).checkboxradio();

							$( "#newsSearchForm" ).submit(function( e ) {
								var data = $('#newsSearchForm').serializeArray();
								data.push({name: "action", value: "showNewsResult"});
								e.preventDefault();
								get_content(linked_to + "NewsSearch" + instanceOf,$.param(data),1);
								
							});
							
							$( ".news-item" ).click(function(e) {
								get_content(linked_to + "NewsModifyMeta" + instanceOf,"newsid=" + $(this).attr("newsid"),0);
							});

						break;

						case 'showDocumentAttachment':
						break;

						case 'showUploadManagerFolderList':
							if(!data.content) $("main").html("");

							$('.folder-item-container').click(function(){
								var data = [{name: "id", value: $(this).attr("id")}];
								data.push({name: "action", value:"modifyFolderForm" });
								get_content(linked_to + "FolderModify" + instanceOf,$.param(data),0);
							});

						case 'showUploadManagerAddFolderForm':
							$( '#add_folder_tab' ).tabs();
							$.each(data.languages, function(key, name)
							{
								$( "#visiblity_"+key).checkboxradio();
							});	

							$("#add_folder_frm").validate({
									ignore: [],
									rules: {"folder_name[hu]": "required"},

									submitHandler: function() {	
										var data = $('#add_folder_frm').serializeArray();
										if($('#method_type').val() == 'add')
										{
											data.push({name: "action", value: "addFolder"});
											get_content(linked_to + "FolderAdd" + instanceOf,$.param(data),0);
										}
										else if($('#method_type').val() == 'update')
										{
											data.push({name: "action", value: "modifySaveFolder"});
											get_content(linked_to + "FolderModifyForm" + instanceOf,$.param(data),0);
										}
									},
									invalidHandler: function(){
										$( '#add_folder_tab' ).tabs({ active: 0 });
									}
							});

							if ($('#method_type').val() == 'add' && data.message && data.message.indexOf("success") != -1)
							{
								$("#add_folder_frm")[0].reset();
							}
	

						break;

						case 'showUploadManagerEditFolderForm':
							$( '#folderDataTabs' ).tabs();
							
							$.each(data.languages, function(key, name){
								$( "#visiblity_"+key+", #unvisiblity_"+ key ).checkboxradio();
							});

							$("#edit_folder_frm").validate({
									rules: {
										folder_name: "required"
									},
									submitHandler: function() {	
										var data = $('#edit_folder_frm').serializeArray();
										data.push({name: "action", value: "modifyFolder"});
										get_content(linked_to + "FolderModify" + instanceOf,$.param(data),0);
									}
							});

						break;

						case 'showUploadManagerDeleteFolders':
							if(!data.content) $("main").html("");
							$('#toggle_all').click(function(){
							
								if ( $(this).prop('checked') )
								{
									$('.delete_selector').prop('checked', true);
									$('#folder_container span').text('Összes kijelölést eltávolít');
								} 
								else{
									$('.delete_selector').prop('checked', false);
									$('#folder_container span').text('Összes kijelöl');
								} 
							});

							$('.delete_selector').click(function(e){
								e.stopPropagation();
							});

							$('.folder-item-container').click(function(e){
								$(this).find('.delete_selector').trigger('click');
							});

						break;

						case 'showUploadManagerFileModify':
							$( "#folder_selector" ).selectmenu();	
							$( "#fileSubtitleTab").tabs();
							$("#fileSubtitleForm").validate({
									ignore: [],
									rules: {
												"file_subtitle[hu]": {
														required: true
												}
											},

									submitHandler: function() {	
										var data = $('#fileSubtitleForm').serializeArray();

										data.push({name: "action", value: "subtitle-modify"});
										get_content(linked_to + "FileModifyForm" + instanceOf,$.param(data),0);
									},
									invalidHandler: function(){
										$( '#fileSubtitleForm' ).tabs({ active: 0 });
									}
							});

							$.each(data.languages, function(key, name){
								$( "#visiblity-" + key ).checkboxradio();
							});

							if($('#folder_selector').val() != '-1')
							{
								$('#file_cont').show();
								
								$('.subtitle-modify-form-btn').on('click',function(e){
									e.preventDefault(); 
									var data = [{name: "folderId", value: $(this).attr("folder_id")}];
									data.push({name: "fileId", value: $(this).attr("file_id")});
									data.push({name: "action", value: "subtitle-modify-form"});

									get_content(linked_to + "FileModifyForm" + instanceOf,$.param(data),0);
								});
							}
							else
							{
								$('#file_cont').hide();
							}

							$('#folder_selector').on('selectmenuchange',function(e){
								e.preventDefault();
								var data = [{name: "folderId", value: $('#folder_selector').val()}];
								data.push({name:"action", value:"upload-form-dir"});
								get_content(linked_to + "FileModifyForm" + instanceOf,$.param(data),0);	
							});

						break;

						case 'showUploadManagerFileUpload':

							$( "#folder_selector" ).selectmenu();	
							$( "#fileSubtitleTab").tabs();

							$("#fileSubtitleForm").validate({
									ignore: [],
									rules: {
												"file_subtitle[hu]": {
														required: true
												}
											},

									submitHandler: function() {	
										var data = $('#fileSubtitleForm').serializeArray();

										data.push({name: "action", value: "subtitle-modify"});
										get_content(linked_to + "FileUploadForm" + instanceOf,$.param(data),0);
									},
									invalidHandler: function(){
										$( '#fileSubtitleForm' ).tabs({ active: 0 });
									}
							});

							$.each(data.languages, function(key, name){
								$( "#visiblity-" + key ).checkboxradio();
							});

							if($('#folder_selector').val() != '-1')
							{
								$('#file_cont').show();

								$dz = $("#file_cont").dropzone({ 
									url: "php/main.php?target=main&menu=" + linked_to + "FileUploadBtn" + instanceOf +
										"&page=pictUploadFinished&action=upload-form-dir&linked_to=" + linked_to + "&instanceOf=" + instanceOf, 
									addRemoveLinks: true,
									maxFilesize: 10,
									clickable: ["#file_cont"],
									autoProcessQueue: false,
									parallelUploads: 15,
								    complete: function (file) {
								        get_content(linked_to + "FileUploadForm"  + instanceOf,"action=upload-form-dir&folderId="+$('#folder_selector option:selected').val(),1);
								    },
									init: function() {
									   this.on('success', function(file, response){
							           		$('.dz-remove').hide();
							           });
									}
								});

								$('div[menu="' + linked_to + 'FileUploadBtn' + instanceOf + '"').click(function(e){
									e.preventDefault(); 
									$dz[0].dropzone.options.url += '&folderId='+$('#folder_selector option:selected').val();
									if ( $('#folder_selector option:selected').val() != "none") $dz[0].dropzone.processQueue();

								});

								$('.subtitle-modify-form-btn').on('click',function(e){
									e.preventDefault(); 
									var data = [{name: "folderId", value: $(this).attr("folder_id")}];
									data.push({name: "fileId", value: $(this).attr("file_id")});
									data.push({name: "action", value: "subtitle-modify-form"});

									get_content(linked_to + "FileUploadForm" + instanceOf,$.param(data),0);
								});
							}
							else
							{
								$('#file_cont').hide();
							}

							$('#folder_selector').on('selectmenuchange',function(e){
								e.preventDefault();

								var data = [{name: "folderId", value: $('#folder_selector').val()}];
								data.push({name:"action", value:"upload-form-dir"});
								get_content(linked_to + "FileUploadForm" + instanceOf,$.param(data),0);	
							});

						break;

						case 'showUploadManagerDeleteFiles':

							if($('#folder_selector').val() != '-1')
							{
								$('#file_cont').show();
							}
							else
							{
								$('#file_cont').hide();
							}

							if ( $('.file_selector:checked').length > 0 )
							{
								$('.file_selector:checked').remove();
							}
							
							$('#toggle_all').click(function(){
							
								if ( $(this).prop('checked') )
								{
									$('.file_selector').prop('checked', true);
									$('#folder_container span').text('Összes kijelölést eltávolít');
								} 
								else{
									$('.file_selector').prop('checked', false);
									$('#folder_container span').text('Összes kijelöl');
								} 
							});
							
							$('.file_selector').click(function(e){
								e.stopPropagation();
							});

							$('.dz-image-preview').click(function(e){
								$(this).find('.file_selector').trigger('click');
							});

							$( "#folder_selector" ).selectmenu();

							$('#folder_selector').on('selectmenuchange',function(){
								var data = [{name: "folderId", value: $('#folder_selector').val()}];
								get_content(linked_to + "FileDeleteForm" + instanceOf,$.param(data),0);
							});							
						break;

						case 'showAboutUs':

							if (!data.aboutus_combobox.length) return;
							$('main').empty();
							
							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.aboutus_combobox);
							}
							else
							{
								$('#time-combobox-container').html(data.aboutus_combobox);
							}

							$("#time-combobox").change(function(e){
									get_content(linked_to + "AboutUsSaveBtn" + instanceOf,"action=set_other&aboutus_id=" + $("option:selected", this).val(),1);	
									e.preventDefault();
								});

							
							if(!$('#EditorTabs').length)
							{
								var $aboutusEditorTabs = $('<div>', {'id': 'aboutusEditorTabs'});

								$('main').append($aboutusEditorTabs);
								var tabTitle = "<ul>";
								$.each(data.languages, function(key, name){
									tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
								});
								tabTitle += "</ul>";
								$aboutusEditorTabs.append(tabTitle);

								$.each(data.languages, function(key, name){
									if (!$('#aboutusEditor-' + key).length)
									{
										var $aTab = $('<div>', {'id': key});
										var $aboutusEditor = $('<textarea>', {'id': 'aboutusEditor-' + key, 'class': 'editor'});								
										$aboutusEditorTabs.append($aboutusEditor);
										CKEDITOR.replace( 'aboutusEditor-' + key, {
											extraPlugins: 'autogrow',
											extraPlugins: 'imageuploader',
											autoGrow_minHeight: 300,
											autoGrow_maxHeight: 600,
											autoGrow_bottomSpace: 50,
											language: 'hu'
										} );
										
										$aTab.append($aboutusEditor);
										$aboutusEditorTabs.append($aTab);
									}
								});
								$aboutusEditorTabs.tabs();
							}
							else
							{
								$('#aboutusEditorTabs').show();
							}
							if(data.aboutus_content)
							{
						
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("aboutusEditor-" + key)].setData(data.aboutus_content[key]);
								});
							}
						break;

						case 'showRules':
							if (!data.rules_combobox.length) return;
							$('main').empty();

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.rules_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.rules_combobox);
							}

							$("#time-combobox").change(function(e){
									get_content(linked_to + "RulesSaveBtn" + instanceOf,"action=set_other&rules_id=" + $("option:selected", this).val(),1);	
									e.preventDefault();
								});

							if(!$('#rulesEditorTabs').length)
							{
								var $rulesEditorTabs = $('<div>', {'id': 'rulesEditorTabs'});
								$('main').append($rulesEditorTabs);
								var tabTitle = "<ul>";
								$.each(data.languages, function(key, name){
									tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
								});
								tabTitle += "</ul>";
								$rulesEditorTabs.append(tabTitle);

								$.each(data.languages, function(key, name){
									if (!$('#rulesEditor-' + key).length)
									{
										var $aTab = $('<div>', {'id': key});
										var $rulesEditor = $('<textarea>', {'id': 'rulesEditor-' + key, 'class': 'editor'});								
										$rulesEditorTabs.append($rulesEditor);
										CKEDITOR.replace( 'rulesEditor-' + key, {
											extraPlugins: 'autogrow',
											extraPlugins: 'imageuploader',
											autoGrow_minHeight: 300,
											autoGrow_maxHeight: 600,
											autoGrow_bottomSpace: 50,
											language: 'hu'
										} );
										
										$aTab.append($rulesEditor);
										$rulesEditorTabs.append($aTab);
									}
								});
								$rulesEditorTabs.tabs();
							}
							else
							{
								$('#rulesEditorTabs').show();
							}

							if(data.rules_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("rulesEditor-" + key)].setData(data.rules_content[key]);
								});
							}
						break;

						case 'showContacts':
							if (!data.contacts_combobox.length) return;
							$('main').empty();

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.contacts_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.contacts_combobox);
							}

							$("#time-combobox").change(function(e){
									get_content(linked_to + "ContactsSaveBtn" + instanceOf,"action=set_other&contacts_id=" + $("option:selected", this).val(),1);	
									e.preventDefault();
								});

							if(!$('#contactsEditorTabs').length)
							{
								var $contactsEditorTabs = $('<div>', {'id': 'contactsEditorTabs'});
								$('main').append($contactsEditorTabs);
								var tabTitle = "<ul>";
								$.each(data.languages, function(key, name){
									tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
								});
								tabTitle += "</ul>";
								$contactsEditorTabs.append(tabTitle);

								$.each(data.languages, function(key, name){
									if (!$('#contactsEditor-' + key).length)
									{
										var $aTab = $('<div>', {'id': key});
										var $contactsEditor = $('<textarea>', {'id': 'contactsEditor-' + key, 'class': 'editor'});								
										$contactsEditorTabs.append($contactsEditor);
										CKEDITOR.replace( 'contactsEditor-' + key, {
											extraPlugins: 'autogrow',
											extraPlugins: 'imageuploader',
											autoGrow_minHeight: 300,
											autoGrow_maxHeight: 600,
											autoGrow_bottomSpace: 50,
											language: 'hu'
										} );
										
										$aTab.append($contactsEditor);
										$contactsEditorTabs.append($aTab);
									}
								});
								$contactsEditorTabs.tabs();
							}
							else
							{
								$('#contactsEditorTabs').show();
							}

							if(data.contacts_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("contactsEditor-" + key)].setData(data.contacts_content[key]);
								});
							}
						break;

						case 'showStory':
							if (!data.story_combobox.length) return;
							$('main').empty();

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
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
								$('main').append($storyEditorTabs);
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
							$('main').empty();

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
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
								$('main').append($obstaclesEditorTabs);
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
							$('main').empty();

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
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
								$('main').append($fieldDescriptionEditorTabs);
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
							if (!data.information_combobox.length) return;
							$('main').empty();

							if (data.competition_combobox.length)
							{
								$('main').append("<div id='combobox-container'>Verseny választása</div>");
								$('#combobox-container').append(data.competition_combobox);
							}

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.information_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.information_combobox);
							}

							$("#time-combobox").change(function(e){
								get_content(linked_to + "CompetitionInfoSaveBtn" + instanceOf,"action=set_other&information_id=" + $("option:selected", this).val(),1);	
								e.preventDefault();
							});

							
							var $competitionInfoEditorTabs = $('<div>', {'id': 'competitionInfoEditorTabs'});
							$('main').append($competitionInfoEditorTabs);
							var tabTitle = "<ul>";
							$.each(data.languages, function(key, name){
								tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
							});
							tabTitle += "</ul>";
							$competitionInfoEditorTabs.append(tabTitle);

							$.each(data.languages, function(key, name){
								if (!$('#competitionInfoEditor-' + key).length)
								{
									var $aTab = $('<div>', {'id': key});
									var $competitionInfoEditor = $('<textarea>', {'id': 'competitionInfoEditor-' + key, 'class': 'editor'});								
									$competitionInfoEditorTabs.append($competitionInfoEditor);
									CKEDITOR.replace( 'competitionInfoEditor-' + key, {
										extraPlugins: 'autogrow',
										extraPlugins: 'imageuploader',
										autoGrow_minHeight: 300,
										autoGrow_maxHeight: 600,
										autoGrow_bottomSpace: 50,
										language: 'hu'
									} );
									
									$aTab.append($competitionInfoEditor);
									$competitionInfoEditorTabs.append($aTab);
								}
							});
							$competitionInfoEditorTabs.tabs();
							
							
							if(data.information_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("competitionInfoEditor-" + key)].setData(data.information_content[key]);
								});
							}
						break;

						case 'showCompetitionMap':

							if (!data.competition_map_combobox.length) return;

							$('main').empty();

							if (data.competition_combobox.length)
							{
								$('main').append("<div id='combobox-container'>Verseny választása</div>");
								$('#combobox-container').append(data.competition_combobox);
							}

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.competition_map_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.competition_map_combobox);
							}

							$("#time-combobox").change(function(e){
								get_content(linked_to + "CompetitionMapSaveBtn" + instanceOf,"action=set_other&competition_map_id=" + $("option:selected", this).val(),1);	
								e.preventDefault();
							});

							
							var $competitionMapEditorTabs = $('<div>', {'id': 'competitionMapEditorTabs'});
							$('main').append($competitionMapEditorTabs);
							var tabTitle = "<ul>";
							$.each(data.languages, function(key, name){
								tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
							});
							tabTitle += "</ul>";
							$competitionMapEditorTabs.append(tabTitle);

							$.each(data.languages, function(key, name){
								if (!$('#competitionMapEditor-' + key).length)
								{
									var $aTab = $('<div>', {'id': key});
									var $competitionMapEditor = $('<textarea>', {'id': 'competitionMapEditor-' + key, 'class': 'editor'});								
									$competitionMapEditorTabs.append($competitionMapEditor);
									CKEDITOR.replace( 'competitionMapEditor-' + key, {
										extraPlugins: 'autogrow',
										extraPlugins: 'imageuploader',
										autoGrow_minHeight: 300,
										autoGrow_maxHeight: 600,
										autoGrow_bottomSpace: 50,
										language: 'hu'
									} );
									
									$aTab.append($competitionMapEditor);
									$competitionMapEditorTabs.append($aTab);
								}
							});
							$competitionMapEditorTabs.tabs();
							
						
							if(data.competition_map_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("competitionMapEditor-" + key)].setData(data.competition_map_content[key]);
								});
							}
						break;

						case 'showEntry':

							$("#competition-combobox").change(function(e){

								$('.entry-table, #modify_accepted').remove();
								if($(this).val())
								{
									$('main').append("<button id='modify_accepted' type='button' >Mentés és levélküldés</button>");
									$('main').append("<table class='entry-table'>"+
												"<thead>" +
													"<tr>" +
														"<th>Csapatnév</th>" +
														"<th>Reg. kód</th>" +
														"<th>Vezetéknév</th>" +
														"<th>Keresztnév</th>" +
														"<th>Szül.</th>" +
														"<th>Anyja neve</th>" +
														"<th>Távolság</th>" +
														"<th>Póló</th>" +
														"<th>Telefon</th>" +
														"<th>Email</th>" +
														"<th>Neme</th>" +
														"<th>SZIG</th>" +
														"<th>ER neve</th>" +
														"<th>ER telefon</th>" +
														"<th>Elfogadva</th>" +
														"<th>Visszavonás</th>" +
														"</tr>" +
												"</thead>" +
												"<tbody>" +
												"</tbody>" +
											   "</table>");
									$(".odt-top").html("");
									$(".odt-pagination-container").html("");
									$(".entry-table").OpenDataTable({  
										url:"../admin/php/competition_entry_list.php",
										param: $(this).val(),
										callback: function(data) {
												var myTable = $(".entry-table").tableExport();
												myTable.update({
													filename: $(".competition-combobox option:selected").text(),
													formats: ["xlsx", "csv", "txt"],
													
													exportButtons: true,
													position: "bottom",
													ignoreCols: [14,15] 
												});
												
										  		if(!data.num_rec)
										  		{
										  			$('#modify_accepted').remove();

										  		}
										  		$('.delete-entry').click(function(e){
													var that = $(this);
													$.confirm({
															title: 'Visszavonás',
															boxWidth: '30%',
															useBootstrap: false,
															content: 'Biztosan visszavonja a nevezést?',	
															buttons: {
																confirm: {
																	text: 'Igen',
																	action: function(){
																		var data = [];
																		data.push({name: "action", value: "delete"});
																		data.push({name: "competitionRegID", value: that.attr("competitionregid")});
																		get_content(linked_to + "Entry" + instanceOf,$.param(data),1);											
																	}
																},
																cancel: {
																	text: 'Mégsem'

																}
															}
													});
												});
										}
									});

									


									$('#modify_accepted').click(function(e){
										$.confirm({
												title: 'Mentés',
												boxWidth: '30%',
												useBootstrap: false,
												content: 'Biztosan menti és kiküldi a leveleket?',	
												buttons: {
													confirm: {
														text: 'Igen',
														action: function(){
															var data = [];
															data.push({name: "action", value: "accept"});
															$('.accepted:checked').each(function(){
																data.push({name: "accepted[]", value: $(this).val()});
															});
															get_content(linked_to + "Entry" + instanceOf,$.param(data),1);											
														}
													},
													cancel: {
														text: 'Mégsem'

													}
												}
										});

									});
								}

							});


							
						break;

						case 'showCompetitionApproach':
							if (!data.competition_approach_combobox.length) return;
							$('main').empty();

							if (data.competition_combobox.length)
							{
								$('main').append("<div id='combobox-container'>Verseny választása</div>");
								$('#combobox-container').append(data.competition_combobox);
							}

							if (!$('#time-combobox').length) 
							{
								$('main').append("<div id='time-combobox-container'></div>");
								$('#time-combobox-container').html(data.competition_approach_combobox);

							}
							else
							{
								$('#time-combobox-container').html(data.competition_approach_combobox);
							}

							$("#time-combobox").change(function(e){
								get_content(linked_to + "CompetitionApproachSaveBtn" + instanceOf,"action=set_other&competition_approach_id=" + $("option:selected", this).val(),1);	
								e.preventDefault();
							});

							
							var $competitionApproachEditorTabs = $('<div>', {'id': 'competitionApproachEditorTabs'});
							$('main').append($competitionApproachEditorTabs);
							var tabTitle = "<ul>";
							$.each(data.languages, function(key, name){
								tabTitle += "<li><a href='#" + key + "'>" + name + "</a></li>";
							});
							tabTitle += "</ul>";
							$competitionApproachEditorTabs.append(tabTitle);

							$.each(data.languages, function(key, name){
								if (!$('#competitionApproachEditor-' + key).length)
								{
									var $aTab = $('<div>', {'id': key});
									var $competitionApproachEditor = $('<textarea>', {'id': 'competitionApproachEditor-' + key, 'class': 'editor'});								
									$competitionApproachEditorTabs.append($competitionApproachEditor);
									CKEDITOR.replace( 'competitionApproachEditor-' + key, {
										extraPlugins: 'autogrow',
										extraPlugins: 'imageuploader',
										autoGrow_minHeight: 300,
										autoGrow_maxHeight: 600,
										autoGrow_bottomSpace: 50,
										language: 'hu'
									} );
									
									$aTab.append($competitionApproachEditor);
									$competitionApproachEditorTabs.append($aTab);
								}
							});
							$competitionApproachEditorTabs.tabs();
							
							

							if(data.competition_approach_content)
							{
								$.each(data.languages, function(key, name){	
										CKEDITOR.instances[("competitionApproachEditor-" + key)].setData(data.competition_approach_content[key]);
								});
							}
						break;
						case 'showModifyCompetition':
							
							$("#start_hour").mask("99");
							$("#start_minute").mask("99");
                                                        $("#teamate_number").mask("9");
							$( "#comp_dist_1" ).checkboxradio();
							$( "#comp_dist_2" ).checkboxradio();
							$( "#comp_dist_3" ).checkboxradio();

							$("#competition-combobox").change(function(e){
								get_content(linked_to + "ModifyCompetition" + instanceOf,"competitionID=" + $("option:selected", this).attr("competition_id"),0);
								e.preventDefault();
							});

							$( "#start_date, #reg_start_date, #reg_end_date" ).datepicker({
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
					
							$("#competitionForm").validate({
									rules: {
										"start_date": {
											required: true
										},
										"start_hour": {
											required: true,
											number: true,
											range: [0,59]
										},
										"start_minute": {
											required: true,
											number: true,
											range: [0,59]
										},
										"reg_start_date": {
											required: true
										},
										"reg_end_date": {
											required: true								      
										},
                                                                                "teamate_number": {
											required: true,
											number: true,
											range: [0,9]
										},
										"max_reg_number": {
											required: true,
											number: true
										}					            
									},
									
									submitHandler: function() {		
										var data = $('#competitionForm').serializeArray();
										data.push({name: "method", value: "save"});							
										get_content(linked_to + "ModifyCompetition" + instanceOf,$.param(data),1);
									}
								});
						break;
						case 'showDeleteCompetition':
                                                        $( "#comp_dist_1" ).checkboxradio();
							$( "#comp_dist_2" ).checkboxradio();
							$( "#comp_dist_3" ).checkboxradio();
							
							$("#competition-combobox").change(function(e){
								get_content(linked_to + "DeleteCompetition" + instanceOf,"competitionID=" + $("option:selected", this).attr("competition_id"),0);
								e.preventDefault();
							});

							
						break;	
						case 'showAddCompetition':
							$("#start_hour").mask("99");
							$("#start_minute").mask("99");
                                                        $("#teamate_number").mask("9");
							$( "#comp_dist_1" ).checkboxradio();
							$( "#comp_dist_2" ).checkboxradio();
							$( "#comp_dist_3" ).checkboxradio();
							$( "#start_date, #reg_start_date, #reg_end_date" ).datepicker({
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
						
							$("#competitionForm").validate({
									rules: {
										"start_date": {
											required: true
										},
										"start_hour": {
											required: true,
											number: true,
											range: [0,59]
										},
										"start_minute": {
											required: true,
											number: true,
											range: [0,59]
										},
                                                                                "teamate_number": {
											required: true,
											number: true,
											range: [0,9]
										},
										"reg_start_date": {
											required: true
										},
										"reg_end_date": {
											required: true								      
										},
										"max_reg_number": {
											required: true,
											number: true
										}						            
									},
									
									submitHandler: function() {		
										var data = $('#competitionForm').serializeArray();
										data.push({name: "method", value: "save"});							
										get_content(linked_to + "AddCompetition" + instanceOf,$.param(data),1);
									}
								});
						break;
					}
				break;
			}
			
		});

	}
}



$(document).ready(function(){
	
	"use strict";

	initPage();

	$('body').on('click', '.tile,  .title-path', function(e) {	
		get_content($(this).attr('menu'),'',0);
	});

	$('body').on('click', '#message', function(e) {	
		$(this).slideUp({	}, { duration: 200, queue: false});
	});


	$('body').on('click', '.logout', function(e) {	
		$.confirm({
						title: 'Kijelentkezés',
						boxWidth: '30%',
						useBootstrap: false,
						content: 'Biztosan ki szeretne jelentkezni?',	
						buttons: {
							confirm: {
								text: 'Igen',
								action: function(){
									$.post("php/main.php",{ menu:"logout" }, function(data) {
										location.reload(true);
									});											
								}
							},
							cancel: {
								text: 'Mégsem'

							}
						}
				});
	});
});