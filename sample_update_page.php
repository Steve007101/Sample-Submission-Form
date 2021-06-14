<?php
    require_once("../wp-load.php");
?>
<!-- Authored by Steven Perry-->
<!--
    This is the sample update page. First a record must be found by using the search function
    then selected in the table with the results (or my update_page_loader.php will show a record
    automatically through a link). The tracking, lab, and submission sections will then appear.
    Tracking and lab info can only be submitted if they aren't in the record yet.
-->
<!DOCTYPE html>
<html lang="en-us">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Submission Search</title>
<script>
    // functions for search table
    var starting_row = 0;
    var rows_per_page = 10;
    var search_used = false;
    function search(new_search) {
        search_used = true;
        if (new_search)
            starting_row = 0;
        var search_str = "";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("search_results").innerHTML = this.responseText;
                if (document.getElementById("search_debug_info") != null)
                    console.log(document.getElementById("search_debug_info").value);
            }
            else {
                if (document.getElementById("search_debug_info") != null)
                    console.log(document.getElementById("search_debug_info").value);
            }
        };
        search_str += "id=" + document.getElementById("search_id").value
                    + "&trader=" + document.getElementById("search_trader").value
                    + "&ocomp=" + document.getElementById("search_origin_company").value
                    + "&date=" + document.getElementById("search_date").value
                    + "&sort=" + document.querySelector('input[name="order_by_radio"]:checked').value
                    + "&row=" + starting_row
                    + "&rpp=" + rows_per_page;
        xmlhttp.open("GET", "get_search.php?" + search_str, true);
        xmlhttp.send();
    }
    function newPage(new_index) {
        starting_row = new_index;
        search(false); // search with currently set starting_row
    }
    function resetDate() {
        document.getElementById("search_date").value = "";
    }
    function displayRecord(row_index) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("sample_details").innerHTML = this.responseText;
                if (document.getElementById("record_table_debug_info") != null)
                    console.log(document.getElementById("record_table_debug_info").value);
                // need to also populate tracking info and lab report sections with
                // passed hidden html values
                populateTrackingandLabSections();
            } else {
                if (document.getElementById("record_table_debug_info") != null)
                    console.log(document.getElementById("record_table_debug_info").value);
            }
        };
        xmlhttp.open("GET", "get_record.php?id=" + row_index, true);
        xmlhttp.send();
    }

    function populateTrackingandLabSections() {
        // check if tracking number or carrier was already subitted, then it should be disabled
        // otherwise clear values and enable


        // lab report may or may not be present, check
        if (document.getElementById("passed_lab_report") != null &&
            document.getElementById("passed_lab_report").value != "") {
                // there is a lab report
                document.getElementById("lab_text").innerHTML = 
                    "<a href='"+document.getElementById("passed_lab_report_dir").value+"/"+document.getElementById("passed_lab_report").value+"' target='_blank'>"
                    +document.getElementById("passed_lab_report").value+"</a>";
                document.getElementById("lab_file").value = "";
                document.getElementById("lab_file").disabled = true;
                document.getElementById("submit_lab_report").disabled = true;
        } else {
                // there is no lab report, allow input (until tracking info check)
                document.getElementById("lab_text").innerHTML = "None";
                document.getElementById("lab_file").value = "";
                document.getElementById("lab_file").disabled = false;
                document.getElementById("submit_lab_report").disabled = false;
        }

        if (document.getElementById("passed_tracking_num") != null &&
        document.getElementById("passed_tracking_num").value != "") {
            // tracking number was already submitted, show and make read-only
            var ts_tracking = document.getElementById("passed_tstracking").value;
            var carrier = document.getElementById("passed_carrier").value;
            var passed_tracking_num = document.getElementById("passed_tracking_num").value;
            document.getElementById("tracking_text").innerHTML = "Submitted"
            // need to format per Bob's instructions as MM/DD/YY, timestamp is in YYYY/MM/DD HH:MM:SS
            +(ts_tracking != "" ? " "+ts_tracking.substring(5, 7)+"/"+ts_tracking.substring(8, 10)+"/"+ts_tracking.substring(2, 4) : "")
            +(carrier != "" ? " "+carrier : "" );
            // include links to tracking information if one of the major carriers (FedEx, USP, USPS, Courier)
            if (carrier == "FedEx" && passed_tracking_num != "") {
                document.getElementById("tracking_text").innerHTML += "<br><a href='https://www.fedex.com/fedextrack/?tracknumbers="
                +passed_tracking_num+"' target='_blank'>Tracking Link</a>";
            }
            else if (carrier == "UPS" && passed_tracking_num != "") {
                document.getElementById("tracking_text").innerHTML += "<br><a href='http://wwwapps.ups.com/WebTracking/processInputRequest?TypeOfInquiryNumber=T&InquiryNumber1="
                +passed_tracking_num+"' target='_blank'>Tracking Link</a>";
            }
            else if (carrier == "USPS" && passed_tracking_num != "") {
                document.getElementById("tracking_text").innerHTML += "<br><a href='https://tools.usps.com/go/TrackConfirmAction.action?tLabels="
                +passed_tracking_num+"' target='_blank'>Tracking Link</a>";
            }
                        
            document.getElementById("carrier").value = carrier;
            document.getElementById("carrier").hidden = true;
            document.getElementById("tracking_num").value = passed_tracking_num;
            document.getElementById("tracking_num").readOnly = true;
            document.getElementById("submit_tracking").disabled = true;
                 
        } else {
            // no tracking yet
            document.getElementById("tracking_text").innerHTML = "None";
            document.getElementById("carrier").value = "";
            document.getElementById("tracking_num").value = "";
            document.getElementById("submit_tracking").disabled = false;
            // with no tracking, disallow submitting lab reports
            document.getElementById("lab_file").disabled = true;
            document.getElementById("submit_lab_report").disabled = true;
            // in case of distributor loading the page, allow input
            // for tracking info
            if (document.getElementById("enable_editing") != null &&
            document.getElementById("enable_editing").value == 'Yes') {
                document.getElementById("carrier").hidden = false;
                document.getElementById("tracking_num").readOnly = false;
            }

        }

        // make lab cover letter link viewable if distributor is viewing page
        if (document.getElementById("enable_editing") != null &&
            document.getElementById("enable_editing").value == 'Yes') {
                document.getElementById("lab_cover").hidden = false; 
            }
    }
    function submitTracking() {
        var tracking_str = "";
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                console.log("Response text:"+this.responseText);
                if (this.responseText.search("Success") != -1) { 
                //for some reason there are
                console.log("Response text:"+this.responseText);
                // two leading new line characters in response texts
                    if (search_used)
                        search(false); // without this the search table will still say No for tracking#
                    //      /\ false means use current starting row
                    // populate bottom with hidden values
                    displayRecord(document.getElementById("selected_id").value); 
                    // use those hidden values to change tracking form
                    populateTrackingandLabSections();
                    alert("Tracking Number Successfully Uploaded");
                }
                else {
                    console.log("Response text:"+this.responseText);
                    alert("Tracking Number Failed to Upload");
                }
            }
        };
        tracking_str = "id=" + document.getElementById("selected_id").value
                    + "&carrier=" + document.getElementById("carrier").value
                    + "&tracking_num=" + document.getElementById("tracking_num").value
        xmlhttp.open("POST", "post_tracking.php?" + tracking_str, true);
        xmlhttp.send();
        return false; // Since we don't actually "submit" the form
    }

    function submitLabReport() {
        var formData = new FormData();
        var file = document.getElementById("lab_file").files[0];

        formData.append("lab_file", file);
        var url ="post_lab_report.php?id=" + document.getElementById("selected_id").value;
        if (window.XMLHttpRequest){
            xmlhttp=new XMLHttpRequest();
        }else{
            xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }

        var http = new XMLHttpRequest();
        http.open("POST", url, true);        
        http.send(formData);

        http.onreadystatechange = function() {
            if(http.readyState == 4 && http.status == 200) {
                console.log("Response text:"+this.responseText);
                if (this.responseText.search("Success") != -1) {
                    console.log("Response text:"+this.responseText);
                // for some reason there are
                // two leading new line characters in response texts
                    if (search_used)
                        search(false); // without this the search table will still say No for lab report
                    //      /\ false means use current starting row
                    // populate bottom with hidden values
                    displayRecord(document.getElementById("selected_id").value); 
                    // use those hidden values to change bottom forms
                    populateTrackingandLabSections();
                    alert("Lab Report Successfully Uploaded")
                } else {
                    console.log("Response text:"+this.responseText);
                    alert("Lab Report Failed to Upload")
                }
            }
        }
        return false; // Since we don't actually "submit" the form
    }
