// JavaScript file defining all the model objects
Command = function(commandNumber) {

	this.commandNumber = commandNumber;

}
	/**
	 * commandNumber - the number of the command. Command 1 is 1, etc. Baggage command is 5, even if the 
	 * army has less than 4 combat commands.
	 */
Command.prototype.ME = 0;
Command.prototype.AP = 0;
Command.prototype.numElements = 0;
Command.prototype.hasGeneral = false;
Command.prototype.totalBaggage = 0;
Command.prototype.armyBaggage = 0;
Command.prototype.commandElements = new Array();

	/**
	 * Package level to force other classes to use the Army call
	 * 
	 * This is totals-safe
	 * 
	 * Army.addCommandElement
	 * @param e
	 */
Command.prototype.addElement = function(element, number) {
		this.ME = this.ME + (element.getME() * number);
		this.AP = this.AP + (element.getAP() * number);
		this.numElements += number;
		if (this.commandElements.indexOf(element.getElementFingerprint()) == -1) {
			this.commandElements[element.getElementFingerprint()] = element;
		} else {
			this.commandElements[element.getElementFingerprint()].add(number);
		}
		if (element.isGeneral()) {
			this.hasGeneral = true;
		}
		if (element.isBaggage()) {
			this.totalBaggage += number;
			if (element.isArmyBaggage()) {
				this.armyBaggage += number;
				// dont count it towards the elements in the army
				this.numElements -= number;
			}
		}
	}
	
	/**
	 * Totals-safe
	 * 
	 */
Command.prototype.clearCommand = function() {
		this.commandElements = new Array();
		this.AP = 0;
		this.ME = 0;
		this.numElements = 0;
		this.hasGeneral = false;
		this.armyBaggage = 0;
		this.totalBaggage = 0;
	}
	
	/**
	 * @return the armyBaggage
	 */
Command.prototype.getArmyBaggage = function() {
		return this.armyBaggage;
	}
	
Command.prototype.getBreakpoint = function() {
		return this.ME/3;
	}
	
Command.prototype.getCommandXML = function() {
		var s = "<command number=\"";
		s += this.commandNumber;
		s += "\">";
		for (key in commandElements) {
			s += commandElements[key].getElementXML()
		}
		s += "</command>";
		return s;
	}
	
	/*
	this.getCommandCSV = function(army) {
		String s = "Command ";
		s += commandNumber;
		s += "\n";
		army.setCSVRow(army.getCSVRow() + 1);
		int commandStartRow = army.getCSVRow();
		Iterator<Element> i = getElements();
		while (i.hasNext()) {
			s += getElementCSV(i.next(), army);
		}
		s += ",\"Command Total:\",,,\"=sum(E" + commandStartRow + "..E" + (army.getCSVRow() - 1) + ")\"";
		s += ",\"=sum(F" + commandStartRow + "..F" + (army.getCSVRow() - 1) + ")\"\n";
		int commandTotalRow = army.getCSVRow();
		army.setCSVRow(army.getCSVRow() + 1);
		s += ",\"Disheartened:\",,,\"=E" + commandTotalRow + "/4\"\n";
		army.setCSVRow(army.getCSVRow() + 1);
		s += ",\"Broken:\",,,\"=E" + commandTotalRow + "/3\"\n";
		army.setCSVRow(army.getCSVRow() + 1);
		return s;
	}
	*/

Command.prototype.getDisheartened = function() {
		return this.ME/4;
	}
	
Command.prototype.getElementCount = function(element) {
		return this.commandElements[element.getElementFingerprint()].getCount();
	}

Command.prototype.getElementDesc = function(element) {
		return this.commandElements[element.getElementFingerprint()].getCount() + " x " + this.commandElements[element.getElementFingerprint()].getDesc();
	}

Command.prototype.getElementByKey = function(key) {
	return this.commandElements[key];
}

Command.prototype.getElementDescByKey = function(key) {
	//return this.commandElements[key].getCount() + " x " + this.commandElements[key].getDesc();
	alert(key);
	alert(this.commandElements[key].getDesc());
	return this.commandElements[key].getDesc();
	}

