<?php


$query = 'https://app.ticketmaster.com/discovery/v2/events?apikey=f5r0Yhc2Dln0m3rlwOpG7TtYKtbSUprn&keyword=University+of+Southern+California&radius=10&unit=miles&geoPoint=9q5cs';
$query_json = file_get_contents($query);
$queryResult = json_decode($query_json,true);
$eventsResult = fetchEvents($queryResult);
echo '<pre>'; print_r($queryResult); echo '</pre>';

echo "<h1> this is extracted information </h1>";
echo '<pre>'; print_r($eventsResult); echo '</pre>';


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
?>
<div id = "eventTable"></div>
<script type="text/javascript">
    // pass PHP variable declared above to JavaScript variable
    var eventResult = JSON.parse('<?php echo json_encode($eventsResult,JSON_HEX_APOS); ?>');
    console.log(eventResult);
    var eventTableHTML = "";
    //show table
    eventTableHTML += '<table border = "2" style = "text-align:auto;">';
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
        eventTableHTML += "<td>" + eventResult[i].segment.name + "</td>";
        //show venue
        eventTableHTML += "<td><a href='event.php'" + " onclick='showVenueMap();'>" + eventResult[i].venues.name + "</a></td>";
        eventTableHTML += "</tr>";
    }
    //end show table
    eventTableHTML += '</table>';

    document.getElementById("eventTable").innerHTML = eventTableHTML;
</script>
