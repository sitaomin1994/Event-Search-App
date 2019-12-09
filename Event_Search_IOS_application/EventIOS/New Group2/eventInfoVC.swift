//
//  eventInfoVC.swift
//  hw9
//
//  Created by apple on 11/20/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SwiftSpinner

class eventInfoController:UIViewController{
    
    //MARK: property
    var infoData:JSON?
    @IBOutlet weak var artist: UILabel!
    @IBOutlet weak var venue: UILabel!
    @IBOutlet weak var time: UILabel!
    @IBOutlet weak var category: UILabel!
    @IBOutlet weak var priceRange: UILabel!
    @IBOutlet weak var ticketStatus: UILabel!
    @IBOutlet weak var BuyTicketAt: UITextView!
    @IBOutlet weak var seatmap: UITextView!
    @IBOutlet weak var tabBar: UITabBarItem!
    
    
    override func viewDidLoad() {
        //SwiftSpinner.show("event info....")
        
        super.viewDidLoad()
        if(self.infoData != nil){
            updateInfoData(json: self.infoData!)
        }
        else{
            print("no info record")
        }
        //SwiftSpinner.hide()
    
    }
    
    //MARK: action
    func updateInfoData(json:JSON){
        
        //artist
        var artist:String!
        let artistArray = json["_embedded"]["attractions"].arrayValue
        if artistArray.count != 0{
            artist = artistArray[0]["name"].stringValue
            for i in 1..<artistArray.count {
                artist = artist + " | " + artistArray[i]["name"].stringValue
            }
        }else{
            artist = "N/A"
        }

        //time
        var time:String! = json["dates"]["start"]["localDate"].stringValue + " " + json["dates"]["start"]["localTime"].stringValue
        
        if time == " "{
            time = "N/A"
        }

        //venue
        var venue:String!
        if json["_embedded"]["venues"][0]["name"].stringValue != ""{
            venue = json["_embedded"]["venues"][0]["name"].stringValue
        }else{
            venue = "N/A"
        }
        
        //category
        var category:String!
        let segment = json["classifications"][0]["segment"]["name"].stringValue
        let genre = json["classifications"][0]["genre"]["name"].stringValue
        if segment != "" && genre != ""{
            category = segment + " | " + genre
        }else if segment != "" && genre == ""{
            category = genre
        }else if segment == "" && genre != ""{
            category = segment
        }else{
            category = "N/A"
        }
        
        //ticket status
        var ticketStatus:String!
        if json["dates"]["status"]["code"].stringValue == ""{
            ticketStatus = "N/A"
        }
        else{
            ticketStatus = json["dates"]["status"]["code"].stringValue
        }
        
        //priceRange
        let min = json["priceRanges"][0]["min"].stringValue
        let max = json["priceRanges"][0]["max"].stringValue
        let currency = "$"
        var priceRange:String!
        if min != "" && max != ""{
            priceRange = currency + min + "-" + max
        }else if min != "" && max == ""{
            priceRange = min
        }else if min == "" && max != ""{
            priceRange = max
        }
        else{
            priceRange = "N/A"
        }
        
        //buy ticket at
        let buyTicketUrl = json["url"].stringValue
        if buyTicketUrl != ""{
            let attributedString = NSMutableAttributedString(string: "ticketmaster")
            let url = URL(string:buyTicketUrl)!
            
            // Set the 'click here' substring to be the link
            attributedString.setAttributes([.link: url], range: NSMakeRange(0, 11))
            
            self.BuyTicketAt.attributedText = attributedString
            self.BuyTicketAt.isUserInteractionEnabled = true
            self.BuyTicketAt.isEditable = false
            
            // Set how links should appear: blue and underlined
            self.BuyTicketAt.linkTextAttributes = [
                .foregroundColor: UIColor.blue,
                .underlineStyle: NSUnderlineStyle.single.rawValue
            ]
        }else{
            self.BuyTicketAt.text = "N/A"
        }
        
        //seatmap url
        let seatMapUrl = json["seatmap"]["staticUrl"].stringValue
        if seatMapUrl != ""{
            let attributedString = NSMutableAttributedString(string: "view here")
            let url = URL(string:seatMapUrl)!
            
            // Set the 'click here' substring to be the link
            attributedString.setAttributes([.link: url], range: NSMakeRange(0, 9))
            
            self.seatmap.attributedText = attributedString
            self.seatmap.isUserInteractionEnabled = true
            self.seatmap.isEditable = false
            
            // Set how links should appear: blue and underlined
            self.seatmap.linkTextAttributes = [
                .foregroundColor: UIColor.blue,
                .underlineStyle: NSUnderlineStyle.single.rawValue
            ]
        }else{
            self.seatmap.text = "N/A"
            
        }
        
        
        self.artist.text = artist
        if time != "N/A"{
            let dateformat = DateFormatter()
            dateformat.dateFormat = "MMM d, yyyy HH:mm:ss"
            let dateformat_from = DateFormatter()
            dateformat_from.dateFormat = "yyyy-MM-dd HH:mm:ss"
            let Date = dateformat_from.date(from: time)
            self.time.text = dateformat.string(from:Date!)
        }
        else{
            self.time.text = time
        }
        self.venue.text = venue
        self.category.text = category
        self.ticketStatus.text = ticketStatus
        self.priceRange.text = priceRange
        
    }
}