Command.prototype.getElementCountByKey = function(key) {
		return this.commandElements[key].getCount();
	}


Command.prototype.getElements = function() {
	return this.commandElements;
	}
/*
	this.getElementXML = function(element) {
		return element.getElementXML(commandElements.get(e));
	}
	
	this.getElementCSV = function(element, army) {
		String s = commandElements.get(element) + ",\"";
		s += element.getDesc();
		s += "\",";
		s += String.valueOf(element.getME());
		s += ",";
		s += String.valueOf(element.getAP());
		s += ",\"=A" + army.getCSVRow() + "*C" + army.getCSVRow();
		s += "\",\"=A" + army.getCSVRow() + "*D" + army.getCSVRow() + "\"" + "\n";
		army.setCSVRow(army.getCSVRow() + 1);
		return s;
	}
	*/

Command.prototype.getMorale = function() {
		return this.ME;
	}

Command.prototype.getNumElements = function() {
		return this.numElements;
	}

Command.prototype.getPoints = function() {
		return this.AP;
	}

Command.prototype.getShattered = function() {
		return this.ME/2;
	}
	
	/**
	 * @return the totalBaggage
	 */
	
Command.prototype.getTotalBaggage = function() {
		return this.totalBaggage;
	}
	
	/**
	 * @return the hasGeneral
	 */
Command.prototype.hasGeneral = function() {
		return this.hasGeneral;
	}


	/**
	 * Totals-safe
	 * 
	 * @param e
	 */
	/*
	this.removeElement = function(element) {
		Enumeration<Element> keys = commandElements.keys();
		int newKeyprint = element.getElementFingerprint();
		while (keys.hasMoreElements()) {
			Element key = (Element)keys.nextElement();
			if (key.getElementFingerprint() == newKeyprint) {
				if (commandElements.get(key) > 1) {
					commandElements.put(key, commandElements.get(key) - 1);
				} else {
					commandElements.remove(key);
				}
				ME = ME.subtract(element.getME());
				AP = AP.subtract(element.getAP());
				numElements--;
				if (e instanceof Baggage) {
					totalBaggage--;
					if (element.isArmyBaggage()) {
						armyBaggage--;
						numElements++;
					}
				}
				break;
			}
		}
	}

*/


	/**
	 * 
	 * @param e
	 * @param i
	 */
	/*
	this.setElementCount = function(element, count) {
		Enumeration<Element> keys = commandElements.keys();
		var newKeyprint = element.getElementFingerprint();
		var elementFound = false;
		while (keys.hasMoreElements()) {
			Element key = (Element)keys.nextElement();
			if (key.getElementFingerprint() == newKeyprint) {
				elementFound = true;
				int oldCount = commandElements.get(key);
				if (oldCount < i) {
					// we could do this more efficiently with addElement(e, i- oldCount)
					// will let sleeping dogs lie for the moment
					for (int j = 0; j < i - oldCount; j++) {
						addElement(element);
					}
				} else if (oldCount > i) {
					for (int j = 0; j < oldCount - i; j++) {
						removeElement(element);
					}
				}
				break;
			}
		}
		if (!elementFound) {
			//commandElements.put(e, i);
			addElement(e, i);
		}
	}

	this.validate = function() {
		ArrayList<String> errors = new ArrayList<String>();
		// set up some counters
		int numGenerals = 0;
		int numBaggage = 0;
		boolean hasMounted = false;
		// only check commands with elements in them
		if (numElements > 0) {
			Enumeration<Element> keys = commandElements.keys();
			while (keys.hasMoreElements()) {
				Element e = keys.nextElement();
				if (e.isGeneral()) {
					numGenerals += commandElements.get(e);
				}
				if (e instanceof Baggage && e.getGrade() != Definitions.SUP) {
					numBaggage += commandElements.get(e);
				}
				if (e.isMounted()) {
					hasMounted = true;
				}
			}
			if (commandNumber < 5) { 
				// checks for combat commands combat commands
				switch (numGenerals) {
				case 0:
					errors.add("No general in command. ");
					hasGeneral = false;
					break;
				case 1:
					hasGeneral = true;
					break;
				default:
					hasGeneral = false;
					errors.add("Too many generals in command. ");
					break;
				}
				if (numBaggage > 1 && (getNumElements() + armyBaggage) < 10) {
					errors.add("More than one baggage elements in a small command. ");
				}
				if (numBaggage > 2) {
					errors.add("More than two baggage elements in command. ");
				}
				// do we have more than 4 elements plus the general
				if (numElements < 5) {
					errors.add("Not enough elements in the command. ");
				}
				// do we have more than 4 ME in addition to the general (so 8 ME)
				if (ME.compareTo(new BigDecimal("8")) == -1) {
					errors.add("Not enough ME in the command. ");
				}
			} else {
				// checks for army baggage command
				if (numGenerals > 0) {
					errors.add("You can't have a general in an army baggage command. ");
				}
				// are there mounted in a baggage command
				if (hasMounted) {
					errors.add("You cannot have mounted troops in an army baggage command");
				}
				// are there more foot/train than baggage in a baggage commmand
				BigDecimal baggageME = new BigDecimal(numBaggage * 2);
				BigDecimal nonBaggageME = getMorale().subtract(baggageME);
				if (nonBaggageME.compareTo(baggageME) == 1) {
					errors.add("You cannot have more ME of non-army baggage in the baggage command than army baggage");
				}
			}
		}
		return errors;
	}

	this.hasCinC = function() {
		Enumeration<Element> keys = commandElements.keys();
		while (keys.hasMoreElements()) {
			if (((Element)keys.nextElement()).isCinC() == true) {
				return true;
			}
		}
		return false;
	}
	
	this.getGeneral = function() {
		Enumeration<Element> keys = commandElements.keys();
		while (keys.hasMoreElements()) {
			Element e = ((Element)keys.nextElement());
			if (e.isGeneral() == true) {
				return e;
			}
		}
		return null;
	}
	*/

