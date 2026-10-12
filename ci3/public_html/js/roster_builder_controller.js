var commands = new Array();
commands[1] = new Command(1);
commands[2] = new Command(2);
commands[3] = new Command(3);
commands[4] = new Command(4);
commands[5] = new Command(5);


function showElementTypePopup(command) {
	var html = "<div id=\"elementTypePopup\" class=\"popup\">";
	html += "<table class=\"rosterPopupTable\">";
	html += "<tr>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'EL');\">Elephants</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'PK');\">Pike</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'ART');\">Artillery</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'SHP');\">Ships</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'TF');\">TF</a></span></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'KN');\">Knights</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'SP');\">Spear</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'WWG');\">War-Wagons</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'GAL');\">Galleys</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'PF');\">PF</a></span></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'CV');\">Cavalry</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'BD');\">Blade</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'BGE');\">Baggage</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'BTS');\">Boats</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'PO');\">PO</a></span></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'CM');\">Camelry</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'AX');\">Auxilia</a></span></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'FO');\">FO</a></span></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'LH');\">Light Horse</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'BW');\">Bows</a></span></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'EXP');\">Expendables</a></span></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'WB');\">Warband</a></span></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'PS');\">Psiloi</a></span></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'HD');\">Hordes</a></span></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "</tr>";
	html += "<tr>";
	html += "<td></td>";
	html += "<td><span class=\"button\" style=\"width:100px;\"><a href=\"javascript:elementTypeSelected(1, 'SH');\">Shot</a></span></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "<td></td>";
	html += "</tr>";
	html += "</table></div>";
	
	document.getElementById("popupHolder").innerHTML = html;
}

function removeElementTypePopup() {
	document.getElementById("popupHolder").innerHTML = "";
}

function elementTypeSelected(command, elementType) {
	removeElementTypePopup();
	// TF, PF, PO, FO dont have grades or training...
	// TF, PF and FO go to extras
	// PO go to command selector
	switch (elementType.valueOf()) {
	case "TF":
		showElementExtrasPopup(command, elementType);
		break;
	case "PF":
		showElementExtrasPopup(command, elementType);
		break;
	case "PO":
		element = elementObjectFactory(elementType);
		addElementToCommand(command, element, 1);
		break;
	case "FO":
		showElementExtrasPopup(command, elementType);
		break;
	default:
		showElementGradeTrainingPopup(command, elementType);
		break;
	}
}

function elementObjectFactory(elementTypeString) {
	var element = new Element(elementTypeString);
/*
	switch (elementTypeString.valueOf()) {
	case "ART":
		var element = new Artillery();
		break;
	case "AX":
		var element = new Auxilia();
		break;
	case "BD":
		var element = new Blade();
		break;
	case "BGE":
		var element = new Baggage();
		break;
	case "BTS":
		var element = new Boats();
		break;
	case "BW":
		var element = new Bow();
		break;
	case "CM":
		var element = new Camelry();
		break;
	case "CV":
		var element = new Cavalry();
		break;
	case "EL":
		var element = new Elephants();
		break;
	case "EXP":
		var element = new Expendables();
		break;
	case "GAL":
		var element = new Galleys();
		break;
	case "HD":
		var element = new Hordes();
		break;
	case "KN":
		var element = new Knights();
		break;
	case "LH":
		var element = new LightHorse();
		break;
	case "PK":
		var element = new Pike();
		break;
	case "PS":
		var element = new Psiloi();
		break;
	case "SHP":
		var element = new Ships();
		break;
	case "SH":
		var element = new Shot();
		break;
	case "SP":
		var element = new Spear();
		break;
	case "WB":
		var element = new Warband();
		break;
	case "WWG":
		var element = new Warwagons();
		break;
	case "TF":
		var element = new TempFort();
		break;
	case "PF":
		var element = new PermFort();
		break;
	case "PO":
		var element = new PortObst();
		break;
	case "FO":
		var element = new FixedObst();
		break;
	}
	*/
	return element;
}

