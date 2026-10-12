function trimDecimal(str) {
	while (true) {
		if (str.substring(str.length - 1) == "0") {
			str = str.substring(0, str.length - 1);
		} else if(str.substring(str.length - 1) == ".") {
			str = str.substring(0, str.length - 1);
			break;
		} else {
			break;
		}
	}
	return str;
}

function getHTTPObject(){
	if (window.ActiveXObject) 
		return new ActiveXObject("Microsoft.XMLHTTP");
	else if (window.XMLHttpRequest) 
		return new XMLHttpRequest();
	else {
		alert("Your browser does not support AJAX.");
		return null;
	}
}

function removeErrorPopup() {
	document.getElementById('errorPopupHolder').innerHTML = "";
}

Array.prototype.find = function(searchStr) {
	var returnArray = false;
	for (i=0; i<this.length; i++) {
		if (typeof(searchStr) == 'function') {
			if (searchStr.test(this[i])) {
				if (!returnArray) { returnArray = [] }
				returnArray.push(i);
			}
		} else {
			if (this[i]===searchStr) {
				if (!returnArray) { returnArray = [] }
				returnArray.push(i);
			}
		}
	}
	return returnArray;
}

function sendAJAX(url, params, callback) {
	httpObject = getHTTPObject();
	if (httpObject != null) {
		urlString = baseURL + "/" + url;
		if (params != null) {
			for (i = 0; i < params.length; i+=2) {
				if (i > 0) {
					urlString += "&";
				} else {
					urlString += "?";
				}
				urlString += params[i];
				urlString += "=";
				urlString += params[i+1];
			}
		}
		httpObject.onreadystatechange = function() { 
        	if (httpObject.readyState == 4) {
            	if (httpObject.status == 200) {
					try { // Internet Explorer
						xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
						xmlDoc.async = "false";
						xmlDoc.loadXML(httpObject.responseText);
					} catch (e) {
						try { //Firefox Mozilla etc
							parser = new DOMParser();
							xmlDoc = parser.parseFromString(httpObject.responseText,"text/xml");
						} catch (e) {
							alert(e.message)
						}
					}
					callback(xmlDoc);
				}
    		}
    	}
		httpObject.open("GET",urlString, true);
		httpObject.send(null);
	}
}