Command.prototype.getCommandNumber = function() {
		return this.commandNumber;
	}


//Command.prototype = new Command();

function Element(type) {
	this.type = type;
}

Element.prototype.type = null;
Element.prototype.grade = null;
Element.prototype.training = null;
Element.prototype.cInC = false;
Element.prototype.aG = false;
Element.prototype.sG = false;
Element.prototype.brilliant = false;
Element.prototype.inert = false;
Element.prototype.forCamp = false;
Element.prototype.dbe = false;
Element.prototype.chariot = false;
Element.prototype.isArmyBaggage = false;
Element.prototype.mountedInfantry = false;
Element.prototype.gateway = false;
Element.prototype.hidden = false;
Element.prototype.noShooting = false;
Element.prototype.warDogs = false;
Element.prototype.count = 1;
Element.prototype.allowedGradesAndTraining = {
	"ART": [true,true,true,true,true,false,false,false,false,false],
	"AX": [true,true,false,true,false,true,true,false,true,false],
	"BD": [false,false,false,false,false,false,false,false,false,false],
	"BGE": [false,false,false,false,false,false,false,false,false,false],
	"BTS": [false,false,false,false,false,false,false,false,false,false],
	"BW": [false,false,false,false,false,false,false,false,false,false],
	"CM": [false,false,false,false,false,false,false,false,false,false],
	"CV": [false,false,false,false,false,false,false,false,false,false],
	"EL": [false,false,false,false,false,true,true,false,true,true],
	"EXP": [false,false,false,false,false,false,false,false,false,false],
	"GAL": [false,false,false,false,false,false,false,false,false,false],
	"HD": [false,false,false,false,false,false,false,false,false,false],
	"KN": [false,false,false,false,false,false,false,false,false,false],
	"LH": [false,false,false,false,false,false,false,false,false,false],
	"PK": [false,false,false,false,false,false,false,false,false,false],
	"PS": [false,false,false,false,false,false,false,false,false,false],
	"SHP": [false,false,false,false,false,false,false,false,false,false],
	"SH": [false,false,false,false,false,false,false,false,false,false],
	"SP": [false,false,false,false,false,false,false,false,false,false],
	"WB": [false,false,false,false,false,false,false,false,false,false],
	"WWG": [false,false,false,false,false,false,false,false,false,false],
	"TF": [false,false,false,false,false,false,false,false,false,false],
	"PF": [false,false,false,false,false,false,false,false,false,false],
	"PO": [false,false,false,false,false,false,false,false,false,false],
	"FO": [false,false,false,false,false,false,false,false,false,false],
	};
