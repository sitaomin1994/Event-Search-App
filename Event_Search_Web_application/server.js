var express = require('express');
var path = require('path');
var cors = require('cors');
var bodyParser = require('body-parser');
var Promise = require('promise');
var url = require("url");
var https= require("https");
var SpotifyWebApi = require('spotify-web-api-node');



var app = express();
var port = process.env.PORT || 8081;

app.use(cors());

app.use(express.static('./public'));

/**
 * api information
 * @type {string}
 */
const ticketMasterAPIKEY = "f5r0Yhc2Dln0m3rlwOpG7TtYKtbSUprn";
const googleMapAPIKEY = "AIzaSyAE_BDGG42oJuV4SVrKs37xj9TSjVhOdpE";
const googleCustomSearchAPIKEY = "AIzaSyBxNhwz3emJO4k-0kEpJShvlx1fygUZfEM";
const googleCustomSearchEngineID = "000902821692401213011:f61yninhwxy";
const songkickAPIKEY = "qEacjnTdzk216ZHZ";
var spotifyApi = new SpotifyWebApi({
    clientId: "e6b5e39f1e654eabaab8e01a956e058d",
    clientSecret: "2985509b1687416ebdad434708ed154d",
    redirectUri: 'http://www.example.com/callback'
});

// Add headers
app.use(function (req, res, next) {

    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', '*');

    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

    // Request headers you wish to allow
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');

    // Set to true if you need the website to include cookies in the requests sent
    // to the API (e.g. in case you use sessions)
    res.setHeader('Access-Control-Allow-Credentials', true);

    // Pass to next layer of middleware
    next();
});

/**
 *  send content header
 */
app.all('*', function(req, res, next)
{
    res.header("Access-Control-Allow-Origin", "*");
    res.header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
    res.header("Access-Control-Allow-Methods","PUT,POST,GET,DELETE,OPTIONS");
    next();
});


/***
 *    data of event details
 */

var detailsData = {
                    info:{},
                    artist:[],
                    venue:{},
                    upcoming:[],
                    eventName:""
                    };


app.get("/",function(req, res){

    res.sendFile("./public/index/html",{root:__dirname});
});
/**
 *  listen to request of event search and send response
 */
app.get('/event_search', async function (req, res) {

    res.setHeader('content-type', 'application/json; charset=utf-8');

    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', "*");

    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

    // Request headers you wish to allow
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');

    // Set to true if you need the website to include cookies in the requests sent
    // to the API (e.g. in case you use sessions)
    res.setHeader('Access-Control-Allow-Credentials', true);



    var params = req.query;

    console.log(params);
    console.log(params.otherlocation);

    /**
     * get geopoint
     * @type {string}
     */
    var geoPoint = "";
    var segmentID = getSegmentID(params.Category);

    if(params.currentLocation !== "" && params.otherlocation === ""){
        geoPoint = getGeoPoint(params.currentLocation.lat, params.currentLocation.long);
    }
    else if(params.otherlocation !== ""){
        var location = JSON.parse(await getOtherLocation(params.otherlocation));
        console.log(location);
        console.log(location["results"]);
        var lat = location.results[0].geometry.location.lat;
        var long = location.results[0].geometry.location.lng;
        geoPoint = getGeoPoint(lat, long);
    }
    else{
        res.send("error no location")
    }

    console.log(geoPoint);

    /**
     *  send request to ticketmaster
     */

    var ticketMasterEventSearchQuery;
    if(segmentID === ""){
        ticketMasterEventSearchQuery =  "https://app.ticketmaster.com/discovery/v2/events.json?" + "&apikey=" + ticketMasterAPIKEY +
            "&keyword=" + encodeURIComponent(params.Keywords) + "&unit="  + encodeURIComponent(params.DistanceUnits)+ "&radius=" +
            encodeURIComponent(params.Distance) + "&geoPoint=" + encodeURIComponent(geoPoint) + "&sort=" + encodeURIComponent("date,asc");
    }
    else {
        ticketMasterEventSearchQuery = "https://app.ticketmaster.com/discovery/v2/events.json?" + "&apikey=" + ticketMasterAPIKEY +
            "&keyword=" + encodeURIComponent(params.Keywords) + "&unit="  + encodeURIComponent(params.DistanceUnits)+ "&radius=" +
            encodeURIComponent(params.Distance) + "&geoPoint=" + encodeURIComponent(geoPoint) + "&segmentId=" + encodeURIComponent(segmentID) + "&sort=" + encodeURIComponent("date,asc");
    }

    /**
     *  send request to ticketmaster and send response back to client
    */

    console.log(ticketMasterEventSearchQuery);

    https.get(ticketMasterEventSearchQuery, (resp) => {

        var data = [];

        // A chunk of data has been recieved.
        resp.on('data', (chunk) => {
            data.push(chunk);
        });

        // The whole response has been received. Print out the result.
        resp.on('end', () => {
            var data_all = data.join('');
            res.send(JSON.parse(data_all));
        });

    }).on("error", (err) => {
        res.send("Error happend ");
    });

    console.log("event search GET");
});


