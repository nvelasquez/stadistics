$(document).ready(function(){
	var menudes = $("#content_menu");
	var var_logo = $(".logo");
	var filtro = $(".down");
	var abrir_menu = false;
	

	filtro.click(function(){
		abrir_menu = !abrir_menu;
		if(abrir_menu)
		
			menudes.animate({"margin-top":"0px"},100);
		else
			menudes.animate({"margin-top":"-230px"},200);
	});
	
	$(".grafico").click(function(){
		$("#container").animate({
			"margin-top":"-500px"
		});
		$("#content_graphic, #content_tabla, #content_map ").css("z-index","0");
		$("#content_graphic").css("z-index","1");
		
		$("#container").animate({
			"margin-top":"60px"
		});
	});
	
	$(".mapa").click(function(){
		$("#container").animate({
			"margin-top":"-500px"
		});
		$("#content_graphic, #content_tabla, #content_map ").css("z-index","0");
		$("#content_map").css("z-index","1");
		
		$("#container").animate({
			"margin-top":"60px"
		});
	});
	
	$(".tabla").click(function(){
		$("#container").animate({
			"margin-top":"-500px"
		});
		$("#content_graphic, #content_tabla, #content_map ").css("z-index","0");
		$("#content_tabla").css("z-index","1");
		
		$("#container").animate({
			"margin-top":"60px"
		});
	});
	
});