Element.prototype.apValues = {
	"ART": [[10,8,10,4,4],[0,0,0,0,0]],
	"AX":[[10,8,10,4,4],[0,0,0,0,0]],
	"BD":[[10,8,10,4,4],[0,0,0,0,0]],
	"BGE":[[10,8,10,4,4],[0,0,0,0,0]],
	"BTS":[[10,8,10,4,4],[0,0,0,0,0]],
	"BW":[[10,8,10,4,4],[0,0,0,0,0]],
	"CM":[[10,8,10,4,4],[0,0,0,0,0]],
	"CV":[[10,8,10,4,4],[0,0,0,0,0]],
	"EL":[[0,0,0,0,0],[20,16,0,12,22]],
	"EXP":[[10,8,10,4,4],[0,0,0,0,0]],
	"GAL":[[10,8,10,4,4],[0,0,0,0,0]],
	"HD":[[10,8,10,4,4],[0,0,0,0,0]],
	"KN":[[10,8,10,4,4],[0,0,0,0,0]],
	"LH":[[10,8,10,4,4],[0,0,0,0,0]],
	"PK":[[10,8,10,4,4],[0,0,0,0,0]],
	"PS":[[10,8,10,4,4],[0,0,0,0,0]],
	"SHP":[[10,8,10,4,4],[0,0,0,0,0]],
	"SH":[[10,8,10,4,4],[0,0,0,0,0]],
	"SP":[[10,8,10,4,4],[0,0,0,0,0]],
	"WB":[[10,8,10,4,4],[0,0,0,0,0]],
	"WWG":[[10,8,10,4,4],[0,0,0,0,0]],
	"TF":[[10,8,10,4,4],[0,0,0,0,0]],
	"PF":[[10,8,10,4,4],[0,0,0,0,0]],
	"PO":[[10,8,10,4,4],[0,0,0,0,0]],
	"FO":[[10,8,10,4,4],[0,0,0,0,0]],
	};
Element.prototype.meValues = {
	"ART": [[10,8,10,4,4],[0,0,0,0,0]],
	"AX":[[10,8,10,4,4],[0,0,0,0,0]],
	"BD":[[10,8,10,4,4],[0,0,0,0,0]],
	"BGE":[[10,8,10,4,4],[0,0,0,0,0]],
	"BTS":[[10,8,10,4,4],[0,0,0,0,0]],
	"BW":[[10,8,10,4,4],[0,0,0,0,0]],
	"CM":[[10,8,10,4,4],[0,0,0,0,0]],
	"CV":[[10,8,10,4,4],[0,0,0,0,0]],
	"EL":[[0,0,0,0,0],[2,2,0,1,2]],
	"EXP":[[10,8,10,4,4],[0,0,0,0,0]],
	"GAL":[[10,8,10,4,4],[0,0,0,0,0]],
	"HD":[[10,8,10,4,4],[0,0,0,0,0]],
	"KN":[[10,8,10,4,4],[0,0,0,0,0]],
	"LH":[[10,8,10,4,4],[0,0,0,0,0]],
	"PK":[[10,8,10,4,4],[0,0,0,0,0]],
	"PS":[[10,8,10,4,4],[0,0,0,0,0]],
	"SHP":[[10,8,10,4,4],[0,0,0,0,0]],
	"SH":[[10,8,10,4,4],[0,0,0,0,0]],
	"SP":[[10,8,10,4,4],[0,0,0,0,0]],
	"WB":[[10,8,10,4,4],[0,0,0,0,0]],
	"WWG":[[10,8,10,4,4],[0,0,0,0,0]],
	"TF":[[10,8,10,4,4],[0,0,0,0,0]],
	"PF":[[10,8,10,4,4],[0,0,0,0,0]],
	"PO":[[10,8,10,4,4],[0,0,0,0,0]],
	"FO":[[10,8,10,4,4],[0,0,0,0,0]],
	};
	
