<?php
/**
 * User: Sitao Min
 * USC id: 2109923078
 */
include 'geoHash.php';
$ticketMasterApikey = "f5r0Yhc2Dln0m3rlwOpG7TtYKtbSUprn";
$googleMapApiKey = "";
$error = array();


function getSegmentID($category){
    if($category == "Music"){
        $segmentID = "KZFzniwnSyZfZ7v7nJ";
    }
    else if($category == "Sports"){
        $segmentID = "KZFzniwnSyZfZ7v7nE";
    }
    else if($category == "Arts & Theatre"){
        $segmentID = "KZFzniwnSyZfZ7v7na";

    }
    else if($category == "Film"){
        $segmentID = "KZFzniwnSyZfZ7v7nn";

    }
    else if($category == "Miscellaneous"){
        $segmentID = "KZFzniwnSyZfZ7v7n1";

    }
    else{
        $segmentID = "";

    }
    return $segmentID;
}

/**
 * get ticketMaster query results JSON and convert to associative array
 * @param $searchQuery
 * @return result array
 */
function getTMQueryResult($searchQuery){
    $queryResult_JSON = file_get_contents($searchQuery);
    $queryResult = json_decode($queryResult_JSON,true);
    return $queryResult;
}

/**
 * fetch useful information
 * @param $TMEventSearchResult
 * @return array
 */
function fetchEvents($TMEventSearchResult){
    $eventsResult = array();
    if(array_key_exists('_embedded',$TMEventSearchResult)){
        if(array_key_exists('events',$TMEventSearchResult["_embedded"])){
            $eventsArray = $TMEventSearchResult["_embedded"]["events"];

            foreach($eventsArray as $i => $eventItem){

                $eventsResult[$i]["name"] = array_key_exists('name',$eventItem)?$eventItem["name"]:"";
                $eventsResult[$i]["id"] = array_key_exists('id',$eventItem)?$eventItem["id"]:"";
                //extract icon
                if(array_key_exists("images",$eventItem)){
                    $eventsResult[$i]["icon"] = $eventItem["images"][0];
                }
                else{
                    $eventsResult[$i]["icon"] = "";
                }

                //extract date
                if(array_key_exists('dates',$eventItem)){
                    if(array_key_exists('start',$eventItem["dates"])){
                        $eventsResult[$i]["localDate"] = array_key_exists('localDate', $eventItem["dates"]["start"]) ?$eventItem["dates"]["start"]["localDate"]:"";
                        $eventsResult[$i]["localTime"] = array_key_exists('localTime', $eventItem["dates"]["start"]) ?$eventItem["dates"]["start"]["localTime"]:"";
                    }
                    else{
                        $eventsResult[$i]["localDate"] = "";
                        $eventsResult[$i]["localTime"] = "";
                    }
                }
                else{
                    $eventsResult[$i]["localDate"] = "";
                    $eventsResult[$i]["localTime"] = "";
                }
                //extract segment
                if(array_key_exists('classifications',$eventItem)){
                    if(array_key_exists("segment",$eventItem['classifications'][0])){
                        if(array_key_exists('name',$eventItem["classifications"][0]['segment'])){
                            $eventsResult[$i]["segment"] = $eventItem['classifications'][0]['segment']['name'];
                        }
                        else{
                            $eventsResult[$i]["segment"] = "";
                        }
                    }
                    else{
                        $eventsResult[$i]["segment"] = "";

                    }
                }
                else{
                    $eventsResult[$i]["segment"] = "";
                }


                //extract venue
                $eventsResult[$i]["venues"] = array();
                if(array_key_exists('_embedded',$eventItem)){
                    if(array_key_exists('venues',$eventItem["_embedded"])){
                        $eventsResult[$i]["venues"]["name"] = array_key_exists('name',$eventItem["_embedded"]["venues"][0])? $eventItem["_embedded"]["venues"][0]["name"]:"";
                        $eventsResult[$i]["venues"]["location"] = array_key_exists('location',$eventItem["_embedded"]["venues"][0])? $eventItem["_embedded"]["venues"][0]["location"]:"";
                    }
                    else{
                        $eventsResult[$i]["venues"]["name"] = "";
                        $eventsResult[$i]["venues"]["location"] = "";
                    }
                }
                else{
                    $eventsResult[$i]["venues"]["name"] = "";
                    $eventsResult[$i]["venues"]["location"] = "";
                }
            }

        }
    }

    return $eventsResult;
}

