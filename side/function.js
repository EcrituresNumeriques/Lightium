function pushState(e, url, title){
	e.preventDefault();
	history.pushState('', title, url);
	document.title = title;
}

$(document).ready(function(){
	$("a.pushState").on("click",function(e){
		//pushState(e, $(this).attr("href"), $(this).attr("data-title"));
	});
	//load admin popup
	$(".admin").on("click",function(){
		var thisContext = $(this);
		$("body").append('<div id="blackout"><div id="popup"><div id="closePopup">X</div></div></div>');
		if($(this).attr("id") == "newCat"){
			//New category
			$("#popup").append('<h1>'+translation.admin_newCat+'</h1>');
			$("#popup").append('<form action="/api/" method="post"></form>');
			$.post( '/api/', {action: "languages"},"json")
			.done(function( data ){
				for(i = 0;i<data.length;i++){
					if(i>0){
						$("#popup > form").append('<hr>');
					}
					$("#popup > form").append('<input type="text" value="'+data[i]+'" name="lang[]" readonly>');
					$("#popup > form").append('<input type="text" value="" name=name[] placeholder="'+translation.admin_newCatName+'">');
					$("#popup > form").append('<textarea name="description[]" placeholder="'+translation.admin_newCatDescription+'"></textarea>');
				}
				$("#popup > form").append('<input type="hidden" name="action" value="newCat">');
				$("#popup > form").append('<input type="submit" value="'+translation.admin_newCatSubmit+'">');

			})
			.fail(function(d, textStatus, error) {
            console.error("getJSON failed, status: " + textStatus + ", error: "+error);
        });
		}
		else if($(this).attr("id") == "newSubCat"){
			//New SubCategory
			$("#popup").append('<h1>'+translation.admin_newSubCat+'</h1>');
			$("#popup").append('<form action="/api/" method="post"></form>');
			$.post( '/api/', {action: "languages"},"json")
			.done(function(data,cat){
				for(i = 0;i<data.length;i++){
					if(i>0){
						$("#popup > form").append('<hr>');
					}
					$("#popup > form").append('<input type="text" value="'+data[i]+'" name="lang[]" readonly>');
					$("#popup > form").append('<input type="text" value="" name=name[] placeholder="'+translation.admin_newSubCatName+'">');
					$("#popup > form").append('<textarea name="short[]" placeholder="'+translation.admin_newSubCatShort+'"></textarea>');
					$("#popup > form").append('<textarea name="description[]" placeholder="'+translation.admin_newSubCatDescription+'"></textarea>');
				}
				$("#popup > form").append('<input type="hidden" name="cat" value="'+thisContext.data("cat")+'">');
				$("#popup > form").append('<input type="hidden" name="action" value="newSubCat">');
				$("#popup > form").append('<input type="submit" value="'+translation.admin_newSubCatSubmit+'">');

			})
			.fail(function(d, textStatus, error) {
						console.error("getJSON failed, status: " + textStatus + ", error: "+error);
				});
		}
		else if($(this).attr("id") == "newItem"){
			//New SubCategory
			if(typeof $(this).attr("data-subcat") !== "undefined" && $(this).attr("data-subcat") != ""){var thisSubCat = $(this).attr("data-subcat");}
			else{var thisSubCat = null;}
			$("#popup").append('<h1>'+translation.admin_newItem+'</h1>');
			$("#popup").append('<form action="/api/" method="post"></form>');
			$.post( '/api/', {action: "tags",lang : $(this).data("lang")},"json")
			.done(function(data,cat){
				for(i = 0;i<data.langs.length;i++){
					if(i>0){
						$("#popup > form").append('<hr>');
					}
					$("#popup > form").append('<input type="text" value="'+data.langs[i]+'" name="lang[]" readonly>');
					$("#popup > form").append('<input type="text" value="" name=name[] placeholder="'+translation.admin_newItemName+'">');
					$("#popup > form").append('<textarea name="short[]" placeholder="'+translation.admin_newItemShort+'"></textarea>');
					$("#popup > form").append('<textarea name="content[]" placeholder="'+translation.admin_newItemContent+'"></textarea>');
				}
				$("#popup > form").append('<hr>');
				for(i = 0;i<data.tags.length;i++){
					if(data.ids[i] == thisSubCat){var checked = "checked";}
					else{var checked = "";}
					$("#popup > form").append('<input type="checkbox" name="tags[]" value="'+data.ids[i]+'" id="tags'+data.ids[i]+'" '+checked+'><label for="tags'+data.ids[i]+'">'+data.tags[i]+'</label>');
				}
				$("#popup > form").append('<input type="hidden" name="action" value="newItem">');
				$("#popup > form").append('<input type="submit" value="'+translation.admin_newItemSubmit+'">');

			})
			.fail(function(d, textStatus, error) {
						console.error("getJSON failed, status: " + textStatus + ", error: "+error);
			});
		}
		else if($(this).attr("id") == "newCalendar"){
			//New SubCategory
			$("#popup").append('<h1>'+translation.admin_newCalendar+'</h1>');
			$("#popup").append('<form action="/api/" method="post"></form>');

			var today = new Date();
	    var dd = today.getDate();
	    var mm = today.getMonth()+1; //January is 0!
	    var yyyy = today.getFullYear();
			if(dd<10){dd='0'+dd}
	    if(mm<10){mm='0'+mm}
			var date = yyyy+'-'+mm+'-'+dd;

			$("#popup > form").append('<input type="date" value="'+date+'" name="date">');
			$("#popup > form").append('<input type="time" value="12:00" name="time">');

			$("#popup > form").append('<hr>');
			$.post( '/api/', {action: "languages"},"json")
			.done(function(data){
				for(i = 0;i<data.length;i++){
					if(i>0){
						$("#popup > form").append('<hr>');
					}
					$("#popup > form").append('<input type="text" value="'+data[i]+'" name="lang[]" readonly>');
					$("#popup > form").append('<input type="text" value="" name=title[] placeholder="'+translation.admin_newCalendarTitle+'">');
					$("#popup > form").append('<input type="text" value="" name=location[] placeholder="'+translation.admin_newCalendarLocation+'">');
					$("#popup > form").append('<textarea name="short[]" placeholder="'+translation.admin_newCalendarShort+'"></textarea>');
					$("#popup > form").append('<textarea name="description[]" placeholder="'+translation.admin_newCalendarDescription+'"></textarea>');
				}
				$("#popup > form").append('<input type="hidden" name="action" value="newCalendar">');
				$("#popup > form").append('<input type="submit" value="'+translation.admin_newCalendarSubmit+'">');

			})
			.fail(function(d, textStatus, error) {
						console.error("getJSON failed, status: " + textStatus + ", error: "+error);
				});
		}
		else if($(this).attr("id") == "editLead"){
				if($(this).attr("data-type") == "index"){
					//edit settings of the site
					$("#popup").append('<h1>'+translation.admin_editSettings+'</h1>');
					$("#popup").append('<form action="/api/" method="post"></form>');
					$.post( '/api/', {action: "getSettings"},"json")
					.done(function(data){
						for(i = 0;i<data.length;i++){
							if(i>0){
								$("#popup > form").append('<hr>');
							}
							$("#popup > form").append('<input type="text" value="'+data[i].lang+'" name="lang[]" readonly>');
							$("#popup > form").append('<input type="text" value="'+data[i].name+'" name=name[] placeholder="'+translation.admin_settingsSiteName+'">');
							$("#popup > form").append('<input type="text" value="'+data[i].title+'" name=title[] placeholder="'+translation.admin_settingsSiteTitle+'">');
							$("#popup > form").append('<textarea name="meta[]" placeholder="'+translation.admin_settingsSiteMeta+'">'+data[i].meta+'</textarea>');
							$("#popup > form").append('<input type="text" value="'+data[i].logo+'" name=logo[] placeholder="'+translation.admin_settingslogoTitle+'">');
							$("#popup > form").append('<textarea name="description[]" placeholder="'+translation.admin_settingsSiteDescription+'">'+data[i].description+'</textarea>');
						}
						$("#popup > form").append('<input type="hidden" name="action" value="editSettings">');
						$("#popup > form").append('<input type="submit" value="'+translation.admin_editSettingsSubmit+'">');

					})
					.fail(function(d, textStatus, error) {
								console.error("getJSON failed, status: " + textStatus + ", error: "+error);
					});
				}
				else if($(this).attr("data-type") == "cat"){
					//edit cat
					$("#popup").append('<h1>'+translation.admin_editCat+'</h1>');
					$("#popup").append('<form action="/api/" method="post"></form>');
					$("#popup > form").append('<input type="integer" value="'+$(this).attr("data-priority")+'" name="priority" placeholder="'+translation.admin_CatPriority+'">');
					var cat = $(this).attr("data-cat");
					$.post( '/api/', {action: "getCat", cat : cat},"json")
					.done(function(data){
						$("#popup > form").append('<input type="text" value="'+data[0].template+'" name="template" placeholder="'+translation.admin_setTemplate+'">');
						$("#popup > form").append('<hr>');
						for(i = 0;i<data.length;i++){
							if(i>0){
								$("#popup > form").append('<hr>');
							}
							$("#popup > form").append('<input type="text" value="'+data[i].lang+'" name="lang[]" readonly>');
							$("#popup > form").append('<input type="text" value="'+data[i].name+'" name=name[] placeholder="'+translation.admin_CatName+'">');
							$("#popup > form").append('<input type="text" value="'+data[i].image+'" name=image[] placeholder="'+translation.admin_urlToImg+'">');
							$("#popup > form").append('<textarea name="description[]" placeholder="'+translation.admin_CatDescription+'">'+data[i].description+'</textarea>');
						}
						$("#popup > form").append('<input type="hidden" name="cat" value="'+cat+'">');
						$("#popup > form").append('<input type="hidden" name="action" value="editCat">');
						$("#popup > form").append('<input type="submit" value="'+translation.admin_editCatSubmit+'">');

					})
					.fail(function(d, textStatus, error) {
								console.error("getJSON failed, status: " + textStatus + ", error: "+error);
					});
				}
				else if($(this).attr("data-type") == "subcat"){
					//edit subCat
					$("#popup").append('<h1>'+translation.admin_editSubCat+'</h1>');
					$("#popup").append('<form action="/api/" method="post"></form>');
					var cat = $(this).attr("data-cat");
					$.post( '/api/', {action: "getSubCat", cat : cat},"json")
					.done(function(data){
						$("#popup > form").append('<input type="text" value="'+data[0].template+'" name=template placeholder="'+translation.admin_setTemplate+'">');
						$("#popup > form").append('<input type="text" value="'+data[0].maxItem+'" name=maxItem placeholder="'+translation.admin_maxItem+'">');
						$("#popup > form").append('<hr>');
						for(i = 0;i<data.length;i++){
							if(i>0){
								$("#popup > form").append('<hr>');
							}
							$("#popup > form").append('<input type="text" value="'+data[i].lang+'" name="lang[]" readonly>');
							$("#popup > form").append('<input type="text" value="'+data[i].name+'" name=name[] placeholder="'+translation.admin_SubCatName+'">');
							$("#popup > form").append('<input type="text" value="'+data[i].image+'" name=image[] placeholder="'+translation.admin_urlToImg+'">');
							$("#popup > form").append('<textarea name="description[]" placeholder="'+translation.admin_SubCatDescription+'">'+data[i].description+'</textarea>');
							$("#popup > form").append('<textarea name="short[]" placeholder="'+translation.admin_SubCatShort+'">'+data[i].short+'</textarea>');
						}
						$("#popup > form").append('<input type="hidden" name="cat" value="'+cat+'">');
						$("#popup > form").append('<input type="hidden" name="action" value="editSubCat">');
						$("#popup > form").append('<input type="submit" value="'+translation.admin_editSubCatSubmit+'">');

					})
					.fail(function(d, textStatus, error) {
								console.error("getJSON failed, status: " + textStatus + ", error: "+error);
					});
				}
		}
		else if($(this).attr("id") == "editItem"){
			//edit item
			$("#popup").append('<h1>'+translation.admin_editItem+'</h1>');
			$("#popup").append('<form action="/api/" method="post"></form>');
			var item = $(this).attr("data-item");
			var lang = $(this).attr("data-lang");
			$.post( '/api/', {action: "getItem", item : item, lang : lang},"json")
			.done(function(data){
				for(i = 0;i<data.items.length;i++){
					if(i>0){
						$("#popup > form").append('<hr>');
					}
					$("#popup > form").append('<input type="text" value="'+data.items[i].lang+'" name="lang[]" readonly>');
					$("#popup > form").append('<input type="text" value="'+data.items[i].title+'" name=title[] placeholder="'+translation.admin_itemTitle+'">');
					$("#popup > form").append('<textarea name="short[]" placeholder="'+translation.admin_itemShort+'">'+data.items[i].short+'</textarea>');
					$("#popup > form").append('<textarea name="content[]" placeholder="'+translation.admin_itemContent+'">'+data.items[i].content+'</textarea>');
				}
				$("#popup > form").append('<hr>');
				for(i = 0;i<data.tags.length;i++){
					$("#popup > form").append('<input type="checkbox" name="tags[]" value="'+data.tags[i].id+'" id="tags'+data.tags[i].id+'" '+data.tags[i].checked+'><label for="tags'+data.tags[i].id+'">'+data.tags[i].name+'</label>');
				}

				$("#popup > form").append('<input type="hidden" name="item" value="'+item+'">');
				$("#popup > form").append('<input type="hidden" name="action" value="editItem">');
				$("#popup > form").append('<input type="submit" value="'+translation.admin_editItemSubmit+'">');

			})
			.fail(function(d, textStatus, error) {
						console.error("getJSON failed, status: " + textStatus + ", error: "+error);
			});
		}
		else if($(this).attr("id") == "CSS"){
			//edit item
			$("#popup").append('<h1>'+translation.admin_editCSS+'</h1>');
			$("#popup").append('<form action="/api/" method="post"></form>');
			$.post( '/api/', {action: "getCSS"},"json")
			.done(function(data){
				$("#popup > form").append('<textarea name="CSS" placeholder="'+translation.admin_editCSS+'">'+data.CSS+'</textarea>');
				$("#popup > form").append('<input type="hidden" name="action" value="editCSS">');
				$("#popup > form").append('<input type="submit" value="'+translation.admin_editCSSSubmit+'">');

			})
			.fail(function(d, textStatus, error) {
						console.error("getJSON failed, status: " + textStatus + ", error: "+error);
			});
		}
		else if($(this).attr("id") == "plugins"){
			//edit item
			$("#popup").append('<h1>'+translation.admin_editPlugins+'</h1>');
			$.post( '/api/', {action: "getPlugins"},"json")
			.done(function(data){
				for(i = 0;i<data.length;i++){
					$("#popup").append('<div id="plugin'+data[i].id+'" class="pluginList"></div>');
					$("#popup > #plugin"+data[i].id).append('<p>'+data[i].file+' ("'+data[i].public1+'","'+data[i].public2+'","'+data[i].public3+'")</p>');
					$("#popup > #plugin"+data[i].id).append('<p class="editPlugin" data-id="'+data[i].id+'">'+translation.admin_editPlugin+'</p>');
					$("#popup > #plugin"+data[i].id).append('<p class="deletePlugin" data-id="'+data[i].id+'">'+translation.admin_deletePlugin+'</p>');
				}
				$("#popup").append('<p id="newPlugin">'+translation.admin_newPlugin+'</p>');


				$("#newPlugin").on("click",function(){
					//open the editPlugin with a new plugin
					$.post( '/api/', {action: "newPlugin"},"json")
					.done(function(data){
						editPlugin(data);
					})
					.fail(function(data){});
				});

				$(".editPlugin").on("click",function(){
					//open the editPlugin with a new plugin
					$.post( '/api/', {action: "retrievePlugin", id : $(this).attr("data-id")},"json")
					.done(function(data){
						editPlugin(data);
					})
					.fail(function(data){});
				});

				$(".deletePlugin").on("click",function(){
					$("#plugin"+$(this).attr("data-id")).remove();
						$.post( '/api/', {action: "deletePlugin", plugin : $(this).attr("data-id")},"json").done(function(data){
					});
				});

			})
			.fail(function(d, textStatus, error) {
						console.error("getJSON failed, status: " + textStatus + ", error: "+error);
			});
		}
		$("#closePopup").on("click",function(){
			$("#blackout").remove();
		});
	});


	function editPlugin(data){
		$("#popup").html('<div id="closePopup">X</div>');
		$("#popup").append('<h1>'+translation.admin_editPlugins+'</h1>');
		$("#popup").append('<form action="/api/" method="post"></form>');
		$("#popup > form").append('<select name="file" id="pluginList"></select>');
		for(i=0;i<data.pluginList.length;i++){
			if(data.pluginList[i] == data.plugin.file){
				var checked = " selected";
			}
			else{
				var checked = "";
			}
			$("#popup > form > #pluginList").append('<option value="'+data.pluginList[i]+'"'+checked+'>'+data.pluginList[i]+'</option>');
		}
		$("#popup > form").append('<input type="text" value="'+data.plugin.public1+'" name="public1">');
		$("#popup > form").append('<input type="text" value="'+data.plugin.public2+'" name="public2">');
		$("#popup > form").append('<input type="text" value="'+data.plugin.public3+'" name="public3">');
		$("#popup > form").append('<input type="text" value="'+data.plugin.int1+'" name="int1" readonly>');
		$("#popup > form").append('<input type="text" value="'+data.plugin.int2+'" name="int2" readonly>');
		$("#popup > form").append('<input type="text" value="'+data.plugin.int3+'" name="int3" readonly>');
		$("#popup > form").append('<input type="text" value="'+data.plugin.txt1+'" name="txt1" readonly>');
		$("#popup > form").append('<input type="text" value="'+data.plugin.txt2+'" name="txt2" readonly>');
		$("#popup > form").append('<input type="text" value="'+data.plugin.txt3+'" name="txt3" readonly>');
		$("#popup > form").append('<input type="hidden" name="id_plugin" value="'+data.plugin.id+'">');
		$("#popup > form").append('<input type="hidden" name="action" value="editPlugin">');
		$("#popup > form").append('<input type="submit" value="'+translation.admin_editPlugin+'">');

		$("#closePopup").on("click",function(){
			$("#blackout").remove();
		});
	}

});