Element.prototype.add = function(number) {
		this.count += number;
	}
	
Element.prototype.getCount = function() {
	return this.count;
}
	
Element.prototype.isHidden = function() {
		return this.hidden;
	}

Element.prototype.setHidden = function(hidden) {
		this.hidden = hidden;
	}

Element.prototype.isNoShooting = function() {
		return this.noShooting;
	}

Element.prototype.setNoShooting = function(noShooting) {
		this.noShooting = noShooting;
	}

Element.prototype.isWarDogs = function() {
		return this.warDogs;
	}

Element.prototype.setWarDogs = function(warDogs) {
		this.warDogs = warDogs;
	}

Element.prototype.canBeForCamp = function() {
		return false;
	}
	
Element.prototype.isBaggage = function() {
	if (this.type == "BGE") {
		return true;
	} else {
		return false;
	}
}

Element.prototype.isArmyBaggage = function() {
	if (this.isArmyBaggage) {
		return true;
	} else {
		return false;
	}
}

Element.prototype.equals = function(e) {
		if (this.type.valueOf() == e.getType().valueOf()
			&& this.grade.valueOf() == e.getGrade().valueOf()
			&& this.training.valueOf() == e.getTraining().valueOf()) {
			return true;
		} else {
			return false;
		}
	}

Element.prototype.getCondensedDesc = function() {
		var s = this.getTrainingDesc();
		s += "\n";
		s += this.getTypeDesc();
		s += "(";
		s += this.getGradeDesc();
		s += ")";
		if (this.isGeneral()) {
			s += " " + this.getGeneralDescShort();
		}
		if (this.isChariot()) {
			s += " ch";
		}
		if (this.isDbe()) {
			s += " dbe";
		}
		if (this.isMountedInfantry()) {
			s += " m";
		}
		if (this.isForCamp()) {
			s += " for camp";
		}
		if (this.isArmyBaggage()) {
			s += " army";
		}
		if (this.isWarDogs()) {
			s += " wardog";
		}
		if (this.isNoShooting()) {
			s += " not shooting";
		}
		if (this.isGateway()) {
			s += " gate";
		}
		return s;
	}
	
Element.prototype.getDesc = function() {
		var s = this.getTrainingDesc();
		s += " ";
		s += this.getTypeDesc();
		s += "(";
		s += this.getGradeDesc();
		s += ")";
		if (this.isGeneral()) {
			s += " " + this.getGeneralDesc();
		}
		if (this.isChariot()) {
			s += " chariot";
		}
		if (this.isDbe()) {
			s += " double-based";
		}
		if (this.isMountedInfantry()) {
			s += " mounted";
		}
		if (this.isForCamp()) {
			s += " for camp";
		}
		if (this.isArmyBaggage()) {
			s += " army";
		}
		if (this.isWarDogs()) {
			s += " wardog";
		}
		if (this.isNoShooting()) {
			s += " not shooting";
		}
		if (this.isGateway()) {
			s += " gate";
		}
		return s;
	}

