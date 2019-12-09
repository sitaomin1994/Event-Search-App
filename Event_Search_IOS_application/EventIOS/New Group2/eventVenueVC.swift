//
//  eventVenueVC.swift
//  hw9
//
//  Created by apple on 11/20/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import GoogleMaps
import CoreLocation
import SwiftSpinner

class eventVenueVC:UIViewController{
    
    @IBOutlet weak var address: UILabel!
    @IBOutlet weak var city: UILabel!
    @IBOutlet weak var phone: UILabel!
    @IBOutlet weak var openHour: UILabel!
    @IBOutlet weak var generalRule: UILabel!
    @IBOutlet weak var childRule: UILabel!

    var venueData:JSON?
    var googleMapApiKey:String = "AIzaSyBIXM--j_EQpy0f0-oQy5ZHsn0dJvzEuuI"
    
    @IBOutlet weak var map: UIView!
    
    @IBOutlet weak var tabBar: UITabBarItem!
    
    //MARK: action
    override func viewDidLoad() {
        super.viewDidLoad()
        
        SwiftSpinner.show("showing venue info...")
        
        //check whether json is come back
        if(self.venueData != nil){
            updateVenueData(json: self.venueData!)
            SwiftSpinner.hide()
        }
        else{
            print("no venue record")
            SwiftSpinner.hide()
        }
       
        
    }

    func updateVenueData(json: JSON){
        
        //address
        var address = json["_embedded"]["venues"][0]["address"]["line1"].stringValue
        if address == ""{
            address = "N/A"
        }
        self.address.text = address
        
        //city
        let city = json["_embedded"]["venues"][0]["city"]["name"].stringValue
        let state = json["embedded"]["venues"][0]["state"]["name"].stringValue
        if city == "" && state == ""{
            self.city.text = "N/A"
        }
        else{
            self.city.text = city + "," + state
        }
        
        //phone number
        var phoneNumber = json["_embedded"]["venues"][0]["boxOfficeInfo"]["phoneNumberDetail"].stringValue
        if phoneNumber == ""{
            phoneNumber = "N/A"
        }
        self.phone.text = phoneNumber
        
        //open hour
        var openHour = json["_embedded"]["venues"][0]["boxOfficeInfo"]["openHoursDetail"].stringValue
        if openHour == ""{
            openHour = "N/A"
        }
        self.openHour.text = openHour
        
        //general rule
        var generalRule = json["_embedded"]["venues"][0]["generalInfo"]["generalRule"].stringValue
        if generalRule == ""{
            generalRule = "N/A"
        }
        self.generalRule.text = generalRule
        
        //child rule
        var childRule = json["_embedded"]["venues"][0]["generalInfo"]["childRule"].stringValue
        if childRule == ""{
            childRule = "N/A"
        }
        self.childRule.text = childRule
        
        //map
        let location = json["_embedded"]["venues"][0]["location"].dictionaryValue
        print(location)
        if location.isEmpty == false{
            //load google map
            let latitude:String = location["latitude"]!.stringValue
            let longitude:String = location["longitude"]!.stringValue
            let camera = GMSCameraPosition.camera(withLatitude:Double(latitude)!, longitude: Double(longitude)!, zoom: 15.0)
            let mapView = GMSMapView.map(withFrame: CGRect(x:0, y: 0, width: 343, height: 200), camera: camera)
            
            // Creates a marker in the center of the map.
            let marker = GMSMarker()
            marker.position = CLLocationCoordinate2D(latitude: Double(latitude)!, longitude: Double(longitude)!)
            marker.map = mapView
            
            self.map.addSubview(mapView)
        }
        
    }
}
