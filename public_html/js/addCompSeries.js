document.getElementById('menuitem_competitions').className = "menuitemselected";

function addCompetitionSeries() {
	message = "";
	html = "<div class=\"popup\" id=\"addCompSeriesPopup\">\n";
	html += "	<div class=\"line1col1\">Add Competition Series</div>\n";
	html += "	<div class=\"line2col1\"><span id=\"messagelabel\">" + message + "</span></div>\n";
	html += "	<div class=\"line3col1\"><span id=\"compseriesnamelabel\">Name</span></div>\n";
	html += "	<div class=\"line3col2\"><input name=\"compseriesname\" id=\"compseriesname\"/></div>\n";
	html += "	<div class=\"line7col1\">\n";
	html += "   	<div class=\"button\"><a href=\"javascript:addCompSeriesRequest();\"><span>Insert<span></a></div>\n";
	html += "		<div class=\"button\"><a href=\"javascript:removeAddCompSeriesPopup();\"><span>Cancel<span></a></div>\n";
	html += "	</div>\n";
	html += "</div>\n";
	document.getElementById('addCompSeriesHolder').innerHTML = html;
}

function addCompSeriesRequest() {
	errors = 0;
	if (document.getElementById('compseriesname').value == "") {
		document.getElementById('compseriesnamelabel').style.color = '#FF0000';
		document.getElementById('messagelabel').innerHTML = "Please enter the missing details highlighted";
		errors = 1;
	} else {
		document.getElementById('compseriesnamelabel').style.color = '#000000';
	}
	if (errors) {
		return;
	}/*
	httpObject = getHTTPObject();
	if (httpObject != null) {
		urlString = "xml.php/createCompSeries";
		urlString += "?name=" + document.getElementById('compseriesname').value;
		urlString += "&rar=0";
		//alert(urlString);
		httpObject.open("GET",urlString, true);
		httpObject.send(null);
		httpObject.onreadystatechange = addCompSeriesResponse;
	}
	*/
	params = new Array("name", document.getElementById('compseriesname').value, "rar", 0);
	sendAJAX("xml/createCompSeries", params, addCompSeriesResponse);
}

function addCompSeriesResponse(xmlDoc) {
	if(httpObject.readyState == 4) {
		//show the results and format the page for another add
		//alert(httpObject.responseText);
		/*
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
		*/
		status = xmlDoc.getElementsByTagName('response')[0].getAttribute('status');
		if (status == "0") {
			id = xmlDoc.getElementsByTagName("id")[0].childNodes[0].nodeValue;
			name = xmlDoc.getElementsByTagName("name")[0].childNodes[0].nodeValue;
			//document.getElementById('statusMessage').innerHTML = httpObject.responseText;
			removeAddCompSeriesPopup();
			// add the comp series into the select and select it...
			html = "<input name=\"series\" id=\"series\" value=\"" + id + "\" hidden />" + name;
			document.getElementById('seriesPicker').innerHTML = html;
			//html += "<option value=\"" + id + "\" selected>" + name + "</option>\n";
			//document.getElementById('series').innerHTML = html;
		} else {
			// an error occurred
			removeAddCompSeriesPopup();
			errorMessage = xmlDoc.getElementsByTagName("message")[0].childNodes[0].nodeValue;
			alert(errorMessage);
		}
	}
}

function removeAddCompSeriesPopup() {
	document.getElementById('addCompSeriesHolder').innerHTML = "";
}