Element.prototype.getElementFingerprint = function() {
		var i = this.getType() + this.getGrade() + this.getTraining();
		/*
		if (cInC) {
			i += Definitions.CINC;
		}
		if (sG) {
			i += Definitions.SUBG;
		}
		if (aG) {
			i += Definitions.ALLG;
		}
		if (brilliant) {
			i += Definitions.BRILL;
		}
		if (inert) {
			i += Definitions.INERT;
		}
		if (isChariot()) {
			i += Definitions.CHAR;
		}
		if (isDbe()) {
			i += Definitions.DBE;
		}
		if (isForCamp()) {
			i += Definitions.CAMP;
		}
		if (isArmyBaggage()) {
			i += Definitions.ARMY;
		}
		if (isGateway()) {
			i += Definitions.GATE;
		}
		if (isWarDogs()) {
			i += Definitions.DOGS;
		}
		if (isHidden()) {
			i += Definitions.HO;
		}
		*/
		return i;
	}

Element.prototype.getElementXML = function() {
		var s = "<element training=\"";
		s += this.getTrainingDesc();
		s += "\" type=\"";
		s += this.getTypeDesc();
		s += "\" grade=\"";
		s += this.getGradeDesc();
		s += "\"";
		s += " number=\"" + this.count + "\"";
		if (this.isGeneral()) {
			s += " general=\"" + this.getGeneralDescShort() + "\"";
		}
		if (this.isChariot()) {
			s += " chariot=\"true\"";
		}
		if (this.isDbe()) {
			s += " dbe=\"true\"";
		}
		if (this.isMountedInfantry()) {
			s += " mountedInfantry=\"true\"";
		}
		if (this.isForCamp()) {
			s += " forCamp=\"true\"";
		}
		if (this.isArmyBaggage()) {
			s += " army=\"true\"";
		}
		if (this.isWarDogs()) {
			s += " wardog=\"true\"";
		}
		if (this.isNoShooting()) {
			s += " notShooting=\"true\"";
		}
		if (this.isGateway()) {
			s += " gate=\"true\"";
		}
		if (this.isHidden()) {
			s += " hidden=\"true\"";
		}
		s += ">";
		s += "</element>";
		return s;
	}

Element.prototype.getGeneralCost = function() {
		var ap = 0;
		if (this.isGeneral()) {
			if (this.aG) {
				if (this.training == "IRR") {
					this.ap += 10;
				} else {
					this.ap += 5;
				}
				if (this.isInert()) {
					this.ap -= 25;
				}
			} else {
				if (this.training == "REG") {
					this.ap += 20;
				} else {
					this.ap += 10;
				}
				if (this.isInert()) {
					this.ap -= 75;
				}
			}
			if (this.isBrilliant()) {
				this.ap += 25;
			}
		}
		return ap;
	}
	
Element.prototype.getGeneralDesc = function() {
		var s = "";
		if (this.isBrilliant()) {
			s += "Brilliant ";
		}
		if (this.isInert) {
			s += "Inert ";
		}
		if (this.cInC) {
			s += "C-in-C";
		} else if (this.sG) {
			s += "Sub General";
		} else if (this.aG) {
			s += "Ally General";
		}
		return s;
	}

Element.prototype.getGeneralDescShort = function() {
		var s = "";
		if (this.isBrilliant) {
			s += "B ";
		}
		if (this.isInert) {
			s += "I ";
		}
		if (this.cInC) {
			s += "C-in-C";
		} else if (this.sG) {
			s += "SG";
		} else if (this.aG) {
			s += "AG";
		}
		return s;
	}
	
Element.prototype.getGrade = function() {
		return this.grade;
	}
	
Element.prototype.getGradeDesc = function() {
		switch (this.getGrade().valueOf()) {
		case "SUP":
			return "S";
		case "ORD":
			return "O";
		case "FST":
			return "F";
		case "INF":
			return "I";
		case "EXE":
			return "X";
		}
		return "";
	}
	
Element.prototype.getTraining = function() {
		return this.training;
	}
	
Element.prototype.getTrainingDesc = function() {
		if (this.getTraining().valueOf() == "REG") {
			return "Reg";
		} else if (this.getTraining().valueOf() == "IRR") {
			return "Irr";
		} else {
			return "";
		}
		
	}
	
