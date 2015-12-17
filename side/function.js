function pushState(e, url, title){
	e.preventDefault();
	history.pushState('', title, url);
	document.title = title;
}
$(document).ready(function(){
	$("a.pushState").on("click",function(e){
		//pushState(e, $(this).attr("href"), $(this).attr("data-title"));
	});
});
