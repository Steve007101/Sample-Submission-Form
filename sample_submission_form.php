<?php
    require_once("../wp-load.php");
?>
<!-- Authored by Steven Perry-->
<!--
    This is the sample submission form.
    
    The Javascript functions hide and show elements based on what's selected 
    (and enable/disable them so as not to break the form validation). Potentially could
    implement hiding/showing photo uploads based on how many a user wants.
    HTML5 Form input validation for text with regex/required. 
    Google API for pulling location data for origin/recipient company.
    Pictures validate for being required for 1, common image types and under 16 MB big.
-->

<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Submission</title>
</head>
<!-- google api script section --> 
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=ThisIsADummyKeyValue&libraries=places"></script>
<script type="text/javascript">
// google API for origin form autofill
        google.maps.event.addDomListener(window, 'load', function () {
            var places = new google.maps.places.Autocomplete(document.getElementById('origin_company'));
            google.maps.event.addListener(places, 'place_changed', function () {
                var place = places.getPlace();
                // for assigning individual fields with the information
                document.getElementById("origin_address").value = ""; //need to clear values since I use +=
                                                                    // because street# and name are seperate
                for (var i = 0; i < place.address_components.length; i++) {
                    for (var j = 0; j < place.address_components[i].types.length; j++) {
                        if(place.address_components[i].types[j] == "floor") {
                            document.getElementById("origin_address2").value = "Floor " + place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "street_number") {
                            document.getElementById("origin_address").value += place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "route") {
                            document.getElementById("origin_address").value += " "+place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "locality") {
                            document.getElementById("origin_city").value = place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "administrative_area_level_1") {
                            document.getElementById("origin_region").value = place.address_components[i].short_name; // for appreviated state
                        }
                        if(place.address_components[i].types[j] == "country") {
                            document.getElementById("origin_country").value = place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "postal_code") {
                            document.getElementById("origin_zip").value = place.address_components[i].long_name;
                        }
                        // there is also a postal_code_suffix I could pull
                    }
                }
                // these values aren't in the address_components section of the array
                // only need to check if they are defined
                if (place.international_phone_number != undefined) {
                    document.getElementById("origin_phone").value = place.international_phone_number;
                }
                if (place.name != undefined) {
                    document.getElementById("origin_company").value = place.name;
                }
            });
        });
// google API for recipient form autofill
google.maps.event.addDomListener(window, 'load', function () {
            var places = new google.maps.places.Autocomplete(document.getElementById('recipient_company'));
            google.maps.event.addListener(places, 'place_changed', function () {
                var place = places.getPlace();
                // for assigning individual fields with the information
                document.getElementById("recipient_address").value = ""; //need to clear values since I use +=
                                                                    // because street# and name are seperate
                for (var i = 0; i < place.address_components.length; i++) {
                    for (var j = 0; j < place.address_components[i].types.length; j++) {
                        if(place.address_components[i].types[j] == "floor") {
                            document.getElementById("recipient_address2").value = "Floor " + place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "street_number") {
                            document.getElementById("recipient_address").value += place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "route") {
                            document.getElementById("recipient_address").value += " "+place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "locality") {
                            document.getElementById("recipient_city").value = place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "administrative_area_level_1") {
                            document.getElementById("recipient_region").value = place.address_components[i].short_name; // for appreviated state
                        }
                        if(place.address_components[i].types[j] == "country") {
                            document.getElementById("recipient_country").value = place.address_components[i].long_name;
                        }
                        if(place.address_components[i].types[j] == "postal_code") {
                            document.getElementById("recipient_zip").value = place.address_components[i].long_name;
                        }
                        // there is also a postal_code_suffix I could pull
                    }
                }
                // these values aren't in the address_components section of the array
                // only need to check if they are defined
                if (place.international_phone_number != undefined) {
                    document.getElementById("recipient_phone").value = place.international_phone_number;
                }
                if (place.name != undefined) {
                    document.getElementById("recipient_company").value = place.name;
                }
            });
        });