Element.prototype.isArmyBaggage = function() {
		return this.isArmyBaggage;
	}
	
Element.prototype.isBrilliant = function() {
		return this.brilliant;
	}

Element.prototype.isChariot = function() {
		return this.chariot;
	}
	
Element.prototype.isCinC = function() {
		return this.cInC;
	}
	
Element.prototype.isDbe = function() {
		return this.dbe;
	}
	
Element.prototype.isForCamp = function() {
		return this.forCamp;
	}
	
Element.prototype.isGeneral = function() {
		if (this.cInC || this.sG || this.aG) {
			return true;
		} else {
			return false;
		}
	}
	
Element.prototype.isInert = function() {
		return this.inert;
	}
	
Element.prototype.isMounted = function() {
		return false;
	}
	
Element.prototype.setArmyBaggage = function(isArmyBaggage) {
		this.isArmyBaggage = isArmyBaggage;
	}
	
Element.prototype.setBrilliant = function(brilliant) {
		this.brilliant = brilliant;
	}
	
Element.prototype.setChariot = function(chariot) {
		this.chariot = chariot;
	}

Element.prototype.setDoubleBased = function(doubleBased) {
		this.dbe = doubleBased;
	}

Element.prototype.setForCamp = function(forCamp) {
		this.forCamp = forCamp;
	}
	
Element.prototype.setGeneralByType = function(typeOfGeneral) {
		switch (typeOfGeneral.valueOf()) {
			case "CINC":
				this.cInC = true;
				break;
			case "SUBG":
				this.sG = true;
				break;
			case "ALLG":
				this.aG = true;
				break;
		}
	}
	
Element.prototype.setGeneralByDesc = function(shortDesc) {
			if (shortDesc == "C-in-C") {
				this.setGeneral("CINC");
				this.setBrilliant(false);
				this.setInert(false);
			} else if (shortDesc == "B C-in-C") {
				this.setGeneral("CINC");
				this.setBrilliant(true);
				this.setInert(false);
			} else if (shortDesc == "I C-in-C") {
				this.setGeneral("CINC");
				this.setBrilliant(false);
				this.setInert(true);
			} else if (shortDesc == "SG") {
				this.setGeneral("SUBG");
				this.setBrilliant(false);
				this.setInert(false);
			} else if (shortDesc == "B SG") {
				this.setGeneral("SUBG");
				this.setBrilliant(true);
				this.setInert(false);
			} else if (shortDesc == "AG") {
				this.setGeneral("ALLG");
				this.setBrilliant(false);
				this.setInert(false);
			} else if (shortDesc == "B AG") {
				this.setGeneral("ALLG");
				this.setBrilliant(true);
				this.setInert(false);
			} else if (shortDesc == "I AG") {
				this.setGeneral("ALLG");
				this.setBrilliant(false);
				this.setInert(true);
			}
	}
	
Element.prototype.setGradeByName = function(grade) {
		this.grade = grade;
	}
	
Element.prototype.setGradeByLetter = function(grade) {
		if (grade.toUpperCase().trim() == "S") {
			this.grade = "SUP";
		} else if (grade.toUpperCase().trim() == "O") {
			this.grade = "ORD";
		} else if (grade.toUpperCase().trim() == "F") {
			this.grade = "FST";
		} else if (grade.toUpperCase().trim() == "I") {
			this.grade = "INF";
		} else if (grade.toUpperCase().trim() == "X") {
			this.grade = "EXE";
		}
	}

Element.prototype.setInert = function(inert) {
			this.inert = inert;
	}
	
Element.prototype.setTraining = function(training) {
		if (training.toUpperCase().trim() == "REG") {
			this.training = "REG";
		} else if (training.toUpperCase().trim() == "IRR") {
			this.training = "IRR";
		}
	}
	
Element.prototype.setType = function(type) {
		this.type = type;
	}
	
Element.prototype.getType = function() {
		return this.type;
	}
Element.prototype.getTypeDesc = function() {
		return this.type;
	}

