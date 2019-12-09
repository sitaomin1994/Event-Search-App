var myApp = angular.module('myApp', ['ngMaterial','angular-svg-round-progressbar','ngAnimate','ngMessages']);


myApp.controller('myAppController', ['$scope','$http','$httpParamSerializerJQLike', function ($scope, $http, $httpParamSerializerJQLike) {


    /**
     *  reset and clear
     */
    $scope.clear = function(){

        /**
         * input and locatoin
         * @type {boolean}
         */
        $scope.getLocation = true;
        $scope.eventInput = {};
        $scope.eventInput.Keywords = '';
        $scope.eventInput.Category = "All";
        $scope.eventInput.DistanceUnits = "miles";
        $scope.eventInput.Distance = 10;
        $scope.eventInput.currentLocation = {lat: $scope.lat, long: $scope.long};
        $scope.eventInput.otherlocation = "";
        $scope.detailsRequestData = {};


        /**
         * upcoming about filter and sort
         */
        $scope.orderQuery = null;
        $scope.sortType = "Default";
        $scope.order = "Ascending";
        $scope.reverseOrder = false;
        $scope.showCardThreshold = 5;

        /**
         *  autocomplete data
         */
        $scope.autoCompleteResults = [];

        /**
         * event list
         * @type {string}
         */
        $scope.eventsErrorMessage = "";
        $scope.eventsHandler = {noRecord: false, errorHappen: false, events:[]};
        /**
         * event details
         * @type {Array}
         */
        $scope.detailsErrorMessage = "";
        $scope.detailsHandler = {record: {
                noNameRecord:false,
                noInfoRecord:false,
                noArtistRecord:false,
                noVenueRecord:false,
                noUpcomingRecord:false},
            errorHappen:{
                InfoError: false,
                artistError: false,
                venueError:false,
                upcomingError: false},
            requestError: false,
            detailsData:{
                info:{},
                artist:[],
                venue:{},
                upcoming:[],
                eventName:"",
                favorite: false,
                index:-1,
                resultOrFavorite:"result",
                eventIdentification:""
            }
        };
        /**
         * favortie items
         * @type {Array}
         */
        $scope.favoriteItem = [];


        /**
         * form check sign
         * @type {boolean}
         */
        $scope.complete = ($scope.Keywords !== undefined) && ($scope.getLocation === true);    //form complete
        $scope.otherLocationDisabled = true;                                                    //otherlocation disabled
        /**
         * show progressive
         * @type {boolean}
         */
        $scope.showProgressive = false;
        $scope.showEventSearchProgressive = false;
        /**
         * show more show less
         * @type {boolean}
         */
        $scope.showMoreButton = true;
        $scope.showLessButton = false;
        /**
         * error bar
         * @type {boolean}
         */
        $scope.eventsError = false;
        $scope.detailsError = false;
        /**
         * event search div
         * @type {boolean}
         */
        $scope.eventSearchAnimationSign = false;
        $scope.showEventSearch = false;
        $scope.detailsButton1Disabled = true;
        $scope.detailsButton2Disabled = true;

        $scope.showResultsList = true;
        $scope.showFavoriteList = false;

        $scope.showDetailsTable = false;

        /**
         * result and favorte button
         * @type {boolean}
         */
        $scope.resultButtonSign = true;
        $scope.favoriteButtonSign = true;

        /**
         * result and favorite sign
         * @type {string}
         */
        $scope.resultOrFavoriteSign = "result";
        /**
         * search button press sign
         * @type {boolean}
         */
        $scope.haveSearched  = false;
        /**
         * details request sign
         * @type {boolean}
         */
        $scope.haveDetails = false;

        /**
         *  restore local storage information
         */

        $scope.resetLocalstorage();

    };


    /**
     *  reset localstorage sign
     */
    $scope.resetLocalstorage = function(){
        if(localStorage.getItem('favorite') !== null){
            $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
        }
        else{
            $scope.favoriteItem = [];
        }

        for(let i in $scope.favoriteItem){
            var item = $scope.favoriteItem[i];
            item.hasFavoriteDetails = false;
            item.hasResultDetails = false;
        }
        localStorage.setItem('favorite', JSON.stringify($scope.favoriteItem));
    };

    /**
     * sort by keyword function
     */
    $scope.onSelectTypeChange = function(){
        if($scope.sortType === "name" ){
            $scope.orderQuery = "displayName";
            $scope.comparator = undefined;
        }
        else if($scope.sortType === "date"){
            $scope.orderQuery = "date";
           // $scope.comparator = $scope.compareDate();
        }
        else if($scope.sortType === "artist"){
            $scope.orderQuery = "artist";
            $scope.comparator = undefined;
        }
        else if($scope.sortType === "type"){
            $scope.orderQuery = "type";
            $scope.comparator = undefined;
        }
        else if($scope.sortType === "Default"){
            $scope.orderQuery = null;
            $scope.comparator = undefined;
        }
    };

    /**
     *  sort
     */
    $scope.comparator = undefined;


    /**
     * decide sort order
     */
    $scope.onSelectOrderChange = function(){
        if($scope.order === "Ascending"){
            $scope.reverseOrder = false;
        }
        if($scope.order === "Descending"){
            $scope.reverseOrder = true;
        }
    };

    /**
     * about filter and sort
     */
    $scope.orderQuery = null;
    $scope.sortType = "Default";
    $scope.order = "Ascending";
    $scope.reverseOrder = false;
    $scope.showCardThreshold = 5;

    /**
     *  data
     */
    $scope.nofavorite = true;
    if(localStorage.getItem('favorite') !== null){
        $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
    }
    else{
        $scope.favoriteItem = [];
    }

    if($scope.favoriteItem.length === 0){
        $scope.nofavorite = true;
    }
    else{
        $scope.nofavorite = false;
    }
    $scope.resetLocalstorage();

    $scope.autoCompleteResults = [];
    $scope.eventsErrorMessage = "";
    $scope.detailsErrorMessage = "";
    $scope.eventsHandler = {noRecord: false, errorHappen: false, events:[]};
    $scope.detailsHandler = {record: {
                                        noNameRecord:false,
                                        noInfoRecord:false,
                                        noArtistRecord:false,
                                        noVenueRecord:false,
                                        noUpcomingRecord:false},
                             errorHappen:{
                                        InfoError: false,
                                        artistError: false,
                                        venueError:false,
                                        upcomingError: false},
                             requestError: false,
                             detailsData:{
                                        info:{},
                                        artist:[],
                                        venue:{},
                                        upcoming:[],
                                        eventName:"",
                                        favorite: false,
                                        index:-1,
                                        resultOrFavorite:"result",
                                        eventIdentification:""
                                        }
                             };

    /**
     * form data
     * @type {{}}
     */
    $scope.getLocation = false;

    $http.get("http://ip-api.com/json" ).then(function(data) {
            console.log(data);
            $scope.lat = data.data.lat;
            $scope.long = data.data.lon;
            console.log($scope.lat,$scope.long);
            $scope.eventInput.currentLocation = {lat: $scope.lat, long: $scope.long};
            $scope.getLocation = true;
        });

    $scope.eventInput = {};
    $scope.eventInput.Keywords = '';
    $scope.eventInput.Category = "All";
    $scope.eventInput.DistanceUnits = "miles";
    $scope.eventInput.Distance = 10;
    $scope.eventInput.otherlocation = '';
    $scope.detailsRequestData = {};

    /**
     * sign
     * @type {boolean}
     */
    $scope.complete = ($scope.Keywords !== undefined) && ($scope.getLocation === true);
    $scope.otherLocationDisabled = true;
    $scope.showProgressive = false;
    $scope.showEventSearchProgressive = false;
    $scope.showMoreButton = true;
    $scope.showLessButton = false;
    $scope.eventsError = false;
    $scope.detailsError = false;
    $scope.showEventSearch = false;
    $scope.eventSearchAnimationSign = false;
    $scope.showDetailsTable = false;
    $scope.detailsButton1Disabled = true;
    $scope.detailsButton2Disabled = true;
    $scope.resultButtonSign = true;
    $scope.favoriteButtonSign = true;
    $scope.showResultsList = true;
    $scope.showFavoriteList = false;
    $scope.resultOrFavoriteSign = "result";
    $scope.haveSearched  = false;
    $scope.haveDetails = false;


    /**
     * show result list
     */
    $scope.showResult = function(){

        // haven't press search, these two are at initial state
        if($scope.haveSearched === false){
            if($scope.resultOrFavoriteSign !== "result"){
                $scope.resultButtonSign = !$scope.resultButtonSign;
                $scope.favoriteButtonSign = !$scope.favoriteButtonSign;
                $scope.resultOrFavoriteSign = "result";
            }
            $scope.showEventSearch = false;
            $scope.showDetailsTable = false;
            $scope.showResultsList = true;
            $scope.showFavoriteList = false;
            $scope.eventSearchAnimationSign = false;
        }
        else{

            // at event search page, have search
            if($scope.showEventSearch === true && $scope.showDetailsTable === false){
                $scope.showResultsList = true;
                $scope.showFavoriteList = false;
                if($scope.resultOrFavoriteSign !== "result"){
                    $scope.resultButtonSign = !$scope.resultButtonSign;
                    $scope.favoriteButtonSign = !$scope.favoriteButtonSign;
                    $scope.resultOrFavoriteSign = "result";
                }
                $scope.eventSearchAnimationSign = false;
            }

            // at details page
            if($scope.showEventSearch === false && $scope.showDetailsTable === true){
                if($scope.resultOrFavoriteSign !== "result"){
                    $scope.resultButtonSign = !$scope.resultButtonSign;
                    $scope.favoriteButtonSign = !$scope.favoriteButtonSign;
                    $scope.resultOrFavoriteSign = "result";
                    if($scope.eventsHandler.noRecord === false || $scope.eventsHandler.errorHappen === false){
                        $scope.eventSearchAnimationSign = true;
                    }
                    else{
                        $sope.eventSearchAnimationSign = false;
                    }
                    $scope.eventSearchAnimationSign = true;
                    $scope.showEventSearch = true;
                    $scope.showDetailsTable = false;
                    $scope.showResultsList = true;
                    $scope.showFavoriteList = false;
                    console.log("test");
                }
                console.log("test");
            }
        }
    };

    $scope.showFavorite = function() {

        if (localStorage.getItem('favorite') !== null) {
            $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
        }
        else{
            $scope.favoriteItem = [];
        }
        if($scope.favoriteItem.length === 0){
            $scope.nofavorite = true;
        }
        else{
            $scope.nofavorite = false;
        }

        // haven't press search, these two are at initial state ---- show favorite list
        if($scope.haveSearched === false){

            $scope.showEventSearch = true;
            $scope.showDetailsTable = false;
            $scope.showResultsList = false;
            $scope.showFavoriteList = true;
            $scope.eventSearchAnimationSign = false;
            if($scope.resultOrFavoriteSign !== "favorite"){
                $scope.resultButtonSign = !$scope.resultButtonSign;
                $scope.favoriteButtonSign = !$scope.favoriteButtonSign;
                $scope.resultOrFavoriteSign = "favorite";
            }
        }
        else{
            // at result/favorite page
            if($scope.showEventSearch === true && $scope.showDetailsTable === false){
                $scope.showResultsList = false;
                $scope.showFavoriteList = true;
                $scope.eventSearchAnimationSign = false;
                if($scope.resultOrFavoriteSign !== "favorite"){
                    $scope.resultButtonSign = !$scope.resultButtonSign;
                    $scope.favoriteButtonSign = !$scope.favoriteButtonSign;
                    $scope.resultOrFavoriteSign = "favorite";
                }
            }

            // at details table page
            if($scope.showEventSearch === false && $scope.showDetailsTable === true){

                if($scope.resultOrFavoriteSign !== "favorite"){
                    $scope.resultButtonSign = !$scope.resultButtonSign;
                    $scope.favoriteButtonSign = !$scope.favoriteButtonSign;
                    $scope.resultOrFavoriteSign = "favorite";

                    if($scope.nofavorite === false){
                        $scope.eventSearchAnimationSign = true;
                    }
                    else{
                        $scope.eventSearchAnimationSign = false;
                    }
                    $scope.showEventSearch = true;
                    $scope.showDetailsTable = false;
                    $scope.showResultsList = false;
                    $scope.showFavoriteList = true;
                }
            }
        }

        console.log($scope.favoriteItem);
    };

    /**
     * details and list button
     */
    $scope.clickList = function(){
        $scope.eventSearchAnimationSign = true;
        $scope.showEventSearch = true;
        $scope.showDetailsTable = false;

    };
    $scope.clickDetails = function(){
        $scope.showEventSearch = false;
        $scope.showDetailsTable = true;
    };

    /**
     *  favorite and delete
     */
    $scope.clickStar = function(eventIndex){

        if(localStorage.getItem('favorite') !== null){
            $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
        }
        else{
            $scope.favoriteItem = [];
        }
        //update the favorite array
        if($scope.eventsHandler.events[eventIndex].favorite === true){

            for(let i in $scope.favoriteItem){
                if($scope.eventsHandler.events[eventIndex].eventIdentification === $scope.favoriteItem[i].eventIdentification){
                    var index = i;
                }
            }

            if(index !== undefined){
                $scope.favoriteItem.splice(index,1);
            }

            localStorage.setItem('favorite', JSON.stringify($scope.favoriteItem));
            $scope.eventsHandler.events[eventIndex].favorite = false;
        }
        else{

            var eventItem = $scope.eventsHandler.events[eventIndex];
            $scope.favoriteItem.push(eventItem);
            $scope.eventsHandler.events[eventIndex].favorite = true;
            localStorage.setItem('favorite', JSON.stringify($scope.favoriteItem));
        }

        //handle details star
        if($scope.haveDetails === true){
            $scope.detailsHandler.detailsData.favorite = $scope.eventsHandler.events[eventIndex].favorite;
        }
    };

    $scope.clickDelete = function(favoriteIndex){

        $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
        console.log($scope.favoriteItem);
        var removedItem = $scope.favoriteItem.splice(favoriteIndex,1);
        //console.log(removedItem);
        for(let i in $scope.eventsHandler.events){
            //console.log(removedItem[0].eventIdentification);
            //console.log($scope.eventsHandler.events[i].eventIdentification);
            if(removedItem[0].eventIdentification === $scope.eventsHandler.events[i].eventIdentification) {
                $scope.eventsHandler.events[i].favorite = false;
                //handle details star
                if($scope.haveDetails === true){
                    $scope.detailsHandler.detailsData.favorite = false;
                }
                console.log($scope.eventsHandler.events[i].favorite);
            }
        }
        console.log($scope.eventsHandler.events);

        if($scope.favoriteItem.length === 0 ){
            $scope.nofavorite = true;
        }
        else{
            $scooe.nofavorite = false;
        }
        localStorage.setItem('favorite', JSON.stringify($scope.favoriteItem));

    };


    $scope.clickDetailsStar = function(){
        // load local storage
        if(localStorage.getItem('favorite') !== null){
            $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
        }
        else{
            $scope.favoriteItem = [];
        }
        var data = $scope.detailsHandler.detailsData;

        //if yellow star delete the item of local storage and change favorite sign
        if(data.favorite === true){
            //delete
            for(let i in $scope.favoriteItem){
                if(data.eventIdentification === $scope.favoriteItem[i].eventIdentification){
                    var index = i;
                }
            }
            if(index !== undefined){
                $scope.favoriteItem.splice(index,1);
            }

            // change favortie sign of list and details
            for(let i in $scope.eventsHandler.events){
                if(data.eventIdentification === $scope.eventsHandler.events[i].eventIdentification){
                    $scope.eventsHandler.events[i].favorite = false;
                }
            }

            data.favorite = false;
            if($scope.favoriteItem.length === 0){
                $scope.nofavorite = true;
            }
            else{
                $scope.nofavorite = false;
            }

        }
        //if no yellow star add element to local storage and change favorite sign
        else{

            for(let i in $scope.eventsHandler.events){
                if(data.eventIdentification === $scope.eventsHandler.events[i].eventIdentification){
                    var eventItem = $scope.eventsHandler.events[i];
                    $scope.favoriteItem.push(eventItem);
                    $scope.eventsHandler.events[i].favorite = true;
                }
            }

            data.favorite = true;

        }
        // update local storage
        localStorage.setItem('favorite', JSON.stringify($scope.favoriteItem));

    };


    /**
     *  current location and other location
     */
    $scope.checkCurrentLocation = function(){
        $scope.eventInput.otherlocation = "";
        $scope.currentLocation = {lat: $scope.lat, long: $scope.long};
        $scope.otherLocationDisabled = true;

    };

    $scope.checkOtherLocation = function(){
        $scope.currentLocation = "";
        $scope.otherLocationDisabled = false;


    };
    /**
     * show and close seatmap
     */
    $scope.showSeatmap = function(){
        console.log('show');
        var modal = document.getElementById('myModal');
        modal.style.display = "block";
    };

    $scope.closeSeatmap = function(){
        var modal = document.getElementById('myModal');
        modal.style.display = "none";
    };

    /**
     * show google map
     */
    $scope.showMap = function(venueData){
        // The location of Uluru
        var latitude = -25.344;
        var longitude = 131.036;
        if(venueData.hasOwnProperty("location")){
            latitude = Number(venueData.location.latitude);
            longitude = Number(venueData.location.longitude);
        }
        var uluru = {lat: latitude, lng: longitude};
        // The map, centered at Uluru
        var map = new google.maps.Map(
            document.getElementById('map'), {zoom: 15, center: uluru});
        // The marker, positioned at Uluru
        var marker = new google.maps.Marker({position: uluru, map: map});

    };

    /**
     *  show more and show less
     */
    $scope.showMore =  function(){
        $scope.showLessButton = !$scope.showLessButton;
        $scope.showMoreButton = !$scope.showMoreButton;
        $scope.showCardThreshold = $scope.detailsHandler.detailsData.upcoming.length;

    };

    $scope.showLess = function(){
        $scope.showLessButton = !$scope.showLessButton;
        $scope.showMoreButton = !$scope.showMoreButton;
        $scope.showCardThreshold = 5;
    };

    /**
     *  autocomplete query
     */
    $scope.query = function(searchText) {
        console.log(searchText);
        return $http
               .get('http://sitaomin571hw8.us-east-2.elasticbeanstalk.com/auto?Keywords=' + encodeURIComponent(searchText))
               .then(function(data) {
                   return data.data;
                });
        };


    /**
     *  handle event search request
     */
    $scope.handleEventsTable = async function(){
        //show progressive bar
        $scope.showEventSearchProgressive = true;
        $scope.showEventSearch = false;
        $scope.resetLocalstorage();
        await setTimeout(5000);

        //if it in favorite tab auto change to result tab
        if($scope.resultOrFavoriteSign === "favorite"){
            $scope.resultButtonSign = !$scope.resultButtonSign;
            $scope.favoriteButtonSign = !$scope.favoriteButtonSign;
            $scope.resultOrFavoriteSign = "result";
            $scope.showResultsList = true;
            $scope.showFavoriteList = false;
        }

        if($scope.resultOrFavoriteSign === "result"){
            $scope.showResultsList = true;
            $scope.showFavoriteList = false;
        }

        //show form data
        console.log($scope.eventInput);
        console.log($httpParamSerializerJQLike($scope.eventInput));


        /**
         *  add params as string
         */

        //send ajax request to backend
        $http.get('http://sitaomin571hw8.us-east-2.elasticbeanstalk.com/event_search?' + $httpParamSerializerJQLike($scope.eventInput)).then(function(response) {
                console.log(response);
                $scope.eventsHandler.errorHappen = false;
                $scope.eventsHandler.events = new Array();
                //parse response data
                if(response.hasOwnProperty("data")){
                    if(response.data.hasOwnProperty("_embedded")){
                        if(response.data._embedded.hasOwnProperty("events")){
                            if(response.data._embedded.events.length === 0){
                                $scope.eventsHandler.noRecord = true;
                            }
                            else{
                                $scope.eventsHandler.noRecord = false;

                                var eventData = response.data._embedded.events;

                                for(let i in eventData){
                                    var event = new Object();
                                    event.eventID = Number(i)+1;
                                    // find event ID
                                    event.eventIdentification = eventData[i].id;
                                    event.hasResultDetails = false;
                                    event.hasFavoriteDetails = false;


                                    // find event favorite
                                    if (localStorage.getItem('favorite') !== null) {
                                        $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
                                    }
                                    else{
                                        $scope.favoriteItem = [];
                                    }

                                    event.favorite = false;
                                    for(let i in $scope.favoriteItem){
                                        //console.log($scope.favoriteItem[i].eventIdentification);
                                        //console.log(event.eventIdentification);
                                        //console.log(typeof (event.eventIdentification));
                                        //console.log(typeof($scope.favoriteItem[i].eventIdentification));

                                        if($scope.favoriteItem[i].eventIdentification.localeCompare(event.eventIdentification) === 0){
                                            //console.log("a");
                                            event.favorite = true;
                                        }
                                    }
                                    console.log(event.favorite);

                                    // find date
                                    if(eventData[i].hasOwnProperty("dates")){
                                        if(eventData[i].dates.hasOwnProperty('start')){
                                            if(eventData[i].dates.start.hasOwnProperty('localDate')){
                                                event.date = eventData[i].dates.start.localDate;
                                            }
                                            else{
                                                event.date = "N/A";
                                            }
                                        }
                                        else{
                                            event.date = "N/A"
                                        }
                                    }
                                    else{
                                        event.date = "N/A"
                                    }
                                    // find genre and segment:
                                    if(eventData[i].hasOwnProperty("name")){
                                        event.name = eventData[i].name;
                                        event.nameTootip = event.name;
                                        console.log(event.name);
                                        console.log(event.name.length);
                                        if(event.name.length >= 35){
                                            event.tooltip = true;
                                            event.name = event.name.substr(0,35);
                                            console.log(event.name);
                                            console.log(event.nameTootip);

                                            if(event.name[34] !== " "){
                                                event.name = event.name.substring(0, event.name.lastIndexOf(' '));
                                            }
                                            event.name = event.name + "...";

                                        }
                                        else{
                                            event.tooltip = false;
                                        }
                                    }
                                    else{
                                        event.nameTooltip = "N/A";
                                        event.name = "";
                                        event.tooltip = false;
                                    }
                                    console.log(event.tooltip);

                                    //find category
                                    try{
                                        event.segment = eventData[i].classifications[0].segment.name;
                                    }
                                    catch(err){
                                        event.segment = "N/A";
                                    }
                                    try{
                                        event.genre = eventData[i].classifications[0].genre.name;
                                    }
                                    catch(err){
                                        event.genre = "N/A"
                                    }
                                    // find venue info
                                    try{
                                        event.venueInfo = eventData[i]._embedded.venues[0].name;
                                    }
                                    catch{
                                        event.venueInfo = "N/A";
                                    }

                                    $scope.eventsHandler.events.push(event);
                                }
                            }
                        }
                        else{
                            $scope.eventsHandler.noRecord = true;
                        }
                    }
                    else{
                        $scope.eventsHandler.noRecord = true;
                    }
                }
                else{
                    $scope.eventsHandler.noRecord = true;
                }

                console.log($scope.eventsHandler.events);
                // hide progress bar
                $scope.showEventSearchProgressive = false;
                $scope.haveSearched = true;
                $scope.eventSearchAnimationSign = false;
                $scope.showEventSearch = true;

            },
                function(response){
                // handling error
                    console.log(response);
                    $scope.eventsHandler.events = new Array();
                    $scope.eventsHandler.errorHappen = true;
                    $scope.eventsError = response.status;
                    $scope.eventsErrorMessage = response.statusText;
                    $scope.showEventSearchProgressive = false;
                });
    };


    /**
     *  handle event details request
     * @param eventID
     */

    $scope.handleEventsDetails = function(eventID, index){

        /*
        initialization
         */
        $scope.haveDetails = true;
        $scope.detailsHandler = {record: {
                noNameRecord:false,
                noInfoRecord:false,
                noArtistRecord:false,
                noVenueRecord:false,
                noUpcomingRecord:false},
            errorHappen:{
                InfoError: false,
                artistError: false,
                venueError:false,
                upcomingError: false},
            requestError: false,
            detailsData:{
                info:{},
                artist:[],
                venue:{},
                upcoming:[],
                eventName:"",
                favorite:false,
                index:-1,
                resultOrFavorite:"result",
                eventIdentification:""
            }
        };

        if(localStorage.getItem('favorite') !== null){
            $scope.favoriteItem = JSON.parse(localStorage.getItem('favorite'));
        }
        else{
            $scope.favoriteItem = [];
        }

        for(let i in $scope.favoriteItem) {
            $scope.favoriteItem[i].hasDetails = false;
        }

        for(let i in $scope.eventsHandler.events){
            $scope.eventsHandler.events[i].hasDetails = false;
        }

        //show progressive bar

        $scope.showProgressive = true;
        $scope.showEventSearch = false;
        $scope.showDetailsTable = true;

        //background orange color and favorite sign check
        $scope.detailsHandler.detailsData.eventIdentification = eventID;

        if($scope.resultOrFavoriteSign === "result"){

            $scope.detailsButton1Disabled = false;
            $scope.eventsHandler.events[index].hasResultDetails = true;
            $scope.eventsHandler.events[index].hasFavoriteDetails = false;
            $scope.resetLocalstorage();
            $scope.detailsHandler.detailsData.eventName = $scope.eventsHandler.events[index].nameTootip;
            console.log($scope.eventsHandler.events);
            $scope.detailsHandler.detailsData.favorite = $scope.eventsHandler.events[index].favorite;
            $scope.detailsHandler.detailsData.index = index;
            $scope.detailsHandler.detailsData.resultOrFavorite = "result";
        }

        if($scope.resultOrFavoriteSign === "favorite"){

            $scope.detailsButton2Disabled = false;
            $scope.favoriteItem[index].hasFavoriteDetails = true;
            $scope.favoriteItem[index].hasResultDetails = false;
            for(let i in $scope.eventsHandler.events){
                $scope.eventsHandler.events[i].hasFavoriteDetails = false;
                $scope.eventsHandler.events[i].hasResultDetails = false;
            }
            $scope.detailsHandler.detailsData.eventName = $scope.favoriteItem[index].nameTootip;
            $scope.detailsHandler.detailsData.favorite = $scope.favoriteItem[index].favorite;
            $scope.detailsHandler.detailsData.index = index;
            $scope.detailsHandler.detailsData.resultOrFavorite = "favorite";
            localStorage.setItem('favorite', JSON.stringify($scope.favoriteItem));
        }


        $scope.detailsRequestData = new Object();
        $scope.detailsRequestData.id = eventID;

        $http.get('http://sitaomin571hw8.us-east-2.elasticbeanstalk.com/event_details?'+ $httpParamSerializerJQLike($scope.detailsRequestData)).then(function(response) {

                console.log(response);
                $scope.detailsHandler.requestError = false;
                //parse data

                if(response.hasOwnProperty("data")) {

                    /**
                     *  handle info data
                     */
                    if(response.data.hasOwnProperty("eventName")){
                        $scope.detailsHandler.record.noNameRecord = false;
                    }
                    else{
                        $scope.detailsHandler.record.noNameRecord = true;
                    }

                    if(response.data.hasOwnProperty("info")){
                        $scope.detailsHandler.detailsData.info = handleInfoData(response.data.info);
                        $scope.detailsHandler.record.noInfoRecord = false;
                        console.log($scope.detailsHandler.detailsData.info);
                    }
                    else{
                        $scope.detailsHandler.record.noInfoRecord = true;
                    }

                    //handle music artist data
                    if(response.data.hasOwnProperty("artist")){
                        if(response.data.artist.length!== 0){
                            $scope.detailsHandler.detailsData.artist = handleArtistData(response.data.artist);
                            $scope.detailsHandler.record.noArtistRecord = false;
                            console.log($scope.detailsHandler.detailsData.artist);
                        }
                        else{
                            $scope.detailsHandler.record.noArtistRecord = true;
                        }
                    }
                    else {
                        $scope.detailsHandler.record.noArtistRecord = true;
                    }

                    /**
                     *  handle venue data
                     */
                    if(response.data.hasOwnProperty("venue")){
                        $scope.detailsHandler.detailsData.venue = handleVenueData(response.data.venue);
                        if(Object.keys($scope.detailsHandler.detailsData.venue).length === 0 ){
                            $scope.detailsHandler.record.noVenueRecord = true;
                        }
                        else{
                            $scope.detailsHandler.record.noVenueRecord = false;
                        }
                        console.log($scope.detailsHandler.detailsData.venue);
                    }
                    else{
                        $scope.detailsHandler.record.noVenueRecord = true;
                    }


                    /**
                     *  handle event upcoming data
                     */
                    if(response.data.hasOwnProperty("upcoming")){
                        if(response.data.upcoming.hasOwnProperty("resultsPage")){
                            if(response.data.upcoming.resultsPage.hasOwnProperty("results")){
                                if(response.data.upcoming.resultsPage.results.hasOwnProperty("event")){
                                    if(response.data.upcoming.resultsPage.results.event.length !== 0){

                                        $scope.detailsHandler.detailsData.upcoming = handleUpcomingData(response.data.upcoming.resultsPage.results.event);
                                        console.log($scope.detailsHandler.detailsData.upcoming);

                                        if($scope.detailsHandler.detailsData.upcoming.length === 0){
                                            $scope.detailsHandler.record.noUpcomingRecord = true;
                                        }
                                        else{
                                            $scope.detailsHandler.record.noUpcomingRecord = false;
                                        }
                                    }
                                    else{
                                        $scope.detailsHandler.record.noUpcomingRecord = true;
                                    }
                                }
                                else{
                                    $scope.detailsHandler.record.noUpcomingRecord = true;
                                }
                            }
                            else{
                                $scope.detailsHandler.record.noUpcomingRecord = true;
                            }
                        }
                        else{
                            $scope.detailsHandler.record.noUpcomingRecord = true;
                        }
                    }
                    else{
                        $scope.detailsHandler.record.noUpcomingRecord = true;
                    }


                }
                else{
                    $scope.detailsHandler.record.noInfoRecord = true;
                    $scope.detailsHandler.record.noArtistRecord = true;
                    $scope.detailsHandler.record.noVenueRecord= true;
                    $scope.detailsHandler.record.noUpcomingRecord = true;
                }

                console.log($scope.detailsHandler.detailsData);
                $scope.showProgressive = false;
                $scope.showEventSearch = false;

            }, function(response){
                    //handling error
                    console.log(response);
                    $scope.detailsHandler.errorHappen.InfoError = true;
                    $scope.detailsHandler.errorHappen.artistError = true;
                    $scope.detailsHandler.errorHappen.venueError= true;
                    $scope.detailsHandler.errorHappen.upcomingError = true;
                    $scope.showEventSearch = false;
                    $scope.showProgressive = false;
                });
    }
}]);