function showElementGradeTrainingPopup(command, elementType) {
	mElement = elementObjectFactory(elementType);
	if (mElement == null) {
		removeElementGradeTrainingPopup();
	} else {
		var flags = mElement.getAllowedGradesAndTraining();
		var html = "<div id=\"elementTypePopup\" class=\"popup\">";
		html += "<table class=\"rosterPopupTable\">";
		html += "<tr><td>";
		if (flags[0] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'REG', 'S');\">Reg (S)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td><td>";
		if (flags[5] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'IRR', 'S');\">Irr (S)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td></tr><tr><td>";
		if (flags[1] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'REG', 'O');\">Reg (O)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td><td>";
		if (flags[6] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'IRR', 'O');\">Irr (O)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td></tr><tr><td>";
		if (flags[2] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'REG', 'F');\">Reg (F)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td><td>";
		if (flags[7] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'IRR', 'F');\">Irr (F)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td></tr><tr><td>";
		if (flags[3] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'REG', 'I');\">Reg (I)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td><td>";
		if (flags[8] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'IRR', 'I');\">Irr (I)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td></tr><tr><td>";
		if (flags[4] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'REG', 'X');\">Reg (X)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td><td>";
		if (flags[9] == true) {
			html += "<span class=\"button\"><a href=\"javascript:elementGradeTrainingSelected(" + command + ", 'IRR', 'X');\">Irr (X)</a></span>";
		} else {
			html += "&nbsp;";
		}
		html += "</td></tr>";
		html += "</table></div>";
		
		document.getElementById("popupHolder").innerHTML = html;		
	}
}

function removeElementGradeTrainingPopup() {
	document.getElementById("popupHolder").innerHTML = "";
}

function elementGradeTrainingSelected(command, training, grade) {
	mElement.setGradeByLetter(grade);
	mElement.setTraining(training);
	//Shp, Gal cant have extras
	if (mElement.getType() == "SHP" || mElement.getType() == "GAL" || mElement.getType() == "ART") {
		addElementToCommand(command, mElement, 1);
	} else {
		showElementExtrasPopup(command);
	}
}

function showElementExtrasPopup(command) {
	var html = "<div id=\"elementTypePopup\" class=\"popup\">";
	html += "<table class=\"rosterPopupTable\">";
	if (mElement.canBeGeneral()) {
		html += "<tr><td>General</td><td>Brilliant</td><td>Normal</td><td>Inert</td></tr>";
		html += "<tr><td>C-in-C</td>";
		html += "<td><input type=\"radio\" name=\"g\" value=\"BC\" id=\"BC\" /></td>";
		html += "<td><input type=\"radio\" name=\"g\" value=\"NC\" id=\"NC\" /></td>";
		html += "<td><input type=\"radio\" name=\"g\" value=\"IC\" id=\"IC\" /></td>";
		html += "</tr>";
		html += "<tr><td>Sub General</td>";
		html += "<td><input type=\"radio\" name=\"g\" value=\"BS\" id=\"BS\" /></td>";
		html += "<td><input type=\"radio\" name=\"g\" value=\"NS\" id=\"NS\" /></td>";
		html += "<td></td>";
		html += "</tr>";
		html += "<tr><td>Ally General</td>"
		html += "<td><input type=\"radio\" name=\"g\" value=\"BA\" id=\"BA\" /></td>";
		html += "<td><input type=\"radio\" name=\"g\" value=\"NA\" id=\"NA\" /></td>";
		html += "<td><input type=\"radio\" name=\"g\" value=\"IA\" id=\"IA\" /></td>";
		html += "</tr>";
		html += "</tr><td>"
		html += "</td></tr>";
	}
	html += "</table><table><tr><td>";
	html += "<span class=\"button\"><a href=\"javascript:removeElementExtrasPopup();\">Cancel</a></span>";
	html += "<span class=\"button\"><a href=\"javascript:elementExtrasSelected(" + command + ");\">Next</a></span>";
	html += "</div>";
	document.getElementById("popupHolder").innerHTML = html;		
}

function removeElementExtrasPopup() {
	document.getElementById("popupHolder").innerHTML = "";
}

function elementExtrasSelected(command) {
	if (mElement.canBeGeneral()) {
		if (document.getElementById('BC').checked) {
			mElement.setGeneral("B C-in-C");
		}
	}
	removeElementExtrasPopup();
	addElementToCommand(command, mElement, 1);
}

function addElementToCommand(command, element, number) {
	commands[command].addElement(element, number);
	var html = "<span class=\"button\"><a href=\"javascript:showElementTypePopup(" + command +");\">Add Element</a></span>";
	var elements = commands[command].getElements();
	for (var key in elements) {
		html += "<span class=\"button\"><a href=\"javascript:showElementModifyPopup(" + command + "," + key + ");\">" + commands[command].getElementDescByKey(key) + "</a></span>";
	}
	document.getElementById("command" + command + "Elements").innerHTML = html;
	updateTotals();
}

function removeElementFromCommand(command, element, number) {
	
}

function updateTotals() {
	
}

function showElementModifyPopup(element) {
	
}