/**
 * listen to request of event details and send response
 */
app.get('/event_details', async function (req, res) {

    res.setHeader('content-type', 'application/json; charset=utf-8');
    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', "*");

    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

    // Request headers you wish to allow
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');

    // Set to true if you need the website to include cookies in the requests sent
    // to the API (e.g. in case you use sessions)
    res.setHeader('Access-Control-Allow-Credentials', true);
    /**
     * get event info
     * @type {null|*}
     */
    var params = req.query;
    var ticketMasterEventDetailQuery = "https://app.ticketmaster.com/discovery/v2/events/" + encodeURIComponent(params.id) + ".json?apikey=" + ticketMasterAPIKEY;
    console.log(ticketMasterEventDetailQuery);
    detailsData.info = JSON.parse(await getEventInfo(ticketMasterEventDetailQuery));
    try{
        detailsData.eventName = detailsData.info.name;
    }
    catch(err){
        detailsData.eventName = "N/A";
    }
    console.log(detailsData.info);
    console.log("event details GET");

    /**
     * get artists info and photos
     * @type {string}
     */
    var eventCategory = "";
    try{
        eventCategory = detailsData.info.classifications[0].segment.name;
    }
    catch(err){
        console.log("no category");
    }

    if("_embedded" in detailsData.info){
        if("attractions" in detailsData.info._embedded){
            if(detailsData.info._embedded.attractions.length !== 0){

                var artistData = new Array();
                /**
                 *  handle each artist
                 */
                for(let i in detailsData.info._embedded.attractions){

                    console.log("has artist");

                    if("name" in detailsData.info._embedded.attractions[i]){

                        console.log("has artist name");

                        var artistName = detailsData.info._embedded.attractions[i].name;
                        var artist = new Object();

                        artist.artistName = artistName;
                        console.log(artist.artistName);

                        if(eventCategory === "Music"){
                            /**
                             *  get sporify attractions info
                             */
                            try{
                                artist.musicArtist = await searchSpotifyArtist(artistName);
                            }
                            catch(err){

                                await setSpotifyCredit();
                                try {
                                    artist.musicArtist = await searchSpotifyArtist(artistName);
                                }
                                catch(err) {
                                    artist.musicArtist = "error";
                                }
                            }

                            console.log(artist.musicArtist);

                        }

                        /**
                         * get google custom info about artist photo
                         */
                        try{
                            var googleCustomQuery = "https://www.googleapis.com/customsearch/v1?q=" + encodeURIComponent(artistName) +"&cx=000902821692401213011%3Af61yninhwxy&imgSize=huge&num=8&searchType=image&key=" + googleCustomSearchAPIKEY;
                            artist.artistPhoto = JSON.parse(await searchArtistPhoto(googleCustomQuery));
                        }
                        catch(err){
                            artist.artistPhoto = "error";
                        }

                        }

                        artistData.push(artist);
                    }

                detailsData.artist = artistData;
            }
        }
    }

    /**
     * venue data
     */
    var venueName;
    try{
        venueName = detailsData.info._embedded.venues[0].name;
    }
    catch(err){
        console.log("no venue info");
    }

    if(venueName !== undefined){

        /**
         * venue data
         */
        var ticketMasterVenueQuery = "https://app.ticketmaster.com/discovery/v2/venues?apikey=" + ticketMasterAPIKEY + "&keyword=" + encodeURIComponent(venueName);
        detailsData.venue = JSON.parse(await searchVenueInfo(ticketMasterVenueQuery));


        /**
         * upcoming event data
         */

        console.log(venueName);

        var songkickVenueQuery = "https://api.songkick.com/api/3.0/search/venues.json?query=" + encodeURIComponent(venueName) + "&apikey=" + songkickAPIKEY;
        var venueResult = JSON.parse(await searchUpcoming(songkickVenueQuery));

        var venueID;

        try{
            venueID = venueResult.resultsPage.results.venue[0].id;
        }
        catch(err){
            console.log("no venue searched by songkick");
        }
        if(venueID !== undefined){
            var songkickUpcomingQuery = "https://api.songkick.com/api/3.0/venues/" +  encodeURIComponent(venueID) +"/calendar.json?apikey=" + songkickAPIKEY;

            detailsData.upcoming = JSON.parse(await searchUpcoming(songkickUpcomingQuery));
        }

    }

    res.send(detailsData);
});

