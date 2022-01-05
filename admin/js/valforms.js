var errCSS = "background-color:#FCC;color:red;";
var okkCSS = "background-color:#FFF;color:black;";

var reqEle, reqEmail, reqInt, reqMon;

reqEle 		= new Array();	
reqEmail	= "";
reqInt		= "";
reqMon		= "";

function checkForm() { 

	OK = true; 
	
	if (reqEmail !=="") {
		OK = checkEmail(reqEmail);	
	}


	if (OK && reqInt !=="") {
		OK = checkInt();	
	}

	if (OK && reqMon !== "") {
		OK = checkMoney();
	}
	
	
	if (OK) {
		
		for (var i=0; i<reqEle.length; i++) { 
			
			elem = document.getElementById(reqEle[i]);
			elem.style.cssText = okkCSS;
		
			if (elem.type=="text" || elem.type=="textarea") { 
				if (elem.value =="") { 
					elem.style.cssText = errCSS;
					OK = false; 
				}
			} else if (elem.type=="select" || elem.type=="select-one") { 
				if (elem.selectedIndex < 0) { 
					elem.style.cssText = errCSS;
					OK = false; 
				} 
			} else if (elem.type=="checkbox") {
				if (elem.checked != true) { 
					elem.style.cssText = errCSS;
					OK = false; 
				}
			}
		}
	}
	
	return(OK); 
}
			
function checkEmail(f) {
	
	document.getElementById(f).style.cssText = okkCSS;
		
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	var address = document.getElementById(f).value;
	
	if(reg.test(address) == false) {			
		alert('Introduzca una dirección de correo electrónico');
		document.getElementById(f).focus();
		document.getElementById(f).style.cssText = errCSS;
		return false;
	} else {	 		
		return true;			
	}
}

function checkInt() {
	
	var re = /^\d{1,6}$/;
	
	OK = true; 
	
	for (var i=0; i<reqInt.length; i++) { 
		
		elem = document.getElementById(reqInt[i]);
		elem.style.cssText = okkCSS;
	
		if (elem.value.toUpperCase()=="ND") {
			elem.value = 999999;
		}
		
		if (!re.test(elem.value)) {
			alert("Formato de número no válido \nIntroduce valor de 0 a 999999.");
			elem.focus();
			elem.style.cssText = errCSS;
			OK = false;
			break;
		} 
	
	}
	
	return(OK); 
	
}

function checkMoney() {
		
	var re = /^\d{1,5}(\.\d{0,2})?$/;
	
	OK = true; 
	
	for (var i=0; i<reqMon.length; i++) { 
		
		elem = document.getElementById(reqMon[i]);
		elem.style.cssText = okkCSS;
	
		if (!re.test(elem.value)) {
			alert("Formato de número no válido \nUsar punto para decimales - de 0.00 a 99999.99");
			elem.focus();
			elem.style.cssText = errCSS;
			OK = false;
			break;
		} 
	
	}
	
	return(OK); 
	
}