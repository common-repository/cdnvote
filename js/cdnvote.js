function cdnvote_post(that) {
	
	input_item = that.getElementsByTagName('input');
	cdnvote_point = -1;
	cdnvote_id = 0;
	for (i = 0; i < input_item.length; i++){
		current_item = input_item[i];
		if (current_item.type == 'radio' && current_item.checked){
			cdnvote_point = current_item.value;
		}
		
		if (current_item.type == 'hidden'){
			cdnvote_post_id = current_item.value;
		}
	}
	
	

	var urlStr = that.action;
	var xmlHttpReq = cdnvote_create_http_request();

	xmlHttpReq.open("post", urlStr ,true) 
	xmlHttpReq.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlHttpReq.send("cdnvote_post_id=" + cdnvote_post_id + "&cdnvote_point=" + cdnvote_point);
	
	cdnvote_submit_button_voting("cdnvote_form_button_" + cdnvote_post_id);//submit button desable.
	
	xmlHttpReq.onreadystatechange = function() {
		if (xmlHttpReq.readyState==4) { 
			cdnvote_submit_button_voted("cdnvote_form_button_" + cdnvote_post_id);
		}
	}
	return false;
} 

function cdnvote_create_http_request() { 
	var x = null; 

	//IE7,Firefox, Safari 
	if (window.XMLHttpRequest) { 
		return new XMLHttpRequest(); 
	} 

	//IE6 
	try { 
		return new ActiveXObject("Msxml2.XMLHTTP"); 
	} catch(e) { 
	// IE5 
		try { 
			return new ActiveXObject("Microsoft.XMLHTTP"); 
		} catch(e) { 
			x = null; 
		} 
	} 
	return x; 
}

function cdnvote_submit_button_voting(target_id){
	var form_button_obj = document.getElementById(target_id);
	form_button_obj.innerHTML = "now voting...";
	form_button_obj.disabled = true;
}

function cdnvote_submit_button_voted(target_id){
	var form_button_obj = document.getElementById(target_id);
	form_button_obj.innerHTML = "Thanks for the vote.";
}