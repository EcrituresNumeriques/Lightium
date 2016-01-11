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
		$("#closePopup").on("click",function(){
			$("#blackout").remove();
		});
	});

});
