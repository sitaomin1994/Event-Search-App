//
//  tableVC.swift
//  hw9
//
//  Created by apple on 11/20/18.
//  Copyright © 2018 apple. All rights reserved.
//

import Foundation
import UIKit
import Alamofire
import SwiftyJSON
import SwiftSpinner
import EasyToast

//Write the protocol declaration here:
protocol reloadSearchListDelegate {
    func reloadSearchList()
}

class tabVC:UITabBarController{
    
    //MARK: property
    var delgate1 : reloadSearchListDelegate?
    var id:String!
    var dataReceived:Dictionary<String,String>!
    var favoriteBtn:UIBarButtonItem!
    var twitterBtn:UIBarButtonItem!
    var favoriteState:Int = 0
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        SwiftSpinner.show("Search for event....")
        
        twitterBtn = UIBarButtonItem(image: UIImage(named:"twitter"), style: .plain, target: self, action: #selector(touchTWitterBtn))
        
        favoriteBtn = UIBarButtonItem(image: UIImage(named:"favorite-empty"),style: .plain, target: self, action: #selector(touchFavoriteBtn))
        
        if self.id != nil{
            let favoriteState = checkFavoriteState(id:self.id)
            if favoriteState == true{
                self.favoriteState = 1
                favoriteBtn = UIBarButtonItem(image: UIImage(named:"favorite-filled"),style: .plain, target: self, action: #selector(touchFavoriteBtn))
            }
            else{
                self.favoriteState = 0
                favoriteBtn = UIBarButtonItem(image: UIImage(named:"favorite-empty"),style: .plain, target: self, action: #selector(touchFavoriteBtn))
            }
        }
        else{
            self.favoriteState = 0
            favoriteBtn = UIBarButtonItem(image: UIImage(named:"favorite-empty"),style: .plain, target: self, action: #selector(touchFavoriteBtn))
        }
        
        self.navigationItem.rightBarButtonItems = [favoriteBtn,twitterBtn]
        
        /*get event detail data*/
        print(id)
        let url = "http://sitaomin571hw8.us-east-2.elasticbeanstalk.com/event_details"
        let params:Dictionary<String,String> = ["id":self.id]
        
        // updata tab view
        let storyboard = UIStoryboard(name:"Main", bundle:nil)
        let infoVC:eventInfoController = storyboard.instantiateViewController(withIdentifier: "eventInfo") as! eventInfoController
        let artistVC:eventArtistVC = storyboard.instantiateViewController(withIdentifier: "eventArtist") as! eventArtistVC
        let venueVC:eventVenueVC = storyboard.instantiateViewController(withIdentifier: "eventVenue") as! eventVenueVC
        let upcomingVC:eventUpcomingVC = storyboard.instantiateViewController(withIdentifier: "eventUpcoming") as! eventUpcomingVC
        
        // connect to backend and get data
        Alamofire.request(url, method: .get, parameters: params, encoding: URLEncoding.default).responseJSON {
            
            response in
            if response.result.isSuccess {
                
                // handle response
                print("Request: \(String(describing: response.request))")   // original url request
                print("Response: \(String(describing: response.response))") // http url response
                
                print("Success! Got the weather data")
                if response.result.value != nil{
                    let searchResultJSON : JSON = JSON(response.result.value as Any)
                    //print(searchResultJSON)
                    infoVC.infoData = searchResultJSON["info"]
                    artistVC.artistData = searchResultJSON["artist"]
                    venueVC.venueData = searchResultJSON["venue"]
                    upcomingVC.upcomingData = searchResultJSON["upcoming"]
                }
                else{
                    infoVC.infoData = nil
                    artistVC.artistData = nil
                    venueVC.venueData = nil
                    upcomingVC.upcomingData = nil
                    
                }
                
                let viewControllerList = [infoVC, artistVC, venueVC, upcomingVC ]
                self.viewControllers = viewControllerList
                SwiftSpinner.hide()
                
            }
            else {
                print("Error")
                SwiftSpinner.hide()
            }
            
        }

    }
    
    
    
    //check favorite state
    func checkFavoriteState(id:String) -> Bool{
       let eventKey = id
        let state = isKeyPresentInUserDefaults(key: eventKey + "eventID@hw9")
       return state
    }
    
    /*check whether in user defaults*/
    func isKeyPresentInUserDefaults(key: String) -> Bool {
        return UserDefaults.standard.object(forKey: key) != nil
    }
    
    
    // twitter buttion action
    @objc func touchTWitterBtn(){
        print("press share")
        let defaultV = "N/A"
        let name = self.dataReceived["name"]
        let venueName = self.dataReceived["venueName"]
        let url = self.dataReceived["url"]
        
        let twitterURL = "https://twitter.com/intent/tweet?text=Check \(name ?? defaultV) at \(venueName ?? defaultV). Website:\(url ?? defaultV)%23CSCI571EventSearch"
        let twitterURLEncoded = URL(string: twitterURL.addingPercentEncoding(withAllowedCharacters: NSCharacterSet.urlQueryAllowed)!)
        UIApplication.shared.open(twitterURLEncoded!)
    }
    
    //favorite button tapped
    @objc func touchFavoriteBtn(){
        
        if self.id != nil{
            if self.favoriteState == 0 {
                print("set favorite")
                self.favoriteState = 1
                self.favoriteBtn = UIBarButtonItem(image: UIImage(named:"favorite-filled"),style: .plain, target: self, action: #selector(touchFavoriteBtn))
                self.navigationItem.rightBarButtonItems = [favoriteBtn,twitterBtn]
               
                //create a event object
                var name = self.dataReceived["name"]
                if name == nil{name = "N/A"}
                
                var venueName = self.dataReceived["venueName"]
                if venueName == nil{venueName = "N/A"}
                
                var url = self.dataReceived["url"]
                if url == nil{url = "N/A"}
                
                var date = self.dataReceived["date"]
                if date == nil{ date = "N/A"}
                
                var category = self.dataReceived["category"]
                if category == nil{category = "N/A"}
                
                var id = self.dataReceived["id"]
                if id == nil{id = "N/A"}
                
                self.view.showToast(name! + "was added to favorites", position: .bottom, popTime: 100, dismissOnTap: true)
                
                let temp = Event(name:name!,id:id!, url:url!, venueName:venueName! , date: date!, category: category!)
                
                //add event to userdefaults
                let defaults = UserDefaults.standard
                let eventID:String = self.id
                defaults.set(try? PropertyListEncoder().encode(temp), forKey: eventID + "eventID@hw9")
                
            }
            else {
                
                print("set not favorite")
                self.favoriteState = 0
                self.favoriteBtn = UIBarButtonItem(image: UIImage(named:"favorite-empty"),style: .plain, target: self, action: #selector(touchFavoriteBtn))
                self.navigationItem.rightBarButtonItems = [favoriteBtn,twitterBtn]
                
                var name = self.dataReceived["name"]
                if name == nil{name = "N/A"}
                
                self.view.showToast(name! + "was removed from favorites", position: .bottom, popTime: 100, dismissOnTap: true)
                
                //remove event to userdefaults
                let defaults = UserDefaults.standard
                let eventID:String = self.id
                defaults.removeObject(forKey: eventID + "eventID@hw9")
            }
        }
        else{
            
        }
        
        delgate1?.reloadSearchList()
        
    }
    
    
}
