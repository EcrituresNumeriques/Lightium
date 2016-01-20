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
					var cat = $(this).attr("data-cat");
					$.post( '/api/', {action: "getCat", cat : cat},"json")
					.done(function(data){
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
			$.post( '/api/', {action: "getItem", item : item},"json")
			.done(function(data){
				for(i = 0;i<data.length;i++){
					if(i>0){
						$("#popup > form").append('<hr>');
					}
					$("#popup > form").append('<input type="text" value="'+data[i].lang+'" name="lang[]" readonly>');
					$("#popup > form").append('<input type="text" value="'+data[i].title+'" name=title[] placeholder="'+translation.admin_itemTitle+'">');
					$("#popup > form").append('<textarea name="short[]" placeholder="'+translation.admin_itemShort+'">'+data[i].short+'</textarea>');
					$("#popup > form").append('<textarea name="content[]" placeholder="'+translation.admin_itemContent+'">'+data[i].content+'</textarea>');
				}
				$("#popup > form").append('<input type="hidden" name="item" value="'+item+'">');
				$("#popup > form").append('<input type="hidden" name="action" value="editItem">');
				$("#popup > form").append('<input type="submit" value="'+translation.admin_editItemSubmit+'">');

			})
			.fail(function(d, textStatus, error) {
						console.error("getJSON failed, status: " + textStatus + ", error: "+error);
			});
		}
		$("#closePopup").on("click",function(){
			$("#blackout").remove();
		});
	});

});
