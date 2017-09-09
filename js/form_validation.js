var $ = function(id) {
    return document.getElementById(id);
}

var pattern = /^\d{3}.\d{3}.\d{4}/;//regex called globally

function showInput() {
    $('phone-number-display').innerHTML = 
                $("phone_number").value;
}


function contactUs() {
	var emailAddress1 = $("email_address1").value;
	var emailAddress2 = $("email_address2").value;
	var isValid = true;
	
	if (emailAddress1 === "") { 
		$("email_address1_error").firstChild.nodeValue = "This field is required.";
		isValid = false;
	} else { $("email_address1_error").firstChild.nodeValue = ""; } 

	if (emailAddress1 !== emailAddress2) { 
		$("email_address2_error").firstChild.nodeValue = "This entry must equal first entry.";
		isValid = false;
	} else { $("email_address2_error").firstChild.nodeValue = ""; }     
        
	if ($("first_name").value === "") {
		$("first_name_error").firstChild.nodeValue = 
                        "This field is required.";
		isValid = false;
	} else { $("first_name_error").firstChild.nodeValue = ""; }
    
    if($("contact_form").contains($("phone_number"))){
        if ($("phone_number").value === "") {
                $("phone_number_error").firstChild.nodeValue = 
                                "This field is required.";
                isValid = false;
        } else if (!(pattern.test($("phone_number").value))){
             $("phone_number_error").firstChild.nodeValue = "Error must contain . or -";
            isValid=false;
        }
        else { 
            $("phone_number_error").firstChild.nodeValue = "";  
        }         
    }
    
    if($("contact_form").contains($("textarea"))){
        if($("textarea").value === "") {
        $("textarea_error").firstChild.nodeValue = "This field is required";
        isValid = false;
        } else {$("textarea_error").firstChild.nodeValue = "";}
    }
    

	
	if (isValid) {
		$("contact_form").submit();
        $("phone-number-display").innerHTML = ($("phone_number").value);
            // Store your data.
        
        
        sessionStorage.setItem("data1", $("phone_number").value);
        
        sessionStorage.setItem("data2", $("first_name").value);
        
        sessionStorage.setItem("data3", emailAddress1);
    
    
	}
    
    
}

window.onload = function() {
    $("send").onclick = contactUs;
    $("email_address1").focus();
}