/**
 *  autocomplete
 */

app.get('/auto',async function (req, res) {

    res.setHeader('content-type', 'application/json; charset=utf-8');

    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', "*");

    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

    // Request headers you wish to allow
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');

    // Set to true if you need the website to include cookies in the requests sent
    // to the API (e.g. in case you use sessions)
    res.setHeader('Access-Control-Allow-Credentials', true);
    var params = req.query;

    console.log(params);
    var keywords = params.Keywords;

    /**
     *  send request to ticketmaster
     */

    if(keywords === ""){
        var data = [];
        res.send(data);
    }
    else{
        var autoQuery = 'https://app.ticketmaster.com/discovery/v2/suggest?';
        autoQuery  +=  "&apikey=" + ticketMasterAPIKEY + "&keyword="
            + encodeURIComponent(keywords);
        /**
         *  send request to ticketmaster and send response back to client
         */

        console.log(autoQuery);

        https.get(autoQuery, (resp) => {

            var data = [];

            // A chunk of data has been recieved.
            resp.on('data', (chunk) => {
                data.push(chunk);
            });

            // The whole response has been received. Print out the result.
            resp.on('end', () => {
                var data_all = data.join('');
                var result = JSON.parse(data_all);
                var autoInfo = [];

                try{
                    var auto = result._embedded.attractions;
                }
                catch(err){
                    console.log("error");
                }

                if(auto !== undefined){
                    if(auto.length !== 0) {
                        for (let i in auto) {
                            if('name' in auto[i]){
                                autoInfo.push(auto[i].name);
                            }
                        }
                    }
                }

                console.log(autoInfo);

                res.send(autoInfo);
            });

        }).on("error", (err) => {
            var data = [];
            res.send(data);
        });


    }

    console.log("event auto GET");
});





var server = app.listen(port, function () {

    var host = server.address().address;
    var port = server.address().port;

    console.log("Example app listening at http://%s:%s", host, port)
});

module.exports = server;


/**
 * get segment ID
 * @param category
 * @returns {string}
 */
function getSegmentID(category){
    var segmentID = "";
    if(category === "Music"){
        segmentID = "KZFzniwnSyZfZ7v7nJ";
    }
    else if(category === "Sports"){
        segmentID = "KZFzniwnSyZfZ7v7nE";
    }
    else if(category === "Arts&Theatre"){
        segmentID = "KZFzniwnSyZfZ7v7na";
    }
    else if(category === "Film"){
        segmentID = "KZFzniwnSyZfZ7v7nn";
    }
    else if(category === "Miscellaneous"){
        segmentID = "KZFzniwnSyZfZ7v7n1";
    }
    else{
        segmentID = "";
    }
    return segmentID;
}

/**
 * get GeoPoint
 * @param lat
 * @param long
 */

function getOtherLocation(otherlocation){

    return new Promise((resolve, reject) => {

        var GoogleMapQuery ="https://maps.googleapis.com/maps/api/geocode/json?" + "address=" + encodeURIComponent(otherlocation) + "&key=" + googleMapAPIKEY;
        var request = https.get(GoogleMapQuery, function (response) {
            if (response.statusCode < 200 || response.statusCode > 299) {
                reject('error');
            }

            var body = [];

            response.on('data', function (chunk) {
                return body.push(chunk);
            });

            // resolve promise
            response.on('end', function () {
                return resolve(body.join(''));
            });
        });

        request.on('error', function (err) {
            return reject("error");
        });
    });
}

/**
 * getGeopoint
 * @param latitude
 * @param Longitude
 * @param geohashLength
 * @returns {string}
 */