//              // for testing returned values
//                 for (var i = 0; i < place.address_components.length; i++) {
//                     for (var j = 0; j < place.address_components[i].types.length; j++) {
//                         mesg += place.address_components[i].types[j] + ": " + place.address_components[i].long_name + "\n";
//                     }
//                 }
//                 alert(mesg);
    // need to call a php file to figure out trader name and email
    var trader_set = false;
    function getTraderInfo() {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("trader_data").innerHTML = this.responseText;
                if (document.getElementById("trader_debug_info") != null)
                    console.log(document.getElementById("trader_debug_info").value);
                if (document.getElementById("trader_name") != null 
                && document.getElementById("trader_name").value != ""
                && document.getElementById("trader_email") != null 
                && document.getElementById("trader_email").value != "") {
                    document.getElementById("trader_display").innerHTML = document.getElementById("trader_name").value;
                    trader_set = true;
                }
            }
        };
        xmlhttp.open("GET", "get_user.php?", true);
        xmlhttp.send();
    }

    var submit_in_progress = false;
    function submitSample() {
        if (!trader_set) {
            alert ("Unable to Obtain Trader Name");
            return false;
        }
        if (submit_in_progress) {
            return false;
        } else {
            submit_in_progress = true;
        }
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "upload_submission.php"); 
        xhr.onload = function(event){
            if (event.target.response.search("Success") != -1) {
                console.log("Response Text:" + event.target.response);
                clearForm();
                submit_in_progress = false;
                alert("Sample Submission Successfully Uploaded");
            } else {
                console.log("Response Text:" + event.target.response);
                submit_in_progress = false;
                alert("Sample Submission Failed to Upload");
            }
        }; 
        var formData = new FormData(document.getElementById("submission_form")); 
        xhr.send(formData);
        return false; // so the form isn't actually "submitted"
    }
    
    function clearForm() {
        document.getElementById("pic_file1").value = "";
        document.getElementById("pic_file2").value = "";
        document.getElementById("pic_file3").value = "";
        document.getElementById("pic_file4").value = "";
        document.getElementById("pic_file5").value = "";

        /* document.getElementById("sample_type").value = "";
        document.getElementById("hazardous_condition").checked = false;
        document.getElementById("flammable_condition").checked = false;
        document.getElementById("lab_oxide").checked = false;
        document.getElementById("lab_precious").checked = false;
        document.getElementById("sample_origin").value = "";
        document.getElementById("sample_recipient").value = "";
        document.getElementById("sample_description").value = "";
        document.getElementById("origin_company").value = "";
        document.getElementById("origin_co").value = "";
        document.getElementById("origin_address").value = "";
        document.getElementById("origin_address2").value = "";
        document.getElementById("origin_city").value = "";
        document.getElementById("origin_region").value = "";
        document.getElementById("origin_zip").value = "";
        document.getElementById("origin_country").value = "";
        document.getElementById("origin_phone").value = "";
        document.getElementById("origin_email").value = "";
        document.getElementById("origin_description").value = "";
        document.getElementById("origin_shipping").value = "";
        document.getElementById("recipient_company").value = "";
        document.getElementById("recipient_co").value = "";
        document.getElementById("recipient_address").value = "";
        document.getElementById("recipient_address2").value = "";
        document.getElementById("recipient_city").value = "";
        document.getElementById("recipient_region").value = "";
        document.getElementById("recipient_zip").value = "";
        document.getElementById("recipient_country").value = "";
        document.getElementById("recipient_phone").value = "";
        document.getElementById("recipient_email").value = "";
        document.getElementById("recipient_date").value = "";
        document.getElementById("recipient_description").value = "";
        document.getElementById("recipient_shipping").value = ""; */

        var input_elements = document.getElementsByTagName("input");
        for (var i=0; i < input_elements.length; i++) {
            if (input_elements[i].type == "text") {
                input_elements[i].value = "";
            }
            if (input_elements[i].type == "number") {
                input_elements[i].value = "";
            }
            if (input_elements[i].type == "checkbox") {
                    input_elements[i].checked = false;
            }
            if (input_elements[i].type == "radio") {
                if (input_elements[i].value == "0") {
                    input_elements[i].checked = true;
                }
            }
        }
        var select_elements = document.getElementsByTagName("select");
        for (var i=0; i < select_elements.length; i++) {
                select_elements[i].value = "";
        }
        var textarea_elements = document.getElementsByTagName("textarea");
        for (var i=0; i < textarea_elements.length; i++) {
            textarea_elements[i].value = "";
        }
        showHideAll();
    }

    // I still can't get these functions to show/hide stuff if the user
    // hits the back button to get back here
    window.addEventListener('popstate', function (e) {
    var state = e.state;
    if (state !== null) {
        showHideAll();
    } }); 
    function showHideAll() {
        sampleOriginFormToggle();
        labAndSampleRecipientFormToggle();
        recipientDateToggle();
        labTCPLToggle();
        labNotesToggle();
    }
 
    window.onload = function() {
        getTraderInfo();
        showHideAll();
    }
    
    // verify file sizes aren't too big
    var uploadField1 = document.getElementById("pic_file1");
    var uploadField2 = document.getElementById("pic_file2");
    var uploadField3 = document.getElementById("pic_file3");
    var uploadField4 = document.getElementById("pic_file4");
    var uploadField5 = document.getElementById("pic_file5");

    uploadField1.onchange = function() {
        // check if file size is over 16MB
    if(this.files[0].size > 16777216){
       alert("Photo 1 is too big. Only up to 16 MB allowed");
       this.value = "";
        };
    };
    uploadField2.onchange = function() {
        // check if file size is over 16MB
    if(this.files[0].size > 16777216){
       alert("Photo 2 is too big. Only up to 16 MB allowed");
       this.value = "";
        };
    };
    uploadField3.onchange = function() {
        // check if file size is over 16MB
    if(this.files[0].size > 16777216){
       alert("Photo 3 is too big. Only up to 16 MB allowed");
       this.value = "";
        };
    };
    uploadField4.onchange = function() {
        // check if file size is over 16MB
    if(this.files[0].size > 16777216){
       alert("Photo 4 is too big. Only up to 16 MB allowed");
       this.value = "";
        };
    };
    uploadField5.onchange = function() {
        // check if file size is over 16MB
    if(this.files[0].size > 16777216){
       alert("Photo 5 is too big. Only up to 16 MB allowed");
       this.value = "";
        };
    };

    function sampleOriginFormToggle(){
        sample_origin = document.getElementById("sample_origin");
        // this is for changing if it's viewable
        sample_origin_form = document.getElementById("sample_origin_form");
        // enabling/disabling elements necessary or the html5 required check will break form
        sample_origin_form_nodes = sample_origin_form.getElementsByTagName('*');

        if (sample_origin.value == "Supplier" || sample_origin.value == "Customer"){
            sample_origin_form.style.display = "initial";

            for (node of sample_origin_form_nodes){
                node.disabled = false;
            }
            // need to engage country/region and zip being required check
            selectOriginCountry(document.getElementById("origin_country").value);
        }
        else if (sample_origin.value == "Warehouse" || sample_origin.value == ""){
            sample_origin_form.style.display = "none";

            for (node of sample_origin_form_nodes){
                node.disabled = true;
            }
        }
    }
    function labAndSampleRecipientFormToggle() {
        sample_recipient = document.getElementById("sample_recipient");
        // these are for changing if they're viewable
        sample_recipient_form = document.getElementById("sample_recipient_form");
        lab_info = document.getElementById("lab_info");
        lab_address = document.getElementById("lab_address");
        lab2_address = document.getElementById("lab2_address");
        // enabled/disabling elements necessary or the html5 required check will break form
        sample_recipient_form_nodes = sample_recipient_form.getElementsByTagName('*');

        if (sample_recipient.value == "St Louis Testing (Lab)"){
            lab_info.style.display = "initial";
            lab_address.style.display = "initial";
            lab2_address.style.display = "none";
            sample_recipient_form.style.display = "none";
            
            for (node of sample_recipient_form_nodes){
                node.disabled = true;
            }
            recipientDateToggle();
        }
        else if (sample_recipient.value == "UMSL Labs") {
            lab_info.style.display = "initial";
            lab_address.style.display = "none";
            lab2_address.style.display = "initial";
            sample_recipient_form.style.display = "none";
            
            for (node of sample_recipient_form_nodes){
                node.disabled = true;
            }
            recipientDateToggle();

        }
        else if (sample_recipient.value == "Supplier" || sample_recipient.value == "Customer"){
            lab_info.style.display = "none";
            sample_recipient_form.style.display = "initial";

            for (node of sample_recipient_form_nodes){
                node.disabled = false;
            }
            recipientDateToggle();
            // need to engage country/region and zip being required check
            selectRecipientCountry(document.getElementById("recipient_country").value);
        }
        else if (sample_recipient.value == "") {
            lab_info.style.display = "none";
            sample_recipient_form.style.display = "none";
            for (node of sample_recipient_form_nodes){
                node.disabled = true;
            }
        }
    }

    function recipientDateShow() {
        recipient_date = document.getElementById("recipient_date");
        recipient_date.value = "";
        recipient_date.disabled = false;

    }
    function recipientDateHide() {
        recipient_date = document.getElementById("recipient_date");
        recipient_date.value = "";
        recipient_date.disabled = true;
    }
    function recipientDateToggle() {
        recipient_date = document.getElementById("recipient_date");
        if (recipient_date.value == "") {
            recipient_date.disabled = true;
            recipient_date_radio_no.checked = true;

        }
        else {
            recipient_date.disabled = false;
            recipient_date_radio_yes.checked = true;
        }
    }

    function labTCPLToggle() {
        if (document.getElementById("lab_tcpl_toggle").checked == true) {
            document.getElementById("lab_tcpl_div").style = "display: initial;";
        }
        else {
            document.getElementById("lab_tcpl_div").style = "display: none;";
        }
    }

    function labNotesToggle() {
        if (document.getElementById("lab_notes_toggle").checked == true) {
            document.getElementById("lab_notes").disabled = false;
            document.getElementById("lab_notes_div").style = "display: initial;";
        }
        else {
            document.getElementById("lab_notes").disabled = true;
            document.getElementById("lab_notes_div").style = "display: none;";
        }
    }

    function selectOriginCountry(country) {
        if (country != "United States" && country != "US Minor Outlying Islands" &&
        country != "Canada") {
            document.getElementById("origin_region").required = false;
            document.getElementById("origin_zip").required = false;
        } else {
            document.getElementById("origin_region").required = true;
            document.getElementById("origin_zip").required = true;
        }
    }

    function selectRecipientCountry(country) {
        if (country != "United States" && country != "US Minor Outlying Islands" &&
        country != "Canada") {
            document.getElementById("recipient_region").required = false;
            document.getElementById("recipient_zip").required = false;
        } else {
            document.getElementById("recipient_region").required = true;
            document.getElementById("recipient_zip").required = true;
        }
    }