/**
 * handle event info data
 * @param data
 * @returns {Object}
 */
function handleInfoData(data){

    console.log("event info data:")
    console.log(data);
    var eventInfo = new Object();

    /**
     * add artists
     */

    if(data.hasOwnProperty("_embedded")){
        if(data._embedded.hasOwnProperty("attractions")){
            if(data._embedded.attractions.length !== 0){
                var artists = new Array();
                for(let i in data._embedded.attractions){
                    if(data._embedded.attractions[i].hasOwnProperty("name")) {
                        artists.push(data._embedded.attractions[i].name)
                    }
                }
                if(artists.length !== 0){
                    eventInfo.artists = artists;
                }
            }
        }
    }

    /**
     * add venue
     */
    if(data.hasOwnProperty("_embedded")){
        if(data._embedded.hasOwnProperty("venues")){
            if(data._embedded.venues.length !==0){
                if(data._embedded.venues[0].hasOwnProperty("name")){
                    eventInfo.venue = data._embedded.venues[0].name;
                }
            }
        }
    }

    /**
     * add date
     */
    var dates = new Object();
    try{
        dates.date = data.dates.start.localDate;
    }
    catch(err){
        dates.date = null;
    }

    try{
        dates.time = data.dates.start.localTime;
    }
    catch(err){
        dates.time = null;
    }

    if(dates.date !== null && dates.time !== null){
        eventInfo.date = new Date(dates.date + " " + dates.time);
    }
    else if(dates.date !== null && dates.time === null){
        eventInfo.date = new Date(dates.date);
    }
    else if(dates.date === null && dates.time !== null){
        eventInfo.date = new Date("1999-01-01" + dates.time);
    }
    else{

    }

    /**
     * add categories
     * @type {any[]}
     */
    var categories = new Array();
    try{
        categories.push(data.classifications[0].genre.name);
    }
    catch(err){
        console.log("no genre");
    }
    try{
        categories.push(data.classifications[0].segment.name);
    }
    catch(err){
        console.log("no segment");
    }
    if(categories.length !==0){
        eventInfo.categories = categories;
    }

    /**
     * add price range
     */
    var priceRange = new Object();
    try{
        priceRange.min = data.priceRanges[0].min;
    }
    catch(err){
        console.log("no price range min");
    }
    try{
        priceRange.max = data.priceRanges[0].max;
    }
    catch(err){
        console.log("no price range max");
    }
    if(Object.keys(priceRange).length !== 0 ){
        eventInfo.priceRange = priceRange;
    }

    try{
        eventInfo.currecy = data.priceRanges[0].currency;
        if(eventInfo.currency === null){
            eventInfo.currency = "USD";
        }
    }
    catch(err){
        eventInfo.currency = "USD";
    }

    /**
     *  add ticket status
     */
    try{
        eventInfo.status = data.dates.status.code;
    }
    catch(err){
        console.log("no status");
    }

    /**
     *  add url
     */
    try{
        eventInfo.url = data.url;
    }
    catch(err){
        console.log("no url");
    }

    /**
     * add seatmap
     */
    try{
        eventInfo.seatmap = data.seatmap.staticUrl;
    }
    catch(err){
        console.log("no seatmap url");
    }

    return eventInfo;

}