Element.prototype.setMountedInfantry = function(mountedInfantry) {
		//over-ride this for mounted and train
		this.mountedInfantry = mountedInfantry;
	}
	
Element.prototype.isMountedInfantry = function() {
		//over-ride this for mounted and train
		return this.mountedInfantry;
	}
	
Element.prototype.canBeMountedInfantry = function() {
		//over-ride this for mounted and train
		switch (type) {
			case "EL":
			case "KN":
				return false;
			default:
				return true;
		}
	}
	
Element.prototype.setGateway = function(gateway) {
		this.gateway = gateway;
	}
	
Element.prototype.isGateway = function() {
		return this.gateway;
	}
	
Element.prototype.canBeGateway = function() {
		// over-ride this for TF and PF
		switch (this.type) {
			case "TF":
			case "PF":
				return true;
			default:
				return false;
		}
	}
	
Element.prototype.canBeNoShooting = function() {
		// over-ride this for Bts
		switch (this.type) {
			case "BTS":
				return true;
			default:
				return false;
		}
	}
	
Element.prototype.canBeHidden = function() {
		// over-ride for PO
		switch (this.type) {
			case "PO":
				return true;
			default:
				return false;
		}
	}
	
Element.prototype.canBeWarDogs = function() {
		// over-ride for Wb(F)
		switch (this.type) {
			case "WB":
				if (this.getGrade() == "FST") {
					return true;
				} else {
					return false;
				}
			default:
				return false;
		}
	}
Element.prototype.canBeGeneral = function() {
		// over-ride for 
		switch (this.type) {
			case "ART":
			case "SHP":
			case "GAL":
			case "BTS":
			case "TF":
			case "PF":
			case "PO":
			case "FO":
				return false;
			case "BGE":
				if (this.getGrade() == "SUP") {
					return true;
				} else {
					return false;
				}
			default:
				return true;
		}
	}

Element.prototype.canBeDoubleBased = function() {
		// over-ride for various
		switch (this.type) {
			case "KN":
			case "CV":
			case "BD":
			case "BW":
			case "AX":
				return true;
			default:
				return false;
		}
	}

Element.prototype.canBeChariot = function() {
		// over-ride for Cv and Kn
		switch (this.type) {
			case "CV":
			case "KN":
				return true;
			default:
				return false;
		}
	}

Element.prototype.getAllowedGradesAndTraining = function() {
	return this.allowedGradesAndTraining[this.type];
}
Element.prototype.getTrainingPointer = function() {
	if (this.training == "REG") {
		return 0;
	} else {
		return 1;
	}
}
Element.prototype.getGradePointer = function() {
	switch (this.grade) {
		case "SUP":
			return 0;
		case "ORD":
			return 1;
		case "FST":
			return 2;
		case "INF":
			return 3;
		case "EXE":
			return 4;
	}
}
Element.prototype.getAP = function() {
	var ap = this.apValues[this.type][this.getTrainingPointer()][this.getGradePointer()];
	ap += this.getGeneralCost();
	return ap;
}
Element.prototype.getME = function() {
	if (this.isGeneral()) {
		return 4;
	}
	var me = this.meValues[this.type][this.getTrainingPointer()][this.getGradePointer()];
	return me;
}


function Artillery() {
	
	Element.call(this);
}

Artillery.prototype = new Element();


Artillery.prototype.getAP = new function() {
		switch (this.grade) {
		case "SUP":
			return 10;
		case "ORD":
			return 8;
		case "FST":
			return 10;
		case "INF":
			return 4;
		case "EXE":
			return 4;
		default:
			return 8;
		}
	}
	
Artillery.prototype.getME = new function() {
		if (this.grade == "EXE") {
			return 0;
		} else {
			return 1;
		}
	}


Artillery.prototype.setMountedInfantry = new function(mountedInfantry) {
		//over-ride this for mounted and train
		this.mountedInfantry = false;
	}
	
	this.canBeMountedInfantry = new function() {
		//over-ride this for mounted and train
		return false;
	}