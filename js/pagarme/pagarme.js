/**
 *
 * @category   Inovarti
 * @package    Inovarti_Pagarme
 * @author     Suporte <suporte@inovarti.com.br>
 */

var Pagarme = {
	load: function(url){
		new Ajax.Request(url, {
		  	onSuccess: function(response) {
		  		var response = response.responseText.evalJSON();
		  		if (response.success) {
					var win = new Window('pagarme', {className:'magento', title:'Pagar.me', width:600, height:370, zIndex:1000, opacity:1, destroyOnClose:true, draggable: false, showEffect: Element.show});
					win.setHTMLContent(response.content_html);
					win.showCenter(true);
		  		} else {
		  			alert(response.error_message);
		  		}
		  	}
		});
		return false;
	}
}