/**
 * handle event artist data
 * @param data
 * @returns {any[]}
 */
function handleArtistData(data){
    console.log("artist data:")
    console.log(data);


    var artistResult = new Array();

    for(let item in data){


        var artist = new Object();

        artist.artistName = data[item].artistName;

        /**
         *  add spotify info
         */
        if(data[item].hasOwnProperty("musicArtist")){

            var musicArtistData;
            try{
                musicArtistData = data[item].musicArtist.artists.items
            }
            catch(err){

                console.log("no music artists")
            }

            for(let i in musicArtistData){

                if(musicArtistData[i].name === data[item].artistName){

                    artist.musicArtist = new Object();

                    /**
                     * add artist name
                     */
                    try {
                        artist.musicArtist.name = musicArtistData[i].name;
                    }
                    catch(err){
                        console.log("no name");
                    }
                    /**
                     * add artist followers
                     */
                    try {
                        artist.musicArtist.followers = musicArtistData[i].followers.total;
                    }
                    catch(err){
                        console.log("no followers");
                    }

                    /**
                     * add artist popularity
                     */
                    try {
                        artist.musicArtist.popularity = musicArtistData[i].popularity;
                    }
                    catch(err){
                        console.log("no popilarity");
                    }

                    /**
                     * add artist chect at
                     */
                    try {
                        artist.musicArtist.url = musicArtistData[i].external_urls.spotify;
                    }
                    catch(err){
                        console.log("no url");
                    }

                    break;
                }
            }

        }

        /**
         * add google customer info
         */

        if(data[item].hasOwnProperty('artistPhoto')){

            //console.log("test");

            if(data[item].artistPhoto.hasOwnProperty("items")){
                //console.log("test");

                if(data[item].artistPhoto.items.length !== 0){
                   // console.log("test");

                    artist.artistPhoto = new Array();

                    for(let ele in data[item].artistPhoto.items){

                        try {
                            artist.artistPhoto.push(data[item].artistPhoto.items[ele].link);
                        }
                        catch(err){
                            console.log("no image url of " + ele);
                        }
                    }
                }
            }
        }

        /**
         * push aritst info to result
         */

        if(Object.keys(artist).length !== 0){
            artistResult.push(artist);
        }
    }


    return artistResult;
}