</script>
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
    grid-template-columns: 300px 300px 300px 410px;
    grid-template-rows: repeat(4, minmax(200, 200));
    grid-gap: 10px;
    padding: 2px;
    justify-content: center;
    }
    #search_area {
        grid-row: 1 / span 2;
        grid-column: 1 / span 2;
    }
    #sample_details {
        grid-row: 1 / span 4;
        grid-column: 3 / span 2;
        /* grid within a grid */
        display: grid;
        grid-template-columns: 400px 150px;

    }
    /*grid items within #sample_details */
    #details_table1 {
        grid-row: 1 / span 1;
        grid-column: 1 / span 1;
    }
    #details_table2 {
        grid-row: 1 / span 1;
        grid-column: 2 / span 1;
    }
    /* end of grid items within #sample_details */
    #tracking_info {
        grid-row: 3 / span 1;
        grid-column: 1 / span 1
    }
    #lab_report_info {
        grid-row: 3 / span 1;
        grid-column: 2 / span 1
    }
    /* table styling */
    th {
        text-align: left;
        vertical-align: top;
    }
    td {
        text-align: left;
        vertical-align: top;
    }
    .search_table {
        table-layout: fixed;
    }
    .search_table_id  {
        width: 9ch;
    }
    .search_table_date  {
        width: 9ch;
    }
    .search_table_trader  {
        width: 18ch;
    }
    .search_table_company  {
        width: 18ch;
    }
    .selectable_row { cursor: pointer; }
    .selectable_row:hover {background-color: #f5f5f5;}
    .details_table {
        table-layout: fixed;
    }
    .details_table th {
        width: 10ch;
    }
    #details_table1 {
        width: 400px;
    }
    #details_table2 {
        width: 150px;
    }
    /*.details_header1 {

    }
    .details_cell1 {

    } 
    .details_header2 {
        width: 10ch;
    } */
    .details_cell2 {
        width: 30ch;
    }
    /* search area styling */
    #search_form > * {
       margin: 3px;
    }
    #below_search > * {
       margin: 3px;
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
    </style>