</script>
<!-- CSS Section -->
<style>
* {
    font-family: "Calibri";
}
body {
    background-color: white;
    margin: 0; /* removes white space above navbar */
}
#logo {
    display: block;
    margin-left: auto;
    margin-right: auto;
    margin-top: 5px;
    width: 250px;
}
#title {
    text-align: center;
}
/* Grid Layout used, only have to specify
items that don't fill up a single cell with
grid row/column information*/
#grid_container {
  display: grid;
  grid-template-columns: 180px 200px 200px;
  grid-gap: 10px;
  padding: 0px;
  justify-content: center;
}
#picture_div {
    grid-column-start: 3;
    grid-column-end: 3;
    grid-row-start: 2;
    grid-row-end: 4;
}
#sample_description_div {
    grid-column-start: 1;
    grid-column-end: 3;
}
#sample_origin_form {
    grid-row: 4 / span 3;
    grid-column: 1 / span 3;

}
#lab_info {
    grid-column: 1 / span 3;
}
#sample_recipient_form {
    grid-column: 1 / span 3;
    /* Causes issues with Submit button */
    /* grid-row: 7 / span 3; */
}
#submit_button {
    width: 60px;
    height: 25px;
    font-size: 15px;
}
/* prevent resizing of textareas, makes form look weird if users make it too big */
textarea {
  resize: none;
}

/* to allow the google API more room to show results for auto-fill */
#origin_company {
    width: 400px;
}
#recipient_company {
    width: 400px;
}
/* navbar styling */
.navmenu {
    margin: 0;
    padding: 0;
    overflow: hidden;
    background-color: #333;
}
.navlink {
    display: flex;
    justify-content: center;
}
.navlink a {
    display: block;
    color: white;
    text-align: center;
    padding: 4px 12px;
    text-decoration: none;
}
.navlink a:hover:not(.active) {
    background-color: #111;
}
.active {
    background-color: #4CAF50;
}
/* Notes to self: .class #id * for all p for elements of type p */
/**
 * Disabled state
 */
button.disabled,
button[disabled] {
	box-shadow: none;
	cursor: not-allowed;
	opacity: 0.5;
	pointer-events: none;
}
</style>
<body>
<!-- navbar -->
<div class="navmenu">
    <div class="navlink">
        <a href="https://company.website.com/">Home</a>
        <a class="active" href="https://company.website.com/sample-log-forms/sample_submission_form.php">Submission Form</a>
        <a href="https://company.website.com/sample-log-forms/update_page_loader.php">Search</a>
    </div>