/**
 * handle event venue data
 * @param data
 * @returns {Object}
 */
function handleVenueData(data){
    console.log("venue data:")
    console.log(data);

    eventVenue = new Object;

    /**
     * add name
     */
    try{
        eventVenue.name = data._embedded.venues[0].name;
    }
    catch(err){
        console.log("no name");
    }
    /**
     * add address
     */
    try{
        eventVenue.address = data._embedded.venues[0].address.line1;
    }
    catch(err){
        console.log("no address");
    }
    /**
     * add city
     */
    try{
        eventVenue.city = data._embedded.venues[0].city.name;
    }
    catch(err){
        console.log("no city");
    }
    try{
        eventVenue.state = data._embedded.venues[0].state.name;
    }
    catch(err){
        console.log("no state");
    }
    /**
     * add phone number
     */
    try{
      eventVenue.phoneNumber = data._embedded.venues[0].boxOfficeInfo.phoneNumberDetail;
    }
    catch(err){
        console.log("no phone number");
    }
    /**
     * add open hour
     */
    try{
      eventVenue.openHour = data._embedded.venues[0].boxOfficeInfo.openHoursDetail;
    }
    catch(err){
        console.log("no open hour");
    }
    /**
     * add general rule
     */
    try{
        eventVenue.generalRule = data._embedded.venues[0].generalInfo.generalRule;
    }
    catch(err){
        console.log("no general rule");
    }
    /**
     *  add child rule
     */
    try{
        eventVenue.childRule = data._embedded.venues[0].generalInfo.childRule;
    }
    catch(err){
        console.log("no child rule");
    }
    /**
     * add location
     */
    try{
        eventVenue.location = data._embedded.venues[0].location;
    }
    catch(err){
        console.log("no location");
    }

    return eventVenue;

}