</head>
<body>
<!-- navbar -->
<div class="navmenu">
    <div class="navlink">
        <a href="https://company.website.com/">Home</a>
        <a href="https://company.website.com/sample-log-forms/sample_submission_form.php">Submission Form</a>
        <a class="active" href="https://company.website.com/sample-log-forms/update_page_loader.php">Search</a>
    </div>
</div>
    <img id="logo" src="./image/Logo-Blackv2.png" alt="Logo Black">
    <div id="title"><h1>Sample Submission Search</h1></div>
<div id='grid_container'>
    <div id="search_area">
        <h4>Search</h4>
        <div id="search_input">
            <form id="search_form">
            <input type="text" class="text_field" id="search_id" name="search_id" placeholder="Sample ID">
            <input type="text" class="text_field" id="search_trader" name="search_trader" placeholder="Trader">
            <input type="text" class="text_field" id="search_origin_company" name="search_origin_company" placeholder="Origin Company">
            <div>
                Order by
                <input type="radio" id="order_by_id_radio" name="order_by_radio" value="id">
                <label for="order_by_id_radio">Sample ID</label>
                <input type="radio" id="order_by_trader_radio" name="order_by_radio" value="trader">
                <label for="order_by_trader_radio">Trader</label>
                <input type="radio" id="order_by_company_radio" name="order_by_radio" value="ocompany">
                <label for="order_by_company_radio">Origin Company</label>
            </div>
            <div>
                <input type="radio" id="order_by_date_new_radio" name="order_by_radio" value="tscreatedDesc" checked>
                <label for="order_by_date_radio">Date (Newest to Oldest)</label>
                <input type="radio" id="order_by_date_old_radio" name="order_by_radio" value="tscreatedAsc">
                <label for="order_by_date_radio">Date (Oldest to Newest))</label>
            </div>
            
                <input type="button" onclick="search(true)" value="Search">
                <input type="button" onclick="resetDate()" value="Reset Date">
                <label for="search_date">Earliest Date:</label>
                <input type="date" class="date_field" id="search_date" name="search_date"> 
                <div>
                Click a row from the search table to display a record
                </div>
            </form>
        </div>
        <div id="search_results">
        <!-- <tr>
                <td></td> php code will generate table here
            </tr> -->
        </div>
    </div>
    <div id="tracking_info">
        <h4 id="tracking_display">Tracking Information</h4>
        <form id= "tracking_form" onsubmit="return submitTracking()">
            <p id="tracking_text">None</p>
            <!-- <input type="text" class ="text_field" id="carrier" name="carrier" placeholder="Carrier Name*" required> old element-->
            <select class="drop_list" id="carrier" name="carrier" required hidden>
                <option value="">Carrier Name*</option>
                <option value="FedEx">FedEx</option>
                <option value="UPS">UPS</option>
                <option value="USPS">USPS</option>
                <option value="Courier">Courier</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" class ="text_field" id="tracking_num" name="tracking_num" placeholder="Tracking #*" required readonly>
            <br>
            <input type="submit" id="submit_tracking" name="submit_tracking" value="Submit" hidden>
            <input type='hidden' id='submit_tracking_reponse' name='submit_tracking_reponse' value="">
        </form>
    </div>
    <div id="lab_report_info">
        <h4 id="lab_display">Lab Report</h4>
        <form id="lab_form" onsubmit="return submitLabReport()">
            <p id="lab_text">None</p>
            <input class="select_file" type="file" id="lab_file" name="lab_file" required
            accept=".doc,.docx,.pdf" hidden>
            <br>
            <input type="submit" id="submit_lab_report" name="submit_lab_report" value="Submit" hidden>
        </form>
    </div>
    <div id="sample_details">
        <!-- php code will show submission information here-->
    </div>
</div> <!-- end of grid div-->
</body>
</html>