/**
 * fetch useful information of event details
 * @param $TMeventDetailsResult
 * @return array
 */
function fetchEventDetails($TMeventDetailsResult){
    $eventDetailsResult = array();
    $eventDetailsArray = $TMeventDetailsResult;
    //extract information
    $eventDetailsResult["name"] = array_key_exists('name',$eventDetailsArray)?$eventDetailsArray["name"]:"";

    //extract dates information
    $eventDetailsResult["date"] = array();
    if(array_key_exists('dates',$eventDetailsArray)){
        if(array_key_exists('start',$eventDetailsArray['dates'])){
            if(array_key_exists('localDate',$eventDetailsArray["dates"]["start"])){
                $eventDetailsResult["date"]["localDate"] = $eventDetailsArray["dates"]["start"]["localDate"];
            }
            if(array_key_exists('localDate',$eventDetailsArray["dates"]["start"])){
                $eventDetailsResult["date"]["localTime"] = $eventDetailsArray["dates"]["start"]["localTime"];
            }
        }
    }

    //extract genre information
    $eventDetailsResult["genre"] = array();
    if(array_key_exists('classification',$eventDetailsArray)){
        foreach($eventDetailsArray["classification"] as $i => $ele){
            if(array_key_exists('segment',$ele)){
                $eventDetailsResult["genre"][$i]["segment"] = $ele["segemnt"];
            }
            if(array_key_exists('genre',$ele)){
                $eventDetailsResult["genre"][$i]["genre"] = $ele["genre"];
            }
            if(array_key_exists('subGenre',$ele)){
                $eventDetailsResult["genre"][$i]["subGenre"] = $ele["subGenre"];
            }
            if(array_key_exists('type',$ele)){
                $eventDetailsResult["genre"][$i]["type"] = $ele["type"];
            }
            if(array_key_exists('segment',$ele)) {
                $eventDetailsResult["genre"][$i]["subType"] = $ele["subType"];
            }
        }
    }

    //extract artist information
    $eventDetailsResult["artists"] = array();
    if(array_key_exists('_embedded',$eventDetailsArray)){
        if(array_key_exists('attractions',$eventDetailsArray["_embedded"])) {
            foreach ($eventDetailsArray["_embedded"]["attractions"] as $i => $ele) {
                if(array_key_exists('name',$ele)){
                    $eventDetailsResult["artists"][$i]["name"] = $ele["name"];
                }
                if(array_key_exists('url',$ele)){
                    $eventDetailsResult["artists"][$i]["url"] = $ele["url"];
                }
            }
        }
    }

    //extract venue information
    $eventDetailsResult["venues"] = array();
    if(array_key_exists("_embedded",$eventDetailsArray)){
        if(array_key_exists("venues",$eventDetailsArray["_embedded"])){
            foreach($eventDetailsArray["_embedded"]["venues"] as $i => $ele){
                if(array_key_exists('name',$ele)){
                    $eventDetailsResult["venues"][$i]["name"] = $ele["name"];
                }
            }
        }
    }

    //extract price range
    $eventDetailsResult['priceRange'] = array();
    if(array_key_exists('priceRange',$eventDetailsArray)){
        foreach($eventDetailsArray["priceRange"] as $i => $ele){
            if(array_key_exists('min',$ele)){
                $eventDetailsResult['priceRange'][$i]["min"] = $ele["min"];
            }
            if(array_key_exists('max',$ele)){
                $eventDetailsResult['priceRange'][$i]["max"] = $ele["max"];
            }

        }
    }
    //extract ticketStatus information
    if(array_key_exists('dates',$eventDetailsArray)){
        if(array_key_exists('status',$eventDetailsArray["dates"])){
            if(array_key_exists('code',$eventDetailsArray["dates"]["status"])){
                $eventDetailsResult["ticketStatus"] = $eventDetailsArray["dates"]["status"]["code"];
            }
        }
    }
    //extract seatmap
    if(array_key_exists('seatmap',$eventDetailsArray)){
        if(array_key_exists('staticUrl',$eventDetailsArray["seatmap"])){
            $eventDetailsResult["seatmap"] = $eventDetailsArray["seatmap"]["staticUrl"];
        }

    }
    //extract buy ticket at url
    if(array_key_exists('url',$eventDetailsArray)){
        $eventDetailsResult["buyTicketAt"] = $eventDetailsArray["url"];

    }

    return $eventDetailsResult;
}

/** fetch useful information of venue information
 * @param $venueInfo
 * @return array
 */
function fetchVenueInfo($venueInfo){

    return array();
}

/** fetch venue image
 * @param $venueInfo
 * @return array
 */
function fetchVeneuImage($venueInfo){

    return array();
}

/**
 * parse form data
 */
$eventsResult_js = "";
$eventsDetails_js = "";
$venueImage_js = "";
$venueInfo_js = "";

echo $searchDataPosted."<br>";
echo $_POST["Keywords"]."<br>";
echo $_POST["Category"]."<br>";
echo $_POST["Distance"]."<br>";
echo isset($_POST["currentLocation"])."<br>";
echo isset($_POST["otherLocation"])."<br>";
if(isset($_POST["otherLocation"])){
    echo $_POST["otherLocation"]."<br>";
}

$searchDataPosted = ($_SERVER["REQUEST_METHOD"] == 'POST')&&(isset($_POST["submit"]));
if($searchDataPosted){

    /**
     * get Super global Variables ---- event form keywords
     */
    $keyword = str_replace(' ','+',$_POST["Keywords"]);
    echo $_POST["Keywords"]."<br>";
    echo $keyword."<br>";
    $segmentID = getSegmentID($_POST["Category"]);
    $distance = $_POST["Distance"];

    if($_POST["otherLocation"] != ""){
        $otherLocationName = substr($_POST["Location"],7);
        $otherLocationName = str_replace(' ', '+', $otherLocationName);

        $GoogleMapQuery.="https://maps.googleapis.com/maps/api/geocode/json?"."address=".$otherLocationName."&key=".$googleMapApiKey;

        $GoogleMapResult = file_get_contents($GoogleMapQuery);
        $GoogleMapResultArray = json_decode($GoogleMapResult, true);


        if($GoogleMapResultArray["status"]=="OK"){
            $locationLat = $GMGeocoding_decode["results"][0]["geometry"]["location"]["lat"];
            $locationLong = $GMGeocoding_decode["results"][0]["geometry"]["location"]["lng"];
            $geoPoint = encode($locationLat, $locationLong);
        }
        else{
            $geoPoint = "";
        }
    }
    else{
        $currentLocation = explode(" ",$_POST["currentLocation"]);
        $locationLat = $currentLocation[0];
        $locationLong = $currentLocation[1];
        $geoPoint = encode($locationLat, $locationLong);
    }

    if($segmentID == ""){
        $ticketMasterEventSearchQuery =  "https://app.ticketmaster.com/discovery/v2/events.json?".
            "&apikey=".$ticketMasterApikey."&keyword=".$keyword."&unit=miles&radius=".$distance."&geoPoint=".$geoPoint;
    }
    else {
        $ticketMasterEventSearchQuery = "https://app.ticketmaster.com/discovery/v2/events.json?".
            "&apikey=".$ticketMasterApikey."&keyword=".$keyword."&unit=miles&radius=".$distance."&segmentID=".$segmentID.
            "&geoPoint=".$geoPoint;
    }
    echo $keyword."<br>";
    echo $ticketMasterEventSearchQuery."<br>";

    /**
     * get Event Search results JSON
     */
    $ticketMasterEventSearchResult = getTMQueryResult($ticketMasterEventSearchQuery);
    //echo '<pre>'; print_r($ticketMasterEventSearchResult); echo '</pre>';
    /**
     * fetch useful information results and save to results array
     */
    $eventsResult_js = fetchEvents($ticketMasterEventSearchResult);
    //echo '<pre>'; print_r($eventsResult_js); echo '</pre>';
}
else{
    $message = "please submit";
}

/**
 * parse event data
 */
$selectedEventPosted = False;
if($selectedEventPosted){
    $eventID = $_GET["eventID"];
    $ticketMasterEventDetailQuery = "https://app.ticketmaster.com/discovery/v2/venues.json?"
                                    .$eventID.".json?apikey=".$ticketMasterApikey;
    $eventDetails = getTMQueryResult($ticketMasterEventDetailQuery);
    $eventDetails_js = fetchEventDetails($eventDetails);

}

/**
 * parse venue info data
 */
$showVenueInfoPosted = False;
if($showVenueInfoPosted){
    $venueName = str_replace(' ', '+', $_POST["venue_info"]);
    $ticketMasterVenueSearchQuery = "https://app.ticketmaster.com/discovery/v2/venues.json?".
                                    "apikey=".$ticketMasterApikey."&keyword=".$venueName;
    $venueInfo = getTMQueryResult($ticketMasterVenueSearchQuery);
    $venueInfo_js = fetchVenueInfo($venueInfo);
}

/**
 * parse venue image data
 */
$showVenueImagePosted = False;
if($showVenueImagePosted){
    $venueName = str_replace(' ', '+', $_POST["venue_image"]);
    $ticketMasterVenueSearchQuery = "https://app.ticketmaster.com/discovery/v2/venues.json?".
                                    "apikey=".$ticketMasterApikey."&keyword=".$venueName;
    $venueInfo = getTMQueryResult($ticketMasterVenueSearchQuery);
    $venueImage_js = fetchVenueImage($venueInfo);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Search</title>
    <style type="text/css">
        form{
            margin: auto;
            padding-left: 10px;
            padding-right: 10px;
            margin-top: 50px;
            width: 600px;
            background-color: #fafafa;
            border: 3px solid grey;
            margin-bottom: 15px;

        }
        form label{
            margin-left: 5px;
            margin-top:10px;
            font-weight: bold;
            font-size:15px;
        }
        form h1{
            font-weight: 100;
            font-size: 30px;
            line-height: 0px;
            padding-left: 200px;
        }
        form input{
            margin-top:5px;
            margin-left:5px;
        }
        form select{
            margin-top:5px;
            margin-left:5px;
        }
        form select option{
            font-size:14px;
        }
        .button{
            margin-left: 50px;
            margin-top:15px;
            margin-bottom: 20px;
        }

        .button input.submit{
            font-size:12px;
        }

        .button input.reset{
            font-size:12px;
        }

        .location-input{
            margin-left:300px;
        }
    </style>
</head>
<body>
<form method = "post">
    <h1><i>Event Search</i></h1>
    <hr/>
    <label>Keywords</label><input type="text" id="Keywords" name="Keywords" value="University of Southern California"><br/>

    <label>Catergories</label>
    <select name = "Category" id="Category">
        <option value = "Default" selected>Default</option>
        <option value = "Music">Music</option>
        <option value = "Sports">Sports</option>
        <option value = "Arts&Theatre">Arts & Theatre</option>
        <option value = "Film">Film</option>
        <option value = "Miscellaneous">Miscellaneous</option>
    </select>
    <br/>

    <label>Distance(Miles)</label><input type = "text" placeholder="10" name="Distance" id="Distance" value="10">
    <label>from</label>
    <input type="radio" name="location" checked>Here<br/><input type="hidden" name="currentLocation" id="currentLocation">
    <div class="location-input">
        <input type="radio" name="location"><input type="text" name="otherLocation" id="otherLocation" placeholder="location" >
    </div>
    <br/>

    <div class="button">
        <input type="submit" onclick="showEventsTable();" name="submit" id="search" value="search" class="submit">
        <input type="reset"  value="Clear" class="reset" onclick = "clearResultAndForm();">
    </div>
</form>

<br>
<br>
<br>
<div id="eventTable"></div>

<script type = "text/javascript">

    document.getElementById("search").disabled = true;
    var ipapi = new XMLHttpRequest();
    ipapi.open("GET","http://ip-api.com/json",false);
    ipapi.send();
    console.log(JSON.parse(ipapi.responseText));
    var lat = JSON.parse(ipapi.responseText).lat;
    var long = JSON.parse(ipapi.responseText).lon;
    console.log(lat,long);

    var currentLocation = document.getElementById("currentLocation");
    currentLocation.value = lat + " " + long;
    console.log(currentLocation.value);
    document.getElementById("search").disabled = false;

    /**
     * clear form
     */
    function clearResultAndForm(){



    }

    /**
     * show event table
     */
    while(true){
        var eventResult = JSON.parse('<?php echo json_encode($eventsResult_js,JSON_HEX_APOS); ?>');

        if(eventResult.length != 0){
            showEventsTable(eventResult);
            break;
        }
    }

    function showEventsTable(eventResult){
        console.log(eventResult);
        var eventTableHTML = "";
        //show table
        eventTableHTML += '<table border = "2" style = "text-align:left;">';
        //show header
        eventTableHTML += "<tr><th>Date</th>" + "<th>Icon</th>" + "<th>Event</th>" + "<th>Genre</th>" + "<th>Venue</th></tr>";
        //show table data
        for(let i in eventResult){
            eventTableHTML += "<tr>";
            //show each rows
            //show date
            eventTableHTML += "<td><p>localDate:"+ eventResult[i].localDate +"</p><p>localTime" +  eventResult[i].localTime +"</p></td>";
            //show icon
            eventTableHTML += "<td>" + "<img src='" + eventResult[i].icon.url + "' width='100' height='80' alt='logo'>" + "</td>";
            //show event name
            eventTableHTML += "<td><a href = 'event.php?eventID=" + eventResult[i].id + " onclick= 'showEventDetails();'>" + eventResult[i].name + "</a></td>"
            //show genre
            eventTableHTML += "<td>" + eventResult[i].segment + "</td>";
            //show venue
            eventTableHTML += "<td><a href='event.php'" + " onclick='showVenueMap();'>" + eventResult[i].venues.name + "</a></td>";
            eventTableHTML += "</tr>";
        }
        //end show table
        eventTableHTML += '</table>';

        document.getElementById("eventTable").innerHTML = eventTableHTML;
    }

    /**
     * show events details
     */
    function showEventsDetails(){
        var eventDetails = JSON.parse('<?php echo json_encode($eventsDetails_js); ?>');
        var eventDetailsText = "";
        document.getElementById("").innerHTML = eventDetailsText;
    }

    /**
     * show venue info
     */
    function showVenueInfo(){
        var venueInfo = JSON.parse('<?php echo json_encode($venueInfo_js); ?>');
        var venueInfoText = "";
        document.getElementById("").innerHTML = venueInfoText;
    }

    /**
     * show venue image
     */
    function showVenueImage(){
        var venueImage = JSON.parse('<?php echo json_encode($venueImage_js); ?>');
        var venueImageText = "";
        document.getElementById("").innerHTML = venueImageText;
    }

    /**
     * show venue map
     */
    function showVenueMap(){

    }
</script>
</body>
</html>