function getGeoPoint(latitude, Longitude, geohashLength = 5){

    function getBits(coordinate, min, max, bitsLength){
        var binaryString = "";
        var i = 0;
        while (bitsLength > i) {
            var mid = (min+max)/2;
            if (coordinate > mid) {
                binaryString += "1";
                min = mid;
            } else {
                binaryString += "0";
                max = mid;
            }
            i++;
        }
        return binaryString;
    }

    var base32Mapping = "0123456789bcdefghjkmnpqrstuvwxyz";
    // Get latitude and longitude bits length from given geohash Length
    var latBitsLength = 0;
    var lonBitsLength = 0;

    if (geohashLength % 2 === 0) {
        latBitsLength = (geohashLength/2) * 5;
        lonBitsLength = (geohashLength/2) * 5;
    } else {
        latBitsLength = (Math.ceil(geohashLength / 2) * 5) - 3;
        lonBitsLength = latBitsLength + 1;
    }
    // Convert the coordinates into binary format
    var binaryString = "";
    var latbits = getBits(latitude, -90, 90, latBitsLength);
    var lonbits = getBits(Longitude, -180, 180, lonBitsLength);
    var binaryLength = latbits.length + lonbits.length;
    // Combine the lat and lon bits and get the binaryString
    for (let i=1 ; i < binaryLength + 1; i++) {
        if (i%2 === 0) {
            var pos = parseInt((i-2)/2);
            binaryString += latbits[pos];
        } else {
            pos = Math.floor(i/2);
            binaryString += lonbits[pos];
        }
    }
    // Convert the binary to hash
    var hash = "";
    for (let i=0; i< binaryString.length; i+=5) {
        var n = parseInt(binaryString.substr(i,5),2);
        hash = hash + base32Mapping[n];
    }
    return hash;
}

/**
 *  get event details
 */

function getEventInfo(ticketMasterEventDetailQuery){

    return new Promise((resolve, reject) =>{

        var request =  https.get(ticketMasterEventDetailQuery,(response) => {

            if (response.statusCode < 200 || response.statusCode > 299) {
                reject('error');
            }

            var data = [];

            // A chunk of data has been recieved.
            response.on('data', (chunk) => {
                data.push(chunk);
            });

            // The whole response has been received. Print out the result.
            response.on('end', () => {
                var responseData = data.join("");
                console.log(responseData);
                return resolve(responseData);
            });
        });

        request.on("error", (err) => {
            return reject("error");
        });
    });
}

/**
 * searchs spotify artist
 * @param artistName
 */
function searchSpotifyArtist(artistName){

    return spotifyApi.searchArtists(artistName)
        .then(function(data) {
            console.log("a");
            console.log(data.body);
            return(data.body);
        });
}
function setSpotifyCredit(){

   return spotifyApi.clientCredentialsGrant().then(
        function(data) {
            spotifyApi.setAccessToken(data.body['access_token']);
            console.log('The access token expires in ' + data.body['expires_in']);
            console.log('The access token is ' + data.body['access_token']);
            return data.body['access_token'];
        },
        function(err) {
            console.log('Something went wrong when retrieving an access token', err);
        }
    );
}

/**
 * search artist photo
 */
function searchArtistPhoto(searchQuery){

    return new Promise((resolve, reject) =>{

        var request =  https.get(searchQuery,(response) => {

            if (response.statusCode < 200 || response.statusCode > 299) {
                reject('error');
            }

            var data = [];

            // A chunk of data has been recieved.
            response.on('data', (chunk) => {
                data.push(chunk);
            });

            // The whole response has been received. Print out the result.
            response.on('end', () => {
                var responseData = data.join("");
                console.log(responseData);
                return resolve(responseData);
            });
        });

        request.on("error", (err) => {
            return reject("error");
        });
    });
}

/**
 * search venue infp
 */

async function searchVenueInfo(searchQuery){

    await timeout(1000);

    return new Promise((resolve, reject) =>{

        var request =  https.get(searchQuery,(response) => {

            if (response.statusCode < 200 || response.statusCode > 299) {
                reject('error');
            }

            var data = [];

            // A chunk of data has been recieved.
            response.on('data', (chunk) => {
                data.push(chunk);
            });

            // The whole response has been received. Print out the result.
            response.on('end', () => {
                var responseData = data.join("");
                console.log(responseData);
                return resolve(responseData);
            });
        });

        request.on("error", (err) => {
            return reject("error");
        });
    });
}

/**
 * search upcoming
 */
function searchUpcoming(searchQuery){
    return new Promise((resolve, reject) =>{

        var request =  https.get(searchQuery,(response) => {

            if (response.statusCode < 200 || response.statusCode > 299) {
                reject('error');
            }

            var data = [];

            // A chunk of data has been recieved.
            response.on('data', (chunk) => {
                data.push(chunk);
            });

            // The whole response has been received. Print out the result.
            response.on('end', () => {
                var responseData = data.join("");
                console.log(responseData);
                return resolve(responseData);
            });
        });

        request.on("error", (err) => {
            return reject("error");
        });
    });
}
/**
 * set time out
 */
function timeout(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}