<?php
error_reporting(0);
$keyword = "";
$distance = 10;
$category = "Default";
$otherLocationInput = "";
$otherLocationLat = "";
$otherLocationLong = "";

$searchDataPosted = false;
$searchDataPosted = ($_SERVER["REQUEST_METHOD"] == 'POST')&&(isset($_POST["submit"]));
$error = false;
//echo $searchDataPosted."<br>";
if($searchDataPosted) {
    //echo 'a';
    $keyword = $_POST['Keywords'];
    $distance = $_POST['Distance'];
    $category = $_POST['Category'];

    if (isset($_POST["otherLocation"])) {
        $otherLocationInput = $_POST['otherLocation'];
        //echo $_POST["otherLocation"] . "a<br>";
    }
}

$selectedEventPosted = false;
$selectedEventPosted = ($_SERVER["REQUEST_METHOD"] == 'GET')&&isset($_GET["eventID"]);

if($selectedEventPosted) {
    $keyword = $_GET['Keywords'];
    $distance = $_GET['Distance'];
    $category = $_GET['Category'];
    if (isset($_GET["otherLocation"])) {
        $otherLocationInput = $_GET['otherLocation'];
        //echo $_POST["otherLocation"] . "<br>";
        $otherLocationLat = $_GET['otherLocationLat'];
        $otherLocationLong = $_GET['otherLocationLong'];
    }
}

?>



<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Event Search</title>
    <style type="text/css">
        .form-wrap{
            border:0px;
            margin:auto;
            width:100%;
        }
        form{
            width:40%;
            margin-left:30%;
            padding-left: 10px;
            padding-right: 10px;
            margin-top: 50px;
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

        /** Events Search Table style
        -------------------------------------------------------- */
        #no-record{
            width: 70%;
            margin-left: 15%;
            background-color: rgb(240,240,240);
            text-align: center;
            font-size:17px;
            padding-top:4px;
            height:20px;
            border: 2px solid rgb(221,221,221);
        }
        .NA{
            padding-left:15px;
        }
        .eventsTableWrap{
            border:0px;
            margin:auto;
            width:100%;
            text-align: center;
        }
        .eventsTable{
            width: 90%;
            margin-left: 5%;
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
        }

        .eventsTable tr th{
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
            text-align: center;
        }

        .eventsTable tr td{
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
        }

        .eventDateWrap{
            text-align: center;
        }
        .eventDate{
            line-height: 2px;
            font-size: 15px;
        }

        .eventAnchorWrap{

        }
        .eventAnchor{
            color: black;
            text-decoration:none;
            padding-left: 15px;
            font-size:15px;
        }
        .eventAnchor:hover{
            color: rgb(151,150,155);
        }

        .eventIconWrap{
            text-align: center;
        }
        .eventsIcon{
            width: 70px;
            padding-top: 5%;
            padding-bottom: 5%;
        }

        .eventGenre{
            padding-left: 10px;
            font-size: 15px;
        }
        .eventGenreWrap{

        }

        /* event Details style
        ------------------------------------------------------------------------------------------*/
        .eventDetailTable{
            border: 0px;
            justify-content: center;
            display:flex;
        }
        .eventTitle{
            line-height: 0px;
            font-size:23px;
        }
        .eventInfoWrap{
            text-align: left;
        }
        .title{
            font-size: 20px;
            line-height: 0px;
        }
        .eventInfoWrap p{
            line-height:17px;
            font-size: 16px;
        }
        .seatmapWrap{
            text-align: center;
            padding-top: 0px;
        }
        .seatMap{
            width: 600px;
            height: 400px;
            margin-top:10px;

        }
        .eventDetailAnchor{
            color: black;
            text-decoration:none;
        }
        .eventDetailAnchor:hover{
            color: rgb(151,150,155);
            text-decoration:none;
        }

        /* venue button style
        ----------------------------------------------------------------------------------------- */

        .clickInfo{
            color:grey;
            text-decoration:none;
        }

        .hidden{
            display:none;
        }

        .arrow {
            weight:60px;
            height: 20px;
        }
        .clickContent{
            margin-top: 20px;
        }

        /* venue info and image style
        --------------------------------------------------------------------------------------------*/
        .no-venue{
            text-align: center;
            font-weight: bold;
            font-size:15px;
            width: 1000px;
        }
        .venueImageWrap{
            justify-content: center;
            border: 0px;
        }
        .venueImageTable{
            width: 1200px;
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
        }
        .venue-image-wrap{
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
            padding-bottom:0px;
        }
        .venue-image{
            padding-top: 8px;
        }
        /* venue info style
        --------------------------------------------------------------------------------------------------------*/
        .venueInfoWrap{
            justify-content: center;
            border: 0px;
        }
        .venueInfoTable {
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
        }
        .info-title-wrap{
            text-align: right;
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
        }
        .info-title{
            font-size:15px;
            line-height:0px;
            font-weight: bold;
        }
        #venue-mp{
            float: left;
            padding-top:10px;
            padding-right:150px;
            padding-bottom:10px;
        }
        #venue-btn{
            padding-top:60px;
            padding-left:20px;
            padding-right:40px;
            float:left;
        }
        .info-content-wrap{
            text-align: center;
            border-collapse: collapse;
            border: 2px solid #c8c8c8;
            font-size: 15px;
            line-height:0px;
        }
        /* Map Option style
        -------------------------------------------------------------------------------------------------- */
        #map{
            height: 350px;
            width: 350px;
            position: absolute;
            left: 0px;
            top: 0px;
        }
        #mapOptions{
            left: 0px;
            top: 0px;
            padding:0px;
            margin:0px;

            position: absolute;
            z-index: 50;
            height:90px;
            width:80px;

        }
        .mapOptions-button{
            background-color: rgb(240,240,240);
            color:black;
            text-align:center;
            height:30px;
            width: 80px;
            margin: 0px;
            padding-top: 0.5px;

            cursor:pointer;
            #position:absolute;
        }
        .mapOptions-button:hover {
            background-color: rgb(210,210,210);
            color: rgb(146,146,156);
        }
        .mapOptions-button p{
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="form-wrap">
        <form method = "post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <h1><i>Event Search</i></h1>
            <hr/>
            <label>Keywords</label><input type="text" id="Keywords" name="Keywords" value="<?php echo $keyword; ?>" required><br/>

            <label>Catergories</label>
            <select name = "Category" id="Category">
                <option id='Default' value = "Default" selected>Default</option>
                <option id='Music' value = "Music">Music</option>
                <option id='Sports' value = "Sports">Sports</option>
                <option id='Arts&Theatre' value = "Arts&Theatre">Arts & Theatre</option>
                <option id='Film' value = "Film">Film</option>
                <option id='Miscellaneous' value = "Miscellaneous">Miscellaneous</option>
            </select>
            <br/>

            <label>Distance(Miles)</label><input type = "text" placeholder="10" name="Distance" id="Distance" value= "<?php echo $distance; ?>" required>
            <label>from</label>
            <input type="radio" name="location" id="Here" onclick = "locationInputListener();" <?php if($otherLocationInput=="") echo 'checked'; ?> >Here<br/><input type="hidden" name="currentLocation" id="currentLocation">
            <div class="location-input">
                <input type="radio" name="location" id="Location" onclick = "locationInputListener();" <?php if($otherLocationInput!="") echo 'checked'; ?>><input type="text" name="otherLocation" id="otherLocation" placeholder="location"  value="<?php echo $otherLocationInput; ?>" <?php if($otherLocationInput == "") echo 'disabled';?>>
            </div>
            <br/>

            <div class="button">
                <input type="submit" name="submit" id="search" value="submit" class="submit">
                <button type="button" class="reset" onclick = "clearResultAndForm();">clear</button>
            </div>
        </form>
    </div>
    <br>
    <div id="eventTable" class="eventsTableWrap"></div>
    <div id="map" class="hidden"></div>
    <div id="mapOptions" class="hidden">
        <div  id='mapOptionWalk' class="mapOptions-button" ><p>Walk there</p></div>
        <div  id='mapOptionBike' class="mapOptions-button" ><p>Bike there</p></div>
        <div  id='mapOptionDrive' class="mapOptions-button"><p>Drive there</p></div>
    </div>
    <div id="map-identifier" class="hidden">-1</div>

    <?php
    //Header("Access-Control-Allow-Origin: * ");
    //Header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");

    include 'geoHash.php';
    $ticketMasterApikey = "f5r0Yhc2Dln0m3rlwOpG7TtYKtbSUprn";
    $googleMapApiKey = "AIzaSyAE_BDGG42oJuV4SVrKs37xj9TSjVhOdpE";
    $error = array();


    /**
    * @param get segment id for query string
    * @return string
    */
    function getSegmentID($category){
        if($category == "Music"){
            $segmentID = "KZFzniwnSyZfZ7v7nJ";
        }
        else if($category == "Sports"){
            $segmentID = "KZFzniwnSyZfZ7v7nE";
        }
        else if($category == "Arts&Theatre"){
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

                foreach($eventsArray as $i => $eventItem) {

                    $eventsResult[$i]["name"] = array_key_exists('name', $eventItem) ? $eventItem["name"] : "";
                    $eventsResult[$i]["id"] = array_key_exists('id', $eventItem) ? $eventItem["id"] : "";

                    //extract icon
                    if (array_key_exists("images", $eventItem)){
                            $eventsResult[$i]["icon"] = "";
                            foreach($eventItem['images'] as $ele){
                                if(array_key_exists('url',$ele)){
                                    $eventsResult[$i]["icon"] = $ele['url'];
                                    break;
                                }
                            }
                    }
                    else{
                            $eventsResult[$i]["icon"] = "";
                    }


                    //extract date
                    if (array_key_exists('dates', $eventItem)) {
                        if (array_key_exists('start', $eventItem["dates"])) {
                            $eventsResult[$i]["localDate"] = array_key_exists('localDate', $eventItem["dates"]["start"]) ? $eventItem["dates"]["start"]["localDate"] : "";
                            $eventsResult[$i]["localTime"] = array_key_exists('localTime', $eventItem["dates"]["start"]) ? $eventItem["dates"]["start"]["localTime"] : "";
                        } else {
                            $eventsResult[$i]["localDate"] = "";
                            $eventsResult[$i]["localTime"] = "";
                        }
                    } else {
                        $eventsResult[$i]["localDate"] = "";
                        $eventsResult[$i]["localTime"] = "";
                    }
                    //extract segment
                    if (array_key_exists('classifications', $eventItem)) {
                        if (array_key_exists("segment", $eventItem['classifications'][0])) {
                            if (array_key_exists('name', $eventItem["classifications"][0]['segment'])) {
                                $eventsResult[$i]["segment"] = $eventItem['classifications'][0]['segment']['name'];
                            } else {
                                $eventsResult[$i]["segment"] = "";
                            }
                        } else {
                            $eventsResult[$i]["segment"] = "";
                        }
                    } else {
                        $eventsResult[$i]["segment"] = "";
                    }


                    //extract venue
                    $eventsResult[$i]["venues"] = array();
                    if (array_key_exists('_embedded', $eventItem)) {
                        if (array_key_exists('venues', $eventItem["_embedded"])) {
                            $eventsResult[$i]["venues"]["name"] = array_key_exists('name', $eventItem["_embedded"]["venues"][0]) ? $eventItem["_embedded"]["venues"][0]["name"] : "";
                            $eventsResult[$i]["venues"]["location"] = array_key_exists('location', $eventItem["_embedded"]["venues"][0]) ? $eventItem["_embedded"]["venues"][0]["location"] : "";
                        } else {
                            $eventsResult[$i]["venues"]["name"] = "";
                            $eventsResult[$i]["venues"]["location"] = "";
                        }
                    } else {
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
        if(array_key_exists('name',$eventDetailsArray)){
            if($eventDetailsArray["name"] != 'Undefined'){
                $eventDetailsResult["name"] = $eventDetailsArray["name"];
            }
            else{
                $eventDetailsResult["name"] = "";
            }
        }
        else{
            $eventDetailsResult["name"] = "";
        }

        //extract dates information
        $eventDetailsResult["date"] = array();
        if(array_key_exists('dates',$eventDetailsArray)){
            if(array_key_exists('start',$eventDetailsArray['dates'])){
                if(array_key_exists('localDate',$eventDetailsArray["dates"]["start"])){
                    if($eventDetailsArray["dates"]["start"]["localDate"] != 'Undefined'){
                        $eventDetailsResult["date"]["localDate"] = $eventDetailsArray["dates"]["start"]["localDate"];
                    }
                }
                if(array_key_exists('localTime',$eventDetailsArray["dates"]["start"])){
                    if($eventDetailsArray["dates"]["start"]["localTime"] != 'Undefined'){
                        $eventDetailsResult["date"]["localTime"] = $eventDetailsArray["dates"]["start"]["localTime"];
                    }
                }
            }
        }

        //extract genre information
        $eventDetailsResult["genre"] = array();
        if(array_key_exists('classifications',$eventDetailsArray)){
            foreach($eventDetailsArray["classifications"] as $ele){
                $temp = array();
                if(array_key_exists('segment',$ele)){
                    if(array_key_exists('name',$ele['segment'])){
                        if($ele['segment']['name'] != 'Undefined'){
                            $temp["segment"] = $ele["segment"]['name'];
                        }
                    }
                }
                if(array_key_exists('genre',$ele)){
                    if(array_key_exists('name',$ele['genre'])){
                        if($ele['genre']['name'] != 'Undefined'){
                            $temp["genre"] = $ele["genre"]['name'];
                        }
                    }
                }
                if(array_key_exists('subGenre',$ele)){
                    if(array_key_exists('name',$ele['subGenre'])){
                        if($ele['subGenre']['name'] != 'Undefined'){
                            $temp["subGenre"] = $ele["subGenre"]['name'];
                        }
                    }
                }
                if(array_key_exists('type',$ele)){
                    if(array_key_exists('name',$ele['type'])){
                        if($ele['type']['name'] != 'Undefined'){
                            $temp["type"] = $ele["type"]['name'];
                        }
                    }
                }
                if(array_key_exists('subType',$ele)) {
                    if(array_key_exists('name',$ele['subType'])){
                        if($ele['subType']['name'] != 'Undefined'){
                            $temp["subType"] = $ele["subType"]['name'];
                        }
                    }
                }
                array_push($eventDetailsResult['genre'],$temp);
            }
        }

        //extract artist information
        $eventDetailsResult["artists"] = array();
        if(array_key_exists('_embedded',$eventDetailsArray)){
            if(array_key_exists('attractions',$eventDetailsArray["_embedded"])) {
                foreach ($eventDetailsArray["_embedded"]["attractions"] as $i => $ele) {
                    if(array_key_exists('name',$ele)){
                        if($ele["name"] != 'Undefined'){
                            $eventDetailsResult["artists"][$i]["name"] = $ele["name"];
                        }
                    }
                    if(array_key_exists('url',$ele)){
                        if($ele["url"] != 'Undefined'){
                            $eventDetailsResult["artists"][$i]["url"] = $ele["url"];
                        }
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
                        if($ele["name"] != 'Undefined'){
                            $eventDetailsResult["venues"][$i]["name"] = $ele["name"];
                        }
                    }
                }
            }
        }

        //extract price range
        $eventDetailsResult['priceRanges'] = array();
        if(array_key_exists('priceRanges',$eventDetailsArray)){
            foreach($eventDetailsArray["priceRanges"] as $ele){
                $tempPrice = array();
                if(array_key_exists('min',$ele)){
                    if( $ele["min"] != 'Undefined'){
                        $tempPrice["min"] = $ele["min"];
                    }
                }
                if(array_key_exists('max',$ele)){
                    if($ele["max"] != 'Undefined'){
                        $tempPrice["max"] = $ele["max"];
                    }
                }
                if(array_key_exists('currency',$ele)){
                    if($ele['currency'] != 'Undefined'){
                        $tempPrice['currency'] = $ele['currency'];
                    }
                }
                array_push($eventDetailsResult['priceRanges'],$tempPrice);
            }
        }
        //extract ticketStatus information
        if(array_key_exists('dates',$eventDetailsArray)){
            if(array_key_exists('status',$eventDetailsArray["dates"])){
                if(array_key_exists('code',$eventDetailsArray["dates"]["status"])){
                    if($eventDetailsArray["dates"]["status"]["code"] != 'Undefined'){
                        $eventDetailsResult["ticketStatus"] = $eventDetailsArray["dates"]["status"]["code"];
                    }
                }
         }
        }
        //extract seatmap
        if(array_key_exists('seatmap',$eventDetailsArray)){
            if(array_key_exists('staticUrl',$eventDetailsArray["seatmap"])){
                if($eventDetailsArray["seatmap"]["staticUrl"] != 'Undefined'){
                    $eventDetailsResult["seatmap"] = $eventDetailsArray["seatmap"]["staticUrl"];
                }
            }
        }
        //extract buy ticket at url
        if(array_key_exists('url',$eventDetailsArray)){
            if($eventDetailsArray["url"]!='Undefined'){
                $eventDetailsResult["buyTicketAt"] = $eventDetailsArray["url"];
            }
        }

        return $eventDetailsResult;
    }


    /**
    * parse form data
    */
    $eventsResult_js = "";
    $eventsDetails_js = "";
    $venueResult_js = "";


    $searchDataPosted = false;
    $searchDataPosted = ($_SERVER["REQUEST_METHOD"] == 'POST')&&(isset($_POST["submit"]));
    //echo $searchDataPosted."<br>";
    if($searchDataPosted){


        $keyword = $_POST['Keywords'];
        $distance = $_POST['Distance'];
        $category = $_POST['Category'];

        if(isset($_POST["otherLocation"])){
            $otherLocationInput = $_POST['otherLocation'];
            //echo $_POST["otherLocation"]."<br>";
        }

        /**
        * get Super global Variables ---- event form keywords
        */
        $keyword = str_replace(' ','+',$_POST["Keywords"]);
        $keyword = urlencode($keyword);
        /*
        echo $_POST["Keywords"]."<br>";
        echo $keyword."<br>";
        */
        $segmentID = getSegmentID($_POST["Category"]);
        $distance = $_POST["Distance"];
        $distance = urlencode($distance);

        if(isset($_POST['otherLocation'])){
            if($_POST["otherLocation"] != ""){
                $otherLocationName = str_replace(' ', '+', $_POST["otherLocation"]);
                $GoogleMapQuery ="https://maps.googleapis.com/maps/api/geocode/json?"."address=".$otherLocationName."&key=".$googleMapApiKey;
                $GoogleMapResult = file_get_contents($GoogleMapQuery);
                $GoogleMapResultArray = json_decode($GoogleMapResult, true);


                if($GoogleMapResultArray["status"]=="OK"){
                    $locationLat = $GoogleMapResultArray["results"][0]["geometry"]["location"]["lat"];
                    $locationLong = $GoogleMapResultArray["results"][0]["geometry"]["location"]["lng"];
                    $geoPoint = encode($locationLat, $locationLong);

                    $otherLocationLat = $locationLat;
                    $otherLocationLong = $locationLong;
                }
                else{
                    $geoPoint = "";
                }
            }
        }
        else{
            $currentLocation = explode(" ",$_POST["currentLocation"]);
            //echo $_POST["currentLocation"].'<br>';
            //echo "<pre>"; print_r($currentLocation); echo "</pre>";
            $locationLat = $currentLocation[0];
            $locationLong = $currentLocation[1];
            $geoPoint = encode($locationLat, $locationLong);
        }

        $geoPoint = urlencode($geoPoint);

        if($segmentID == ""){
            $ticketMasterEventSearchQuery =  "https://app.ticketmaster.com/discovery/v2/events.json?".
                "&apikey=".$ticketMasterApikey."&keyword=".$keyword."&unit=miles&radius=".$distance."&geoPoint=".$geoPoint;
        }
        else {
            $ticketMasterEventSearchQuery = "https://app.ticketmaster.com/discovery/v2/events.json?".
                "&apikey=".$ticketMasterApikey."&keyword=".$keyword."&unit=miles&radius=".$distance."&segmentId=".$segmentID.
                "&geoPoint=".$geoPoint;
        }

        //echo $keyword."<br>";
        //echo $ticketMasterEventSearchQuery."<br>";

        /**
        * get Event Search results JSON
        */
        $ticketMasterEventSearchResult = getTMQueryResult($ticketMasterEventSearchQuery);

        if($ticketMasterEventSearchResult == null){
            $error = true;
        }
        //echo '<pre>'; print_r($ticketMasterEventSearchResult); echo '</pre>';
        /**
        * fetch useful information results and save to results array
        */
        $eventsResult_js = fetchEvents($ticketMasterEventSearchResult);
        //echo '<pre>'; print_r($eventsResult_js); echo '</pre>';
    }

    /**
    * parse event data
    */
    $selectedEventPosted = false;
    $selectedEventPosted = ($_SERVER["REQUEST_METHOD"] == 'GET')&&isset($_GET["eventID"]);
    if($selectedEventPosted){
        $eventID = $_GET["eventID"];
        $eventID = urlencode($eventID);
        //echo $_GET["eventID"]."<br>";
        $ticketMasterEventDetailQuery = "https://app.ticketmaster.com/discovery/v2/events/"
                                        .$eventID.".json?apikey=".$ticketMasterApikey;
        //echo $ticketMasterEventDetailQuery."<br>";
        $eventDetails = getTMQueryResult($ticketMasterEventDetailQuery);
        //echo '<pre>'; print_r($eventDetails); echo '</pre>';
        $eventsDetails_js = fetchEventDetails($eventDetails);
        //echo '<pre>'; print_r($eventDetails_js); echo '</pre>';
        //if has venue name
        $venueName = $eventsDetails_js["venues"][0]["name"];
        $venueName = urlencode($venueName);
        $venueName = str_replace(" ","+",$venueName);

        //echo $venueName."<br>";
        sleep(1.5);
        $ticketMasterVenueQuery = "https://app.ticketmaster.com/discovery/v2/venues?apikey=".$ticketMasterApikey."&keyword=".$venueName;
        $venueResult = getTMQueryResult($ticketMasterVenueQuery);
        $venueResult_js = $venueResult;
    }
    ?>

    <script type = "text/javascript">




        //keep value
        var category = '<?php echo $category; ?>';

        document.getElementById(category).selected = true;

        var otherLocationInput = '<?php echo $otherLocationInput?>';

        console.log(otherLocationInput);

        // get ip address
        document.getElementById("search").disabled = true;
        var ipapi = new XMLHttpRequest();
        ipapi.open("GET","http://ip-api.com/json",false);
        ipapi.send();
        console.log(JSON.parse(ipapi.responseText));
        var lat = JSON.parse(ipapi.responseText).lat;
        var long = JSON.parse(ipapi.responseText).lon;
        var venue_lat = lat;
        var venue_long = long;
        console.log(lat,long);

        var currentLocation = document.getElementById("currentLocation");
        currentLocation.value = lat + " " + long;
        console.log(currentLocation.value);
        document.getElementById("search").disabled = false;

        /**
        * clear form
        */
        function clearResultAndForm(){
            console.log('clear');
            document.getElementById("Keywords").value = "";
            document.getElementById("Keywords").required = true;
            document.getElementById("Category").options[0].selected = true;
            document.getElementById("Distance").value = "10";
            document.getElementById("Distance").required = true;
            document.getElementById("Here").checked = true;
            document.getElementById("otherLocation").disabled = true;
            document.getElementById("otherLocation").value = "";
            var map = document.getElementById("map");
            document.body.appendChild(map);
            document.body.appendChild(mapOptions);
            document.getElementById("eventTable").innerHTML = "";
            document.getElementById("map").style.display = "none";
            document.getElementById("mapOptions").style.display = "none";
        }

        /**
         */

        function locationInputListener(){
            var here = document.getElementById('Here');
            if(here.checked === true){
                document.getElementById('otherLocation').required = false;
                document.getElementById('otherLocation').disabled = true;
                document.getElementById("otherLocation").value = "";
            }
            else{
                document.getElementById('otherLocation').disabled = false;
                document.getElementById('otherLocation').required = true;
            }
        }


        /**
        * show event table
        */
        var eventResult = <?php echo json_encode($eventsResult_js,JSON_HEX_APOS); ?>;
        console.log(eventResult === "");
        if(eventResult !== ""){
            if(eventResult.length !== 0){
                showEventsTable(eventResult);
            }
            else{
                document.getElementById("eventTable").innerHTML = "<div id='no-record'>No records have been found</div>";
            }
        }

        var error = <?php echo json_encode($error);?>;
        console.log(error);
        if(error == true){
            alert('check your input distance, should be interger!');
        }

        function showEventsTable(eventResult){

            //get params transform
            var getParams = "";
            var keyword = '<?php echo $keyword; ?>';
            var category = '<?php echo $category; ?>';
            var distance = '<?php echo $distance; ?>';
            var otherLocation = '<?php echo $otherLocationInput; ?>';
            var otherLocationLat = '<?php echo $otherLocationLat; ?>';
            var otherLocationLong = '<?php echo $otherLocationLong; ?>';
            getParams += '&Keywords=' + keyword + '&Distance=' + distance + '&Category=' + category;

            if(otherLocation !== ""){
                getParams += '&otherLocation=' + otherLocation + '&otherLocationLat=' + otherLocationLat + '&otherLocationLong=' + otherLocationLong;
            }

            //show table
            console.log(eventResult);
            var eventTableHTML = "";
            //show table
            eventTableHTML += '<table class="eventsTable" border = "2" style = "text-align:left;">';
            //show header
            eventTableHTML += "<tr><th>Date</th>" + "<th>Icon</th>" + "<th>Event</th>" + "<th>Genre</th>" + "<th>Venue</th></tr>";
            //show table data
            for(let i in eventResult){
                eventTableHTML += "<tr>";
                //show each rows
                //show date
                if(eventResult[i].localDate !== "" || eventResult[i].localTime !== "" ){
                    eventTableHTML += "<td class='eventDateWrap'><p class='eventDate'>"+ eventResult[i].localDate +"</p><p class='eventDate'>" +  eventResult[i].localTime +"</p></td>";
                }
                else{
                    eventTableHTML += "<td class='eventDateWrap'><p class='NA'>N/A</p></td>";
                }

                //show icon
                if(eventResult[i].icon !== ""){
                    eventTableHTML += "<td class='eventIconWrap'>" + "<img class='eventsIcon' src='" + eventResult[i].icon + "' alt='logo'>" + "</td>";
                }
                else{
                    eventTableHTML += "<td class='eventIconWrap'><p class='NA'>N/A<p></td>";
                }

                //show event name
                if(eventResult[i].name !== ""){
                    if(eventResult[i].id !== ""){
                        eventTableHTML += "<td class='eventAnchorWrap'><a class='eventAnchor' href = 'index.php?eventID=" + eventResult[i].id + getParams + "'>" + eventResult[i].name + "</a></td>";
                    }
                    else{
                        eventTableHTML += "<td class='eventAnchorWrap'><a class='eventAnchor' href = 'javascript:;'>" + eventResult[i].name + "</a></td>";
                    }
                }
                else{
                    if(eventResult[i].id !==""){
                        eventTableHTML += "<td class='eventAnchorWrap'><a class='eventAnchor' href = 'index.php?eventID=" + eventResult[i].id + getParams + "'>N/A</a></td>";
                    }
                    else{
                        eventTableHTML += "<td class='eventAnchorWrap'><a class='eventAnchor' href = 'javascript:;'>N/A</a></td>";
                    }
                }

                //show genre
                if(eventResult[i].segment != ""){
                    eventTableHTML += "<td class='eventGenreWrap'><p class='eventGenre'>" + eventResult[i].segment + "</p></td>";
                }
                else{
                    eventTableHTML += "<td class='eventGenreWrap'><p class='NA'>N/A</p></td>";
                }

                //show venue
                if(eventResult[i].venues.name !== ""){
                    if(eventResult[i].venues.location !== ""){
                        eventTableHTML += "<td class='eventAnchorWrap'>" +
                            "<a id = '" + i + "' href='javascript:void(0)'" +
                            " onclick='showVenueMap(" + eventResult[i].venues.location.longitude + "," + eventResult[i].venues.location.latitude + ",\"" +i +"\");'" +
                            " class='eventAnchor'>"
                            + eventResult[i].venues.name +
                            "</a>" +
                            "</td>";
                        eventTableHTML += "</tr>";
                    }
                    else{
                        eventTableHTML += "<td class='eventAnchorWrap'>" +
                            "<a id = '" + i + "' href='javascript:void(0)'" +
                            " class='eventAnchor'>"
                            + eventResult[i].venues.name +
                            "</a>" +
                            "</td>";
                        eventTableHTML += "</tr>";
                    }
                }
                else{
                    if(eventResult[i].venues.location !== ""){
                        eventTableHTML += "<td class='eventAnchorWrap'>" +
                            "<a id = '" + i + "' href='javascript:void(0)'" +
                            " onclick='showVenueMap(" + eventResult[i].venues.location.longitude + "," + eventResult[i].venues.location.latitude + ",\"" +i +"\");'" +
                            " class='eventAnchor'>"
                            + 'N/A' +
                            "</a>" +
                            "</td>";
                        eventTableHTML += "</tr>";
                    }
                    else{
                        eventTableHTML += "<td class='eventAnchorWrap'><p class='NA'>N/A</p></td>";
                        eventTableHTML += "</tr>";
                    }
                }

            }
            //end show table
            eventTableHTML += '</table>';

            //console.log(eventTableHTML);
            document.getElementById("eventTable").innerHTML = eventTableHTML;
        }

        /**
        * show event and venue details
        */
        var eventDetails = <?php echo json_encode($eventsDetails_js,JSON_HEX_APOS); ?>;
        if(eventDetails.length !== 0){
            showEventsDetails(eventDetails);
        }
        function showEventsDetails(eventDetails){
            //src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAE_BDGG42oJuV4SVrKs37xj9TSjVhOdpE">
            console.log(eventDetails);
            var eventDetailsHTML = "";
            //show event
            eventDetailsHTML += '<table class="eventDetailTable" border = "0" style = "text-align:left;">';
            //show event title
            eventDetailsHTML += "<h3 class='eventTitle'>" + eventDetails.name +"</h3>";

            eventDetailsHTML +="<tr><td class='eventDetail'>";
            eventDetailsHTML += showTableContent(eventDetails);
            eventDetailsHTML += "<td>";

            if(eventDetails.hasOwnProperty('seatmap')){
                eventDetailsHTML +='<td class="seatmapWrap"><img class="seatMap" src=' + eventDetails.seatmap + " alt='seatmap'></td>"
            }
            eventDetailsHTML += "</table>";

            eventDetailsHTML += showVenueContent();

            document.getElementById("eventTable").innerHTML = eventDetailsHTML;

            var venueBtnDiv = document.getElementById('venue-btn');
            var venueMapDiv = document.getElementById('venue-mp');
            if(document.getElementById('venue-map-wrap')){
                var venueMapWrap = document.getElementById('venue-map-wrap');

                if(venueMapWrap.textContent !== "N/A"){
                    var latLong_str = document.getElementById('venue-map').textContent;
                    var latLong = latLong_str.split(" ");
                    venue_lat = latLong[0];
                    venue_long = latLong[1];
                    console.log(latLong_str);
                    console.log(latLong);
                    console.log(venue_lat);
                    console.log(venue_long);

                    var map = document.getElementById('map');
                    var mapOptions = document.getElementById('mapOptions');

                    venueMapWrap.style.position = 'relative';
                    map.style.position = 'static';
                    mapOptions.style.position = 'static';

                    venueBtnDiv.appendChild(mapOptions);
                    venueMapDiv.appendChild(map);

                    //get location
                    var mapOptionsLocationLeft = 30;
                    var mapOptionsLocationTop = 60;
                    var mapLocationLeft = 150;
                    var mapLocationTop = 10;

                    //i(lat, long);
                    //venueMapWrap.style.position = 'relative';
                    //document.getElementById("map").style.left = mapLocationLeft+"px";
                    //document.getElementById("map").style.top = mapLocationTop+"px";
                    //document.getElementById("mapOptions").style.left = mapOptionsLocationLeft+"px";
                    //document.getElementById("mapOptions").style.top = mapOptionsLocationTop+"px";

                    document.getElementById("venue-mp").style.display = "block";
                    document.getElementById("venue-btn").style.display = "block";
                    document.getElementById("venue-btn").style.float = "left";
                    document.getElementById("map").style.display = "block";
                    document.getElementById("mapOptions").style.display = "block";

                }
            }

        }

        /**
         *  show event table content
        */
        function showTableContent(eventDetails){
            var eventDetailsHTML = "";
            eventDetailsHTML += '<div class="eventInfoWrap">';
            //show date
            if(eventDetails.hasOwnProperty('date')){
                if(Object.keys(eventDetails.date).length !== 0){
                    eventDetailsHTML += '<h3 class="title">Date</h3><p>';
                    if(eventDetails.date.hasOwnProperty('localDate')){
                        eventDetailsHTML += eventDetails.date.localDate + " ";
                    }
                    if(eventDetails.date.hasOwnProperty('localTime')){
                        eventDetailsHTML += eventDetails.date.localTime;
                    }
                    eventDetailsHTML +="</p>";
                }
            }
            //show artists
            if(eventDetails.hasOwnProperty('artists')){
                if(eventDetails.artists.length !== 0){
                    eventDetailsHTML +='<h3 class="title">Artist/Team</h3><p>';
                    var sign = true;
                    for(let i in eventDetails.artists){
                        if(eventDetails.artists[i].hasOwnProperty('name')){
                            if(sign === true){
                                if(eventDetails.artists[i].hasOwnProperty('url')){
                                    eventDetailsHTML += "<a target = '_blank' class='eventDetailAnchor' href='"+ eventDetails.artists[i].url+ "'>" + eventDetails.artists[i].name + "</a>" ;
                                }
                                else{
                                    eventDetailsHTML += "<a class='eventDetailAnchor' href='javascript:;'>" + eventDetails.artists[i].name + "</a>" ;
                                }
                            }
                            else{
                                if(eventDetails.artists[i].hasOwnProperty('url')){
                                    eventDetailsHTML += " | " + "<a target= '_blank' class='eventDetailAnchor' href='"+ eventDetails.artists[i].url+ "'>" + eventDetails.artists[i].name + "</a>" ;
                                }
                                else{
                                    eventDetailsHTML += " | " + "<a class='eventDetailAnchor' href='javascript:;'>" + eventDetails.artists[i].name + "</a>" ;
                                }
                            }
                            sign = false;
                        }
                    }
                    eventDetailsHTML +="<p>";
                }

            }
            //show venue
            if(eventDetails.hasOwnProperty('venues')){
                if(eventDetails.venues.length !== 0){
                    if(Object.keys(eventDetails.venues[0]).length !== 0){
                        eventDetailsHTML += '<h3 class="title">Venue</h3><p>' + eventDetails.venues[0].name + "</p>";
                    }
                }

            }
            //show genre
            if(eventDetails.hasOwnProperty('genre')){
                if(eventDetails.genre.length!==0){
                    if(Object.keys(eventDetails.genre[0]).length !== 0 ){
                        var sign = true;
                        eventDetailsHTML += '<h3 class="title">Genres</h3><p>';

                        if(eventDetails.genre[0].hasOwnProperty('subGenre')){
                            if(sign === true){
                                eventDetailsHTML += eventDetails.genre[0].subGenre;
                            }
                            else{
                                eventDetailsHTML += " | " +eventDetails.genre[0].subGenre;
                            }
                            sign = false;
                        }
                        if(eventDetails.genre[0].hasOwnProperty('genre')){
                            if(sign === true){
                                eventDetailsHTML += eventDetails.genre[0].genre;
                            }
                            else{
                                eventDetailsHTML += " | " +eventDetails.genre[0].genre;
                            }
                            sign = false;
                        }
                        if(eventDetails.genre[0].hasOwnProperty('segment')){
                            if(sign === true){
                                eventDetailsHTML += eventDetails.genre[0].segment;
                            }
                            else{
                                eventDetailsHTML += " | " +eventDetails.genre[0].segment;
                            }
                            sign = false;
                        }
                        if(eventDetails.genre[0].hasOwnProperty('subType')){
                            if(sign === true){
                                eventDetailsHTML += eventDetails.genre[0].subType;
                            }
                            else{
                                eventDetailsHTML += " | " +eventDetails.genre[0].subType;
                            }
                            sign = false;
                        }
                        if(eventDetails.genre[0].hasOwnProperty('type')){
                            if(sign === true){
                                eventDetailsHTML += eventDetails.genre[0].type;
                            }
                            else{
                                eventDetailsHTML += " | " +eventDetails.genre[0].type;
                            }
                            sign = false;
                        }
                        eventDetailsHTML +="</p>";
                    }
                }
            }
            //show priceRanges
            if(eventDetails.hasOwnProperty("priceRanges")){
                if(eventDetails.priceRanges.length!==0){
                    if(Object.keys(eventDetails.priceRanges[0]).length!== 0){

                        eventDetailsHTML += '<h3 class="title">Price Ranges</h3><p>';
                        if(eventDetails.priceRanges[0].hasOwnProperty('min')&&(eventDetails.priceRanges[0].hasOwnProperty('max') === false)){
                            eventDetailsHTML += eventDetails.priceRanges[0].min;
                        }
                        else if(eventDetails.priceRanges[0].hasOwnProperty('max')&&(eventDetails.priceRanges[0].hasOwnProperty('min') === false)){
                            eventDetailsHTML += eventDetails.priceRanges[0].max;
                        }
                        else{
                            eventDetailsHTML += eventDetails.priceRanges[0].min + " - " + eventDetails.priceRanges[0].max;
                        }

                        if(eventDetails.priceRanges[0].hasOwnProperty('currency')){
                            eventDetailsHTML += " " + eventDetails.priceRanges[0].currency +"</p>";
                        }
                        else{
                            eventDetailsHTML += " USD</p>";
                        }
                    }
                }
            }
            //show ticketStatus
            if(eventDetails.hasOwnProperty("ticketStatus")){
                eventDetailsHTML += '<h3 class="title">Ticket Status</h3><p>' + eventDetails.ticketStatus + "</p>";
            }
            //show buy ticket at
            if(eventDetails.hasOwnProperty("buyTicketAt")){
                eventDetailsHTML += '<h3 class="title">Buy Ticket At</h3><p><a target="_blank" class="eventDetailAnchor" href=\'' + eventDetails.buyTicketAt + "'>" + "ticketMaster" + "</a></p>";
            }

            eventDetailsHTML += "</div>";
            return eventDetailsHTML;
        }

        /**
        * show venue button
        */
        function showVenueContent(){

            var venueHTML = "";
            //show first expand button
            venueHTML += "<a  href='javascript:;' onclick= 'showVenueInfo();' id='venueInfoDown' class='clickInfo'>" +
                              "<div class='clickContent'>click to show venue info</div>" +
                              "<img class='arrow' src='http://csci571.com/hw/hw6/images/arrow_down.png'>"+
                         "</a>";

            //show first overlap button
            venueHTML += "<a href='javascript:;' onclick= 'hideVenueInfo();' id='venueInfoUp' class='hidden clickInfo'>" +
                            "<div class='clickContent'>click to hide venue info</div>" +
                            "<img class='arrow' src='http://csci571.com/hw/hw6/images/arrow_up.png' onclick='hideVenueInfo();'>" +
                         "</a>";

            //show first -- venue info div
            venueHTML += "<div id='venueInfoContent' class='hidden venueInfoWrap'>";
            venueHTML += showVenueInfoContent();
            venueHTML += "</div>";

            //show second expand button
            venueHTML += "<a  href='javascript:;' onclick='showVenueImage();' id='venueImageDown' class='clickInfo'>" +
                            "<div class='clickContent'>click to show venue photos</div>" +
                            "<img class='arrow' src='http://csci571.com/hw/hw6/images/arrow_down.png'>" +
                         "</a>";

            //show second overlap button
            venueHTML += "<a href='javascript:;' onclick='hideVenueImage();' id='venueImageUp' class='hidden clickInfo'>" +
                            "<div class='clickContent'>click to hide venue photos</div>" +
                            "<img class='arrow' src='http://csci571.com/hw/hw6/images/arrow_up.png'>" +
                         "</a>";

            //show second -- venue image content
            venueHTML += "<div id ='venueImageContent' class='hidden venueImageWrap'>";
            venueHTML += showVenueImageContent();
            venueHTML += "</div>";

            return venueHTML;
        }
        /**
         * show venue info html content
         */
        function showVenueInfoContent(){
            var venueInfo = <?php echo json_encode($venueResult_js,JSON_HEX_APOS); ?>;
            var venueInfoHTML = "";
            console.log(venueInfo);
            venueInfoHTML  += '<table class="venueInfoTable">';
            if(venueInfo.hasOwnProperty('_embedded')){
                if(venueInfo._embedded.hasOwnProperty('venues')){
                    if(venueInfo._embedded.venues.length !== 0){
                        var noName = false;
                        var noMap = false;
                        var noAddress = false;
                        var noCity = false;
                        var noPostal = false;
                        var noUpcoming = false;

                        var venues = venueInfo._embedded.venues[0];
                        var venueContentHTML = ""
                        //show name
                        venueContentHTML += "<tr><td class='info-title-wrap'><p class='info-title'>Name</p></td>";
                        venueContentHTML += "<td class='info-content-wrap'>";
                        if(venues.hasOwnProperty("name")){
                            venueContentHTML += venues.name;
                        }
                        else{
                            venueContentHTML +="N/A";
                            noName = true;

                        }
                        venueContentHTML += "</td>";

                        //show map
                        venueContentHTML += "<tr><td class='info-title-wrap'><p class='info-title'>Map</p></td>";
                        venueContentHTML += "<td id='venue-map-wrap' class='info-content-wrap'>";
                        if(venues.hasOwnProperty('location')){
                            if(venues.location.hasOwnProperty('latitude')&&venues.location.hasOwnProperty('longitude')){
                                if(venues.location['latitude'] != null && venues.location['longitude']!= null){
                                    venueContentHTML += "<p id='venue-map' class='hidden'>" + Number(venues.location.latitude) + " " + Number(venues.location.longitude) + "</p>";
                                    venueContentHTML += "<div id='venue-btn'></div>";
                                    venueContentHTML += "<div id='venue-mp'><div>";
                                }
                                else{
                                    venueContentHTML +="N/A";
                                    noMap = true;
                                }
                            }
                            else{
                                venueContentHTML += "N/A";
                                noMap = true;
                            }
                        }
                        else {
                            venueContentHTML += "N/A";
                            noMap = true;
                        }
                        venueContentHTML += "</td>";

                        //show address
                        venueContentHTML += "<tr><td class='info-title-wrap'><p class='info-title'>Address</p></td>";
                        venueContentHTML += "<td class='info-content-wrap'>";
                        if(venues.hasOwnProperty("address")){
                            if(venues.address.hasOwnProperty("line1")){
                                venueContentHTML += venues.address.line1;
                            }
                            else{
                                venueContentHTML += "N/A";
                                noAddress = true;
                            }
                        }
                        else{
                            venueContentHTML += "N/A";
                            noAddress = true;
                        }
                        venueContentHTML += "</td>";

                        //show city
                        venueContentHTML += "<tr><td class='info-title-wrap'><p class='info-title'>City</p></td>";
                        venueContentHTML += "<td class='info-content-wrap'>";
                        if(venues.hasOwnProperty("city")){
                            if(venues.city.hasOwnProperty("name")){
                                if(venues.hasOwnProperty("state")){
                                    if(venues.state.hasOwnProperty("stateCode")){
                                        venueContentHTML += venues.city.name + "," + venues.state.stateCode;
                                    }
                                    else{
                                        venueContentHTML += venues.city.name;
                                    }
                                }
                                else{
                                    venueContentHTML += venues.city.name;
                                }
                            }
                            else{
                                if(venues.hasOwnProperty("state")){
                                    if(venues.state.hasOwnProperty("stateCode")){
                                        venueContentHTML += venueInfo.state.stateCode;
                                    }
                                    else{
                                        venueContentHTML += "N/A";
                                        noCity = true;
                                    }
                                }
                                else{
                                    venueContentHTML += "N/A";
                                    noCity = true;
                                }
                            }
                        }
                        else{
                            if(venues.hasOwnProperty("state")){
                                if(venues.state.hasOwnProperty("stateCode")){
                                    venueContentHTML += venues.state.stateCode;
                                }
                                else{
                                    venueContentHTML += "N/A";
                                    noCity = true;
                                }
                            }
                            else{
                                venueContentHTML += "N/A";
                                noCity = true;
                            }
                        }
                        venueContentHTML += "</td>";

                        //show postal code
                        venueContentHTML += "<tr><td class='info-title-wrap'><p class='info-title'>Postal Code</p></td>";
                        venueContentHTML += "<td class='info-content-wrap'>";
                        if(venues.hasOwnProperty("postalCode")){
                            venueContentHTML += venues.postalCode;
                        }
                        else{
                            venueContentHTML +="N/A";
                            noPostal = true;
                        }
                        venueContentHTML += "</td>";

                        //show upcoming events
                        venueContentHTML += "<tr><td class='info-title-wrap'><p class='info-title'>Upcoming Events</p></td>";
                        venueContentHTML += "<td class='info-content-wrap'>";
                        if(venues.hasOwnProperty("url")){
                            venueContentHTML += "<a target = '_blank' class='eventAnchor' href='" + venues.url + "'>" + venues.name + "Tickets</a>";
                        }
                        else{
                            venueContentHTML +="N/A";
                            noUpcoming = true;
                        }
                        venueContentHTML += "</td>";

                        //check if all field is N/A then show no records
                        if(noName==true&&noMap==true&&noAddress==true&&noCity==true&&noPostal==true&&noUpcoming==true){
                            venueInfoHTML +="<tr><th class='no-venue'>No Venue Info Found</th>";
                        }
                        else{
                            venueInfoHTML += venueContentHTML;
                        }
                    }
                    else{
                        venueInfoHTML +="<tr><th class='no-venue'>No Venue Info Found</th>";
                    }
                }
                else{
                    venueInfoHTML +="<tr><th class='no-venue'>No Venue Info Found</th>";
                }
            }
            else{
                venueInfoHTML +="<tr><th class='no-venue'>No Venue Info Found</th>";
            }
            venueInfoHTML +="</table>"

            return venueInfoHTML;
        }
        /**
         * venue image html content
         */
        function showVenueImageContent(){

            var venueImage = <?php echo json_encode($venueResult_js,JSON_HEX_APOS); ?>;
            var venueImageHTML = "";
            console.log(venueImage);
            venueImageHTML  += '<table class="venueImageTable">';

            if(venueImage.hasOwnProperty("_embedded")) {
                if (venueImage._embedded.hasOwnProperty("venues")) {
                    if (venueImage._embedded.venues.length !== 0) {
                        if (venueImage._embedded.venues[0].hasOwnProperty('images') === false || (venueImage._embedded.venues[0].hasOwnProperty('images') && venueImage._embedded.venues[0].images.length === 0)) {
                            venueImageHTML += "<tr><th class='no-venue'>No Venue Photo Found</th>";
                        }
                        else {
                            var venueImages = venueImage._embedded.venues[0].images;
                            for (let i in venueImages) {
                                //check width and height
                                var defaultWidth = 1000;
                                var defaultHeight = 500;
                                var imageHeight = 0;
                                var imageWidth = 0;
                                if(venueImages[i].hasOwnProperty('height') && venueImages[i].hasOwnProperty('width')){
                                    imageHeight = venueImages[i].height;
                                    imageWidth = venueImages[i].width;

                                    if(imageWidth >= 1000){
                                        imageHeight = (imageHeight * 1000)/(imageWidth);
                                        imageWidth = 1000;
                                    }
                                }
                                else if(venueImages[i].hasOwnProperty('height')){
                                    imageHeight = defaultHeight;
                                }
                                else if(venueImages[i].hasOwnProperty('width')){
                                    imageWidth = defaultWidth;
                                }
                                else{
                                    imageHeight = defaultHeight;
                                    imageWidth = defaultWidth;
                                }
                                console.log(imageWidth, imageHeight);

                                venueImageHTML += "<tr class='venue-image-row'><td class='venue-image-wrap'><image class='venue-image' alt='venue-image' src='" + venueImages[i].url + "' height='" + imageHeight + "px' width='" + imageWidth + "px' ></td>";
                            }
                        }
                    }
                    else {
                        venueImageHTML += "<tr><th class='no-venue'>No Venue Photo Found</th>";
                    }
                }
                else {
                    venueImageHTML += "<tr><th class='no-venue'>No Venue Photo Found</th>";
                }
            }
            else{
                venueImageHTML +="<tr><th class='no-venue'>No Venue Photo Found</th>";
            }


            venueImageHTML += "</table>";
            return venueImageHTML;
        }


        /**
        * show venue info
        */
        function showVenueInfo(){
            document.getElementById("venueInfoUp").style.display = "block";
            document.getElementById("venueInfoDown").style.display = "none";
            document.getElementById("venueInfoContent").style.display = "flex";
            hideVenueImage();
        }
        /**
        * hide veneu info
        */
        function hideVenueInfo(){
            document.getElementById("venueInfoUp").style.display = "none";
            document.getElementById("venueInfoDown").style.display = "block";
            document.getElementById("venueInfoContent").style.display = "none";
        }
        /**
        * show venue image
        */
        function showVenueImage(){
            document.getElementById("venueImageUp").style.display = "block";
            document.getElementById("venueImageDown").style.display = "none";
            document.getElementById("venueImageContent").style.display = "flex";
            hideVenueInfo();
        }
        /**
        * hide venue image
        */
        function hideVenueImage(){
            document.getElementById("venueImageUp").style.display = "none";
            document.getElementById("venueImageDown").style.display = "block";
            document.getElementById("venueImageContent").style.display = "none";
        }
        /**
        * show venue map
        */
        function showVenueMap(longitude, latitude, id){
            var long = Number(longitude);
            var lat = Number(latitude);

            var event = document.getElementById(id).offsetParent;
            var map = document.getElementById("map");
            var mapOptions = document.getElementById("mapOptions");
            var eventID = document.getElementById("map-identifier").textContent;

            event.appendChild(map);
            event.appendChild(mapOptions);


            //console.log(long);
            //console.log(lat);
            //console.log(document.getElementById("map").style.display);

            //get location
            var mapLocationLeft = 10;
            var mapLocationTop = Math.round(event.offsetHeight / 3 * 2);

            //show map element
            if (document.getElementById("map").style.display !== "none" && eventID === id) {

                document.getElementById("map").style.display = "none";
                document.getElementById("mapOptions").style.display = "none";
                event.style.position = "static";
            }
            else {

                initMap_myfunc(lat, long);
                event.style.position = "relative";
                document.getElementById("map").style.left = mapLocationLeft+"px";
                document.getElementById("map").style.top = mapLocationTop+"px";
                document.getElementById("mapOptions").style.left = mapLocationLeft+"px";
                document.getElementById("mapOptions").style.top = mapLocationTop+"px";

                document.getElementById('map').style.positon = 'absolute';
                document.getElementById('mapOptions').style.position = 'absolute';
                document.getElementById('map').float = 'none';
                document.getElementById('mapOptions').float = 'none';
                document.getElementById("map").style.display = "block";
                document.getElementById("mapOptions").style.display = "inline";
                document.getElementById("map-identifier").textContent = id;

            }
        }

        function initMap_myfunc(latitude = 34.0266, longitude = -118.2831){
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var directionsService = new google.maps.DirectionsService;

            var uluru = {lat: Number(latitude), lng: Number(longitude)};

            // The map, centered at Uluru
            var map = new google.maps.Map(
                document.getElementById('map'), {zoom: 14, center: uluru});
            // The marker, positioned at Uluru
            var marker = new google.maps.Marker({position: uluru, map: map});

            directionsDisplay.setMap(map);

            //calculateDirection(directionsService, directionsDisplay);

            document.getElementById('mapOptionWalk').addEventListener('click', function() {
                calculateDirection(directionsService, directionsDisplay,"WALKING",latitude,longitude);
            });
            document.getElementById('mapOptionBike').addEventListener('click', function() {
                calculateDirection(directionsService, directionsDisplay,"BICYCLING",latitude,longitude);
            });
            document.getElementById('mapOptionDrive').addEventListener('click', function() {
                calculateDirection(directionsService, directionsDisplay, "DRIVING",latitude,longitude);
            });
            //directionsService = new google.maps.DirectionsService();
        }


        function initMap(){
            var latitude = Number(venue_lat);
            var longitude = Number(venue_long);
            var directionsDisplay = new google.maps.DirectionsRenderer;
            var directionsService = new google.maps.DirectionsService;

            var uluru = {lat: Number(latitude), lng: Number(longitude)};

            // The map, centered at Uluru
            var map = new google.maps.Map(
                document.getElementById('map'), {zoom: 14, center: uluru});
            // The marker, positioned at Uluru
            var marker = new google.maps.Marker({position: uluru, map: map});

            directionsDisplay.setMap(map);

            //calculateDirection(directionsService, directionsDisplay);

            document.getElementById('mapOptionWalk').addEventListener('click', function() {
                calculateDirection(directionsService, directionsDisplay,"WALKING",latitude,longitude);
            });
            document.getElementById('mapOptionBike').addEventListener('click', function() {
                calculateDirection(directionsService, directionsDisplay,"BICYCLING",latitude,longitude);
            });
            document.getElementById('mapOptionDrive').addEventListener('click', function() {
                calculateDirection(directionsService, directionsDisplay, "DRIVING",latitude,longitude);
            });
            //directionsService = new google.maps.DirectionsService();
        }



        function calculateDirection(directionsService, directionsDisplay, travelMode,latitude,longitude){
            //var directionsService = new google.maps.DirectionsService();
            var otherLocationLat = '<?php echo $otherLocationLat; ?>';
            var otherLocationLong = '<?php echo $otherLocationLong; ?>';
            var startLat;
            var startLong;
            if(otherLocationLat!==''&&otherLocationLong!==''){
                startLat = Number(otherLocationLat);
                startLong = Number(otherLocationLong);
            }
            else{
                startLat = lat;
                startLong = long;
            }

            directionsService.route({
                origin: {lat: startLat, lng: startLong},  // Haight.
                destination: {lat: Number(latitude), lng: Number(longitude)},  // Ocean Beach.
                // Note that Javascript allows us to access the constant
                // using square brackets and a string value as its
                // "property."
                travelMode: google.maps.TravelMode[travelMode]
            }, function(response, status) {
                if (status == 'OK') {
                    directionsDisplay.setDirections(response);
                } else {
                    window.alert('Directions request failed due to ' + status);
                }
            });
        }
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?v=3&key=AIzaSyAE_BDGG42oJuV4SVrKs37xj9TSjVhOdpE&callback=initMap">
    </script>
</body>
</html>