/**
 * handle event upcoming data
 * @param data
 */
function handleUpcomingData(data){

    console.log("upcoming");
    console.log(data);

    var upcoming = new Array();

    for(let i in data){

        var event = new Object();

        /**
         *  add display name
         */
        try{
            event.displayName = data[i].displayName;
        }
        catch(err){
            console.log("no name");

        }
        /**
         *  add url
         */
        try{
            event.url = data[i].uri;
        }
        catch(err){
            console.log("no url");

        }
        /**
         * add artist
         */
        try{
            event.artist = data[i].performance[0].displayName;
        }
        catch(err){
            console.log("no artist");
        }
        /**
         * add time
         */
        event.date = new Object();
        try{
            event.date.date = data[i].start.date;
        }
        catch(err){
            console.log("no date");
            event.date.date = null;
        }
        try{
            event.date.time = data[i].start.time;
        }
        catch(err){
            event.date.time = null;
            console.log("no time");
        }

        if(event.date.date !== null && event.date.time !== null){
            event.date = new Date(event.date.date + " " + event.date.time);
        }
        else if(event.date.date === null && event.date.time !== null){
            event.date = new Date("1900-01-01" + " " + event.date.time);
        }
        else if(event.date.time === null && event.date.date !== null){
            event.date = new Date(event.date.date);
        }
        else{
            event.date = new Date("2100-12-31");

        }
        /**
         * add type
         */
        try{
            event.type = data[i].type;
        }
        catch(err){
            console.log("no type");

        }

        if(Object.keys(event).length !== 0){
            upcoming.push(event);
        }

    }

    return upcoming;

}

function timeout(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}