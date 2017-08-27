"use strict";

var $ = function(id) {
    return document.getElementById(id);
};

function calculateCosts(marketing, income) {
		var ratePerHour=100;
        var standardHours = 10;
        var total;
        var cost = 12000;
        var companyIncome;
        var chargeBasedOffIncome = .2;
        var discount = .1;
        
        total = (income * chargeBasedOffIncome) +(marketing + (ratePerHour * standardHours));
        
        
        if(total >= cost) {
            total = total - (total * discount);
            return total;
        }
        
        else {
            return total;
        }
};

var processEntries = function() {
	var marketing = parseFloat($("marketing").value);
	var income = parseFloat($("annual_income").value);
	var isValid = false;


    if (isNaN(marketing)) {
        $("marketing_error").firstChild.nodeValue = "Merketing costs needs to be a number";
    } 

    else if (isNaN(income)) {
         $("income_error").firstChild.nodeValue = "Income needs to be a number";
    }


    else if (income <= 0) {
         $("income_error").firstChild.nodeValue = "Income can't be 0 or less";
    }

    else if (marketing <= 0) {
         $("marketing_error").firstChild.nodeValue = "Marketing can't be 0 or less";
    }

    else {
        $("quote").value = calculateCosts(marketing, income);
        isValid = true;
    }

   	if(isValid) {
		    $("marketing_error").firstChild.nodeValue ="";
		    $("income_error").firstChild.nodeValue = "";
	}
};

window.onload = function() {
	$("calculate").onclick = processEntries;
	$("marketing").focus();
}

