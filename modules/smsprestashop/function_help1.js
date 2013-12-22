function popup_title(obj)
{
	var $this = $(obj);
	var title = $this.attr('title');
	$(".popup-title").fadeOut(500, function() {
		$(this).remove();
	});
	var position = $this.position();
	var popup = $("<p class='popup-title' style='display: none;'>" + title + "</p>");
	$("body").append(popup);
	popup.css("position", "absolute")
		.css("top", (position.top-20)+"px")
		.css("left", (position.left+10)+"px")
		.css("max-width", "400px")
		.css("background","black")
		.css("color", "white")
		.css("text-align", "left")
		.css("padding", "5px")
		.css("cursor", "pointer")
		.fadeIn(500);
	popup.click(function() {
		$this.attr("title", $(this).text());
		$(popup).fadeOut(500, function() { $(this).remove() });
	});
}