</div>
    <img id="logo" src="./image/Logo-Blackv2.png" alt="Logo Black">
    <div id="title"><h1>Sample Submission Form</h1></div>
    <form id="submission_form" onsubmit="return submitSample()">
        <div id="grid_container">
            <div class="trader_div">
                <h4 onclick="getTraderInfo()">Trader Name</h4>
                <p id="trader_display">Loading...</p>
                <div id="trader_data">
                </div>
            </div>
            <div id="sample_type_div">
                <label class="field_label" for="sample_type"><h4>Type of Sample*</h4></label>
                <select class="drop_list" name="sample_type"id="sample_type" required>
                    <option value="">Please Select</option>
                    <option value='Samples To Be Mailed'>Samples To Be Mailed</option>
                    <option value='Lab Testing Information'>Lab Testing Information</option>
                    <option value='Special Lab Testing Information'>Special Lab Testing Information</option>
                </select>
            </div>
            <div id="shipping_conditions_div">
                <h4>Shipping Conditions</h4>
                <input type="hidden" name="hazardous_condition" value ="0">
                <input class="checkbox" type="checkbox" id="hazardous_condition" name="hazardous_condition" value="1">
                <label for="hazardous_condition">Hazardous</label>
                <input type="hidden" name="flammable_condition" value ="0">
                <input class="checkbox" type="checkbox" id="flammable_condition" name="hazardous_condition" value="1">
                <label for="flammable_condition">Flammable</label>
            </div>


            <div id="sample_origin_div">
                    <label class="field_label" for="sample_origin"><h4>Sample Origin*</h4></label>
                    <select class="drop_list" name="sample_origin"id="sample_origin" onclick="sampleOriginFormToggle()" required>
                        <option value="">Please Select</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Warehouse">Warehouse</option>
                        <option value="Customer">Customer</option>
                    </select>
            </div>
            <div id="sample_recipient_div">
                <label class="field_label" for="sample_recipient"><h4>Sample Recipient*</h4></label>
                    <select class="drop_list" name="sample_recipient"id="sample_recipient" onclick="labAndSampleRecipientFormToggle()" required>
                        <option value="">Please Select</option>
                        <option value='St Louis Testing (Lab)'>St Louis Testing (Lab)</option>
                        <option value='UMSL Labs'>UMSL Labs</option>
                        <option value='Customer'>Customer</option>
                        <option value='Supplier'>Supplier</option>
                    </select>
            </div>
            <div id="picture_div"> <!-- Need javascript to add/remove photo upload sections, up to 5-->
                <br><br>Upload Photos<br> (Minimum 1 photo)*
                <div id="file1">
                    <span class="close_file" id="close_file1" hidden>remove file</span> <!-- Remove file buttons hidden, unimplemented-->
                    <br>
                    <!-- <label for="pic_file1">Upload Photo*</label> -->
                    <input class="select_file" type="file" id="pic_file1" name="pic_files[]" required
                    accept=".tif,.tiff,.bmp,.jpg,.jpeg,.gif,.png,.eps,.raw">
                </div>
                <div id="file2">
                    <span class="close_file" id="close_file2" hidden>remove file</span>
                    <br>
                    <!-- <label for="pic_file2">Upload Photo 2</label> -->
                    <input class="select_file" type="file" id="pic_file2" name="pic_files[]"
                    accept=".tif,.tiff,.bmp,.jpg,.jpeg,.gif,.png,.eps,.raw">
                </div>
                <div id="file3">
                    <span class="close_file" id="close_file3" hidden>remove file</span>
                    <br>
                    <!-- <label for="pic_file3">Upload Photo 3</label> -->
                    <input class="select_file" type="file" id="pic_file3" name="pic_files[]"
                    accept=".tif,.tiff,.bmp,.jpg,.jpeg,.gif,.png,.eps,.raw">
                </div>
                <div id="file4">
                    <span class="close_file" id="close_file4" hidden>remove file</span>
                    <br>
                    <!-- <label for="pic_file4">Upload Photo 4</label> -->
                    <input class="select_file" type="file" id="pic_file4" name="pic_files[]"
                    accept=".tif,.tiff,.bmp,.jpg,.jpeg,.gif,.png,.eps,.raw">
                </div>
                <div id="file5">
                    <span class="close_file" id="close_file5" hidden>remove file</span>
                    <br>
                    <!-- <label for="pic_file5">Upload Photo 5</label> -->
                    <input class="select_file" type="file" id="pic_file5" name="pic_files[]"
                    accept=".tif,.tiff,.bmp,.jpg,.jpeg,.gif,.png,.eps,.raw">
                </div>
            </div>
            <div id="sample_description_div">
                    <label class="field_label" for="sample_description"><h4>Description of Sample*</h4></label>
                    <textarea class = "text_area" id="sample_description" name="sample_description" 
                    style="height: 115px; width: 360px;" maxlength="500" required></textarea>
            </div>   
            <div id="sample_origin_form" style= "display: none;"> <!-- will be hidden until supplier/customer selected in sample_origin-->
                <h4>Sample Origin Information</h4>
                    <input type="text" class = "text_field" id="origin_company" name="origin_company" placeholder="Origin Company Name*" required disabled
                    pattern="[A-Z]([a-zA-Z0-9]|[- ,@\.#&!])*" title="company name" autocomplete="off" maxlength="64">
                    <br>
                    <input type="text" class = "text_field" id="origin_co" name="origin_co" placeholder="Origin C/O*" required disabled
                    pattern="[\w\-]+((-|\s)[\w\d]+)*" title="person's name" maxlength="64">
                    <input type="text" class = "text_field" id="origin_address" name="origin_address" placeholder="Origin Address Line 1*" required disabled
                    pattern="[\d\w.,\\\-\/]+(\s[\d\w.,\\\-\/]+)*" title="address line" maxlength="128">
                    <input type="text" class = "text_field" id="origin_address2" name="origin_address2" placeholder="Address Line 2" disabled
                    pattern="[\d\w.,\\\-\/]+(\s[\d\w.,\\\-\/]+)*" title="address line" maxlength="64">
                    <br>
                    <input type="text" class = "text_field" id="origin_city" name="origin_city" placeholder="City*" required disabled
                    pattern="([a-zA-Z\u0080-\u024F]+(?:. |-| |'))*[a-zA-Z\u0080-\u024F]*" title="city name" maxlength="64">
                    <input type="text" class = "text_field" id="origin_region" name="origin_region" placeholder="State/ Province/ Region*" required disabled
                    pattern="([a-zA-Z\u0080-\u024F]+(?:. |-| |'))*[a-zA-Z\u0080-\u024F]*" title="region name" maxlength="64">
                    <input type="text" class = "text_field" id="origin_zip" name="origin_zip" placeholder="Zip / Postal Code*" required disabled
                    pattern="[\d\w]+[ -]?[\d\w]*" title="postal code" maxlength="24">
                    <br>
                    <select class="drop_list" name="origin_country" id="origin_country" onchange="selectOriginCountry(this.value)" required disabled>
                        <option value="">Select Country*</option>
                        <option value="Afghanistan">Afghanistan</option>
                        <option value="Åland Islands">Åland Islands</option>
                        <option value="Albania">Albania</option>
                        <option value="Algeria">Algeria</option>
                        <option value="American Samoa">American Samoa</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Anguilla">Anguilla</option>
                        <option value="Antarctica">Antarctica</option>
                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Aruba">Aruba</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaijan">Azerbaijan</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Belgium">Belgium</option>
                        <option value="Belize">Belize</option>
                        <option value="Benin">Benin</option>
                        <option value="Bermuda">Bermuda</option>
                        <option value="Bhutan">Bhutan</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bonaire">Bonaire</option>
                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Bouvet Island">Bouvet Island</option>
                        <option value="Brazil">Brazil</option>
                        <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                        <option value="Brunei Darrussalam">Brunei Darrussalam</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cambodia">Cambodia</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Canada">Canada</option>
                        <option value="Cape Verde">Cape Verde</option>
                        <option value="Cayman Islands">Cayman Islands</option>
                        <option value="Central African Republic">Central African Republic</option>
                        <option value="Chad">Chad</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Christmas Island">Christmas Island</option>
                        <option value="Cocos Islands">Cocos Islands</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Comoros">Comoros</option>
                        <option value="Congo">Congo</option>
                        <option value="Cook Islands">Cook Islands</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Côte dIvoire">Côte dIvoire</option>
                        <option value="Croatia">Croatia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Curaçao">Curaçao</option>
                        <option value="Cyprus">Cyprus</option>
                        <option value="Czech Republic">Czech Republic</option>
                        <option value="Denmark">Denmark</option>
                        <option value="Djibouti">Djibouti</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Dominican Republic">Dominican Republic</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egypt">Egypt</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                        <option value="Eritrea">Eritrea</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Eswatini (Swaziland)">Eswatini (Swaziland)</option>
                        <option value="Ethiopia">Ethiopia</option>
                        <option value="Falkland Islands">Falkland Islands</option>
                        <option value="Faroe Islands">Faroe Islands</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Finland">Finland</option>
                        <option value="France">France</option>
                        <option value="French Guiana">French Guiana</option>
                        <option value="French Polynesia">French Polynesia</option>
                        <option value="French Southern Territories">French Southern Territories</option>
                        <option value="Gabon">Gabon</option>
                        <option value="Gambia">Gambia</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Germany">Germany</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Gibraltar">Gibraltar</option>
                        <option value="Greece">Greece</option>
                        <option value="Greenland">Greenland</option>
                        <option value="Grenada">Grenada</option>
                        <option value="Guadeloupe">Guadeloupe</option>
                        <option value="Guam">Guam</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guernsey">Guernsey</option>
                        <option value="Guinea">Guinea</option>
                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Haiti">Haiti</option>
                        <option value="Heard and McDonald Islands">Heard and McDonald Islands</option>
                        <option value="Holy See">Holy See</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hong Kong">Hong Kong</option>
                        <option value="Hungary">Hungary</option>
                        <option value="Iceland">Iceland</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Iran">Iran</option>
                        <option value="Iraq">Iraq</option>
                        <option value="Ireland">Ireland</option>
                        <option value="Isle of Man">Isle of Man</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japan">Japan</option>
                        <option value="Jersey">Jersey</option>
                        <option value="Jordan">Jordan</option>
                        <option value="Kazakhstan">Kazakhstan</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Kiribati">Kiribati</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                        <option value="Lao">Lao</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Lebanon">Lebanon</option>
                        <option value="Lesotho">Lesotho</option>
                        <option value="Liberia">Liberia</option>
                        <option value="Libya">Libya</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Lithuania">Lithuania</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Macau">Macau</option>
                        <option value="Macedonia">Macedonia</option>
                        <option value="Madagascar">Madagascar</option>
                        <option value="Malawi">Malawi</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Mali">Mali</option>
                        <option value="Malta">Malta</option>
                        <option value="Marshall Islands">Marshall Islands</option>
                        <option value="Martinique">Martinique</option>
                        <option value="Mauritania">Mauritania</option>
                        <option value="Mauritius">Mauritius</option>
                        <option value="Mayotte">Mayotte</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Micronesia">Micronesia</option>
                        <option value="Moldova">Moldova</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Montenegro">Montenegro</option>
                        <option value="Montserrat">Montserrat</option>
                        <option value="Morocco">Morocco</option>
                        <option value="Mozambique">Mozambique</option>
                        <option value="Myanmar">Myanmar</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="New Caledonia">New Caledonia</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Niger">Niger</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Niue">Niue</option>
                        <option value="Norfolk Island">Norfolk Island</option>
                        <option value="North Korea">North Korea</option>
                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                        <option value="Norway">Norway</option>
                        <option value="Oman">Oman</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="Palau">Palau</option>
                        <option value="Palestine, State of">Palestine, State of</option>
                        <option value="Panama">Panama</option>
                        <option value="Papua New Guinea">Papua New Guinea</option>
                        <option value="Paraguay">Paraguay</option>
                        <option value="Peru">Peru</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Pitcairn">Pitcairn</option>
                        <option value="Poland">Poland</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Puerto Rico">Puerto Rico</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Réunion">Réunion</option>
                        <option value="Romania">Romania</option>
                        <option value="Russia">Russia</option>
                        <option value="Rwanda">Rwanda</option>
                        <option value="Saint Barthélemy">Saint Barthélemy</option>
                        <option value="Saint Helena">Saint Helena</option>
                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                        <option value="Saint Lucia">Saint Lucia</option>
                        <option value="Saint Martin">Saint Martin</option>
                        <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                        <option value="Saint Vincent and the Grenadines">Saint Vincent</option>
                        <option value="Samoa">Samoa</option>
                        <option value="San Marino">San Marino</option>
                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Serbia">Serbia</option>
                        <option value="Seychelles">Seychelles</option>
                        <option value="Sierra Leone">Sierra Leone</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Sint Maarten">Sint Maarten</option>
                        <option value="Slovakia">Slovakia</option>
                        <option value="Slovenia">Slovenia</option>
                        <option value="Solomon Islands">Solomon Islands</option>
                        <option value="Somalia">Somalia</option>
                        <option value="South Africa">South Africa</option>
                        <option value="South Georgia">South Georgia</option>
                        <option value="South Korea">South Korea</option>
                        <option value="South Sudan">South Sudan</option>
                        <option value="Spain">Spain</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <option value="Sudan">Sudan</option>
                        <option value="Suriname">Suriname</option>
                        <option value="Svalbard and Jan Mayen Islands">Svalbard Islands</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Switzerland">Switzerland</option>
                        <option value="Syria">Syria</option>
                        <option value="Taiwan">Taiwan</option>
                        <option value="Tajikistan">Tajikistan</option>
                        <option value="Tanzania">Tanzania</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Timor-Leste">Timor-Leste</option>
                        <option value="Togo">Togo</option>
                        <option value="Tokelau">Tokelau</option>
                        <option value="Tonga">Tonga</option>
                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                        <option value="Tunisia">Tunisia</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Turkmenistan">Turkmenistan</option>
                        <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                        <option value="Tuvalu">Tuvalu</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="Uruguay">Uruguay</option>
                        <option value="US Minor Outlying Islands">US Minor Outlying Islands</option>
                        <option value="Uzbekistan">Uzbekistan</option>
                        <option value="Vanuatu">Vanuatu</option>
                        <option value="Venezuela">Venezuela</option>
                        <option value="Vietnam">Vietnam</option>
                        <option value="Virgin Islands, British">Virgin Islands, British</option>
                        <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                        <option value="Wallis and Futuna">Wallis and Futuna</option>
                        <option value="Western Sahara">Western Sahara</option>
                        <option value="Yemen">Yemen</option>
                        <option value="Zambia">Zambia</option>
                        <option value="Zimbabwe">Zimbabwe</option>                
                    </select>
                    <input type="text" class = "text_field" id="origin_phone" name="origin_phone" placeholder="Origin Phone*" required disabled
                    pattern="[+()\- \d]{10,}" title="phone number" maxlength="24">
                    <input type="text" class = "text_field" id="origin_email" name="origin_email" placeholder="Origin Email*" required disabled
                    pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}" title="email" autocomplete="off" maxlength="64">
                    <br>
                        <div>
                            <label class="field_label" for="origin_description">Description of Material* </label>
                            <br>
                            <textarea class = "text_area" id="origin_description" name="origin_description" 
                                style="height: 60px; width: 350px;" maxlength="500" required disabled></textarea>
                        </div>
                        <div>
                            <label class="field_label" for="origin_shipping">Shipping Details* </label>
                            <br>
                            <textarea class = "text_area" id="origin_shipping" name="origin_shipping" 
                                style="height: 60px; width: 350px;" maxlength="500" required disabled></textarea>
        
                        </div>
            </div> <!-- end of sample origin form, hidden by default -->
            <div id="lab_info" style = "display: none;"> <!-- lab info, hidden by default unless picked in sample_recipient-->
                <h4>Sample Recipient Information</h4>
                <div id="lab_address">
                    St Louis Testing Laboratories Inc (Lab)<br>
                    2810 Clark Avenue<br>
                    Saint Louis MO 63103<br><br>
                    Contact Jacob Long<br>
                    (314) 531-8080<br>
                    (314) 531-8085<br>
                    testlab@labinc.com<br>
                </div>
                <div id="lab2_address">
                    UMSL Labs<br>
                    University of Missouri – St. Louis<br>
                    1 University Blvd.<br>
                    Department of Chemistry 309 SLB<br>
                    St. Louis, MO 63121<br><br>
                    Dr. Jack Harms<br>
                    (618) 520-8929<br>
                    harmsjc@umsl.edu<br>
                </div>
                <h4>Lab Report Paperwork</h4>
                <input type="hidden" name="lab_full_comp" value ="0">
                <input class="checkbox" type="checkbox" id="lab_full_comp" name="lab_full_comp" value="1">
                <label for="lab_full_comp">Full Compositional Analysis</label>

                <input type="hidden" name="lab_oxide" value ="0">
                <input class="checkbox" type="checkbox" id="lab_oxide" name="lab_oxide" value="1">
                <label for="lab_oxide">Oxides</label>

                <input type="hidden" name="lab_precious" value ="0">
                <input class="checkbox" type="checkbox" id="lab_precious" name="lab_precious" value="1">
                <label for="lab_precious">Precious Metals</label>
                <br>
                <input type="hidden" name="lab_tcpl_toggle" value ="0">
                <input class="checkbox" type="checkbox" id="lab_tcpl_toggle" name="lab_tcpl_toggle" value="1"
                    onclick="labTCPLToggle()">
                <label for="lab_tcpl_toggle">TCLP</label>

                <input type="hidden" name="lab_moisture" value ="0">
                <input class="checkbox" type="checkbox" id="lab_moisture" name="lab_moisture" value="1">
                <label for="lab_moisture">Moisture</label>

                <input type="hidden" name="lab_notes_toggle" value ="0">
                <input class="checkbox" type="checkbox" id="lab_notes_toggle" name="lab_notes_toggle" value="1"
                    onclick="labNotesToggle()">
                <label for="lab_notes_toggle">Additional Notes</label>

                <div id="lab_tcpl_div" style="display: none;">
                    <br>
                    <input type="hidden" name="lab_as" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_as" name="lab_as" value="1">
                    <label for="lab_as">Arsenic</label>

                    <input type="hidden" name="lab_ba" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_ba" name="lab_ba" value="1">
                    <label for="lab_ba">Barium</label>

                    <input type="hidden" name="lab_cd" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_cd" name="lab_cd" value="1">
                    <label for="lab_cd">Cadmium</label>

                    <input type="hidden" name="lab_cr" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_cr" name="lab_cr" value="1">
                    <label for="lab_cr">Chromium</label>
                    <br>
                    <input type="hidden" name="lab_pb" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_pb" name="lab_pb" value="1">
                    <label for="lab_pb">Lead</label>

                    <input type="hidden" name="lab_hg" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_hg" name="lab_hg" value="1">
                    <label for="lab_hg">Mercury</label>

                    <input type="hidden" name="lab_se" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_se" name="lab_se" value="1">
                    <label for="lab_se">Selenium</label>

                    <input type="hidden" name="lab_ag" value ="0">
                    <input class="checkbox" type="checkbox" id="lab_ag" name="lab_ag" value="1">
                    <label for="lab_ag">Silver</label>
                </div>
                <div id="lab_notes_div" style="display: none;">
                    <label class="field_label" for="lab_notes"><h4>Notes to Lab</h4></label>
                            <textarea class = "text_area" id="lab_notes" name="lab_notes" required disabled
                                style="height: 60px; width: 350px;" maxlength="500"></textarea>
                </div>

            </div>
        
            <div id="sample_recipient_form" style = "display: none;"> 
                <!-- sample recipient form, hidden by default unless customer/supplier picked in sample_recipient-->
                <h4>Sample Recipient Information</h4>
                    <input type="text" class = "text_field" id="recipient_company" name="recipient_company" placeholder="Recipient Company Name*" required disabled
                    pattern="[A-Z]([a-zA-Z0-9]|[- ,@\.#&!])*" title="company name" autocomplete="off" maxlength="64">
                    <br>
                    <input type="text" class = "text_field" id="recipient_co" name="recipient_co" placeholder="Recipient C/O*" required disabled
                    pattern="[\w\-]+((-|\s)[\w\d]+)*" title="person's name (letters, spaces, digits or -)" maxlength="64">
                    <input type="text" class = "text_field" id="recipient_address" name="recipient_address" placeholder="Recipient Address Line 1*" required disabled
                    pattern="[\d\w.,\\\-\/]+(\s[\d\w.,\\\-\/]+)*" title="address line" maxlength="128">
                    <input type="text" class = "text_field" id="recipient_address2" name="recipient_address2" placeholder="Address Line 2" disabled
                    pattern="[\d\w.,\\\-\/]+(\s[\d\w.,\\\-\/]+)*" title="address line" maxlength="64">
                    <br>
                    <input type="text" class = "text_field" id="recipient_city" name="recipient_city" placeholder="City*" required disabled
                    pattern="([a-zA-Z\u0080-\u024F]+(?:. |-| |'))*[a-zA-Z\u0080-\u024F]*" title="city name" maxlength="64">
                    <input type="text" class = "text_field" id="recipient_region" name="recipient_region" placeholder="State/ Province/ Region*" required disabled
                    pattern="([a-zA-Z\u0080-\u024F]+(?:. |-| |'))*[a-zA-Z\u0080-\u024F]*" title="region name" maxlength="64">
                    <input type="text" class = "text_field" id="recipient_zip" name="recipient_zip" placeholder="Zip / Postal Code*" required disabled
                    pattern="[\d\w]+[ -]?[\d\w]*" title="postal code" maxlength="24">
                    <br>
                    <select class="drop_list" name="recipient_country"id="recipient_country" onchange="selectRecipientCountry(this.value)" maxlength="64" required disabled>
                        <option value="">Select Country*</option>
                        <option value="Afghanistan">Afghanistan</option>
                        <option value="Åland Islands">Åland Islands</option>
                        <option value="Albania">Albania</option>
                        <option value="Algeria">Algeria</option>
                        <option value="American Samoa">American Samoa</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Anguilla">Anguilla</option>
                        <option value="Antarctica">Antarctica</option>
                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Aruba">Aruba</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaijan">Azerbaijan</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Belgium">Belgium</option>
                        <option value="Belize">Belize</option>
                        <option value="Benin">Benin</option>
                        <option value="Bermuda">Bermuda</option>
                        <option value="Bhutan">Bhutan</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bonaire">Bonaire</option>
                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Bouvet Island">Bouvet Island</option>
                        <option value="Brazil">Brazil</option>
                        <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                        <option value="Brunei Darrussalam">Brunei Darrussalam</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cambodia">Cambodia</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Canada">Canada</option>
                        <option value="Cape Verde">Cape Verde</option>
                        <option value="Cayman Islands">Cayman Islands</option>
                        <option value="Central African Republic">Central African Republic</option>
                        <option value="Chad">Chad</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Christmas Island">Christmas Island</option>
                        <option value="Cocos Islands">Cocos Islands</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Comoros">Comoros</option>
                        <option value="Congo">Congo</option>
                        <option value="Cook Islands">Cook Islands</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Côte dIvoire">Côte dIvoire</option>
                        <option value="Croatia">Croatia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Curaçao">Curaçao</option>
                        <option value="Cyprus">Cyprus</option>
                        <option value="Czech Republic">Czech Republic</option>
                        <option value="Denmark">Denmark</option>
                        <option value="Djibouti">Djibouti</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Dominican Republic">Dominican Republic</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egypt">Egypt</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                        <option value="Eritrea">Eritrea</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Eswatini (Swaziland)">Eswatini (Swaziland)</option>
                        <option value="Ethiopia">Ethiopia</option>
                        <option value="Falkland Islands">Falkland Islands</option>
                        <option value="Faroe Islands">Faroe Islands</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Finland">Finland</option>
                        <option value="France">France</option>
                        <option value="French Guiana">French Guiana</option>
                        <option value="French Polynesia">French Polynesia</option>
                        <option value="French Southern Territories">French Southern Territories</option>
                        <option value="Gabon">Gabon</option>
                        <option value="Gambia">Gambia</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Germany">Germany</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Gibraltar">Gibraltar</option>
                        <option value="Greece">Greece</option>
                        <option value="Greenland">Greenland</option>
                        <option value="Grenada">Grenada</option>
                        <option value="Guadeloupe">Guadeloupe</option>
                        <option value="Guam">Guam</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guernsey">Guernsey</option>
                        <option value="Guinea">Guinea</option>
                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Haiti">Haiti</option>
                        <option value="Heard and McDonald Islands">Heard and McDonald Islands</option>
                        <option value="Holy See">Holy See</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hong Kong">Hong Kong</option>
                        <option value="Hungary">Hungary</option>
                        <option value="Iceland">Iceland</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Iran">Iran</option>
                        <option value="Iraq">Iraq</option>
                        <option value="Ireland">Ireland</option>
                        <option value="Isle of Man">Isle of Man</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japan">Japan</option>
                        <option value="Jersey">Jersey</option>
                        <option value="Jordan">Jordan</option>
                        <option value="Kazakhstan">Kazakhstan</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Kiribati">Kiribati</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                        <option value="Lao">Lao</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Lebanon">Lebanon</option>
                        <option value="Lesotho">Lesotho</option>
                        <option value="Liberia">Liberia</option>
                        <option value="Libya">Libya</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Lithuania">Lithuania</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Macau">Macau</option>
                        <option value="Macedonia">Macedonia</option>
                        <option value="Madagascar">Madagascar</option>
                        <option value="Malawi">Malawi</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Mali">Mali</option>
                        <option value="Malta">Malta</option>
                        <option value="Marshall Islands">Marshall Islands</option>
                        <option value="Martinique">Martinique</option>
                        <option value="Mauritania">Mauritania</option>
                        <option value="Mauritius">Mauritius</option>
                        <option value="Mayotte">Mayotte</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Micronesia">Micronesia</option>
                        <option value="Moldova">Moldova</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Montenegro">Montenegro</option>
                        <option value="Montserrat">Montserrat</option>
                        <option value="Morocco">Morocco</option>
                        <option value="Mozambique">Mozambique</option>
                        <option value="Myanmar">Myanmar</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="New Caledonia">New Caledonia</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Niger">Niger</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Niue">Niue</option>
                        <option value="Norfolk Island">Norfolk Island</option>
                        <option value="North Korea">North Korea</option>
                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                        <option value="Norway">Norway</option>
                        <option value="Oman">Oman</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="Palau">Palau</option>
                        <option value="Palestine, State of">Palestine, State of</option>
                        <option value="Panama">Panama</option>
                        <option value="Papua New Guinea">Papua New Guinea</option>
                        <option value="Paraguay">Paraguay</option>
                        <option value="Peru">Peru</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Pitcairn">Pitcairn</option>
                        <option value="Poland">Poland</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Puerto Rico">Puerto Rico</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Réunion">Réunion</option>
                        <option value="Romania">Romania</option>
                        <option value="Russia">Russia</option>
                        <option value="Rwanda">Rwanda</option>
                        <option value="Saint Barthélemy">Saint Barthélemy</option>
                        <option value="Saint Helena">Saint Helena</option>
                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                        <option value="Saint Lucia">Saint Lucia</option>
                        <option value="Saint Martin">Saint Martin</option>
                        <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                        <option value="Saint Vincent and the Grenadines">Saint Vincent</option>
                        <option value="Samoa">Samoa</option>
                        <option value="San Marino">San Marino</option>
                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Serbia">Serbia</option>
                        <option value="Seychelles">Seychelles</option>
                        <option value="Sierra Leone">Sierra Leone</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Sint Maarten">Sint Maarten</option>
                        <option value="Slovakia">Slovakia</option>
                        <option value="Slovenia">Slovenia</option>
                        <option value="Solomon Islands">Solomon Islands</option>
                        <option value="Somalia">Somalia</option>
                        <option value="South Africa">South Africa</option>
                        <option value="South Georgia">South Georgia</option>
                        <option value="South Korea">South Korea</option>
                        <option value="South Sudan">South Sudan</option>
                        <option value="Spain">Spain</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <option value="Sudan">Sudan</option>
                        <option value="Suriname">Suriname</option>
                        <option value="Svalbard and Jan Mayen Islands">Svalbard Islands</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Switzerland">Switzerland</option>
                        <option value="Syria">Syria</option>
                        <option value="Taiwan">Taiwan</option>
                        <option value="Tajikistan">Tajikistan</option>
                        <option value="Tanzania">Tanzania</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Timor-Leste">Timor-Leste</option>
                        <option value="Togo">Togo</option>
                        <option value="Tokelau">Tokelau</option>
                        <option value="Tonga">Tonga</option>
                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                        <option value="Tunisia">Tunisia</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Turkmenistan">Turkmenistan</option>
                        <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                        <option value="Tuvalu">Tuvalu</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="Uruguay">Uruguay</option>
                        <option value="US Minor Outlying Islands">US Minor Outlying Islands</option>
                        <option value="Uzbekistan">Uzbekistan</option>
                        <option value="Vanuatu">Vanuatu</option>
                        <option value="Venezuela">Venezuela</option>
                        <option value="Vietnam">Vietnam</option>
                        <option value="Virgin Islands, British">Virgin Islands, British</option>
                        <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                        <option value="Wallis and Futuna">Wallis and Futuna</option>
                        <option value="Western Sahara">Western Sahara</option>
                        <option value="Yemen">Yemen</option>
                        <option value="Zambia">Zambia</option>
                        <option value="Zimbabwe">Zimbabwe</option>                
                    </select>
                    <input type="text" class = "text_field" id="recipient_phone" name="recipient_phone" placeholder="Recipient Phone*" required disabled
                    pattern="[+()\- \d]{10,}" title="phone number" maxlength="24">
                    <input type="text" class = "text_field" id="recipient_email" name="recipient_email" placeholder="Recipient Email*" required disabled
                    pattern="[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}" maxlength="64" title="email" autocomplete="off">
                    <br>
                        <h4>Does the sample have to arrive by a certain date*</h4>
                        <input type="radio" id="recipient_date_radio_yes" name="recipient_date_radio" value="1" 
                            onclick="recipientDateShow()" disabled>
                        <label for="recipient_date_radio_yes">Yes</label>
                        <input type="radio" id="recipient_date_radio_no" name="recipient_date_radio" value="0" 
                            onclick="recipientDateHide()" checked disabled>
                        <label for="recipient_date_radio_no">No</label>
                        <div id="date_form"><!-- Should be hidden until yes is selected in arrival_radio-->
                            <input type="date" class="date_field" id="recipient_date" name="recipient_date" required disabled>
                        </div>
                    <div>
                        <div>
                            <label class="field_label" for="recipient_description">Description of Material* </label>
                            <br>
                            <textarea class = "text_area" id="recipient_description" name="recipient_description" 
                                style="height: 60px; width: 350px;" maxlength="500" required disabled></textarea>
                        </div>
                        <div>
                            <label class="field_label" for="recipient_shipping">Shipping Details* </label>
                            <br>
                            <textarea class = "text_area" id="recipient_shipping" name="recipient_shipping" 
                                style="height: 60px; width: 350px;" maxlength="500" required disabled></textarea>
        
                        </div>
                    </div>
            </div> <!-- End of sample recipient form-->

            <input type="submit" id="submit_button" name="submit_button" value="Submit">
            <br>
            <p id="error_message"><p id="error_message">* = required</p></p>
            <br>
        </div> <!-- end of grid container-->
    </form>
</body>
<script>

</script>
</html>