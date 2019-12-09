//
//  eventUpcomingVC.swift
//  hw9
//
//  Created by apple on 11/20/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import SwiftSpinner

class eventUpcomingVC:UIViewController,UITableViewDelegate,UITableViewDataSource{
    
    
    //MARK: property
    var upcomingData:JSON?
    var upcomingEvents:[upcomingEvent]!
    var upcomingEvents_backup:[upcomingEvent]!
    var asc:Bool = true
    let sortType:[String] = ["default","title","date","artist","type"]
    var sortTypeIndex:Int = 0
    @IBOutlet weak var sortTypeSegment: UISegmentedControl!
    @IBOutlet weak var orderSegment: UISegmentedControl!
    @IBOutlet weak var upcomingEventTable: UITableView!
    @IBOutlet weak var tabBar: UITabBarItem!
    
    @IBOutlet weak var noResultContainer: UIView!
    var noResult:Bool = true
    
    //MARK: action
    override func viewDidLoad() {
        SwiftSpinner.show("showing upcoming event...")
        super.viewDidLoad()
        //load table
        self.orderSegment.isEnabled = false
        self.upcomingEventTable.dataSource = self
        self.upcomingEventTable.delegate = self
        self.upcomingEventTable.rowHeight = UITableView.automaticDimension
        self.upcomingEventTable.estimatedRowHeight = 116
        self.upcomingEvents = []
        self.upcomingEvents_backup = []
        
        self.upcomingEventTable.alpha = 0
        self.noResultContainer.alpha = 0
        
        //handle data
        if(upcomingData != nil){
            let result = updateUpcomingData(json: upcomingData!)
            if result == true {
                noResult = false
                self.upcomingEventTable.reloadData()
            }
            else{
                noResult = true
            }
        }
        else{
            noResult = true
        }
        
        if noResult == true{
            self.upcomingEventTable.alpha = 0
            self.noResultContainer.alpha = 1
        }
        else{
            self.upcomingEventTable.alpha = 1
            self.noResultContainer.alpha = 0
        }
        
        
        SwiftSpinner.hide()
        
    }
    
    //update upcoming event data
    func updateUpcomingData(json:JSON) -> Bool{
        
        let upcomingEventArray = json["resultsPage"]["results"]["event"].arrayValue
        print(upcomingEventArray)
        
        if upcomingEventArray.count != 0{
            
            var length = 5
            if(upcomingEventArray.count < 5){
                length = upcomingEventArray.count
            }
            
            for i in 0..<length{
                //title
                var title = upcomingEventArray[i]["displayName"].stringValue;
                if title == ""{
                    title = "N/A"
                }
                //artist
                var artist = upcomingEventArray[i]["performance"][0]["displayName"].stringValue;
                if artist == ""{
                    artist = "N/A"
                }
                
                //date
                let dateformat_compare = DateFormatter()
                dateformat_compare.dateFormat = "yyyy-MM-dd HH:mm:ss"
                
                let date = upcomingEventArray[i]["start"]["date"].stringValue
                
                let time = upcomingEventArray[i]["start"]["time"].stringValue
                var noDate = false
                var noTime = false
                
                var dateValue:Date!
                var dateCompare:Date!
                
                if date == "" && time == "" {
                    dateCompare = dateformat_compare.date(from: "2100-12-31 00:00:00")
                    dateValue = dateformat_compare.date(from:"0000-00-00 00:00:00")
                    noDate = true
                    noTime = true
                }
                else if time == "" && date != ""{
                    //another dateformat
                    let dateformat_noTime = DateFormatter()
                    dateformat_noTime.dateFormat = "yyyy-MM-dd"
                    dateValue = dateformat_noTime.date(from:date)
                    
                    dateCompare = dateformat_compare.date(from:date + " 00:00:00")
                    noTime = true
                }
                else if date == "" && time != ""{
                    dateCompare = dateformat_compare.date(from: "2100-12-31 " + time)
                    
                    //another dateformat
                    let dateformat_noDate = DateFormatter()
                    dateformat_noDate.dateFormat = "HH:mm:ss"
                    dateValue = dateformat_noDate.date(from:time)
                    noDate = true
                }
                else{
                    dateCompare = dateformat_compare.date(from: date + " " + time)
                    dateValue = dateformat_compare.date(from: date + " " + time)
                }
                
                //type
                var type = upcomingEventArray[i]["type"].stringValue
                if type == ""{
                    type = "N/A"
                }
                
                //url
                let url = upcomingEventArray[i]["uri"].stringValue
               
                
                let event:upcomingEvent = upcomingEvent(title: title, artist: artist, dateCompare: dateCompare, dateValue: dateValue, noDate: noDate, noTime:noTime, type: type, url: url)
                
                self.upcomingEvents.append(event)
                
            }
            
            self.upcomingEvents_backup = self.upcomingEvents
            
            return true
        }
        else{
            return false
        }
        
    }
    
    
    /*upcoming table*/
    //table data source
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return self.upcomingEvents.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "upcomingEventCell", for: indexPath) as! upcomingEventCell
        
        //title and artist
        cell.title.text = self.upcomingEvents[indexPath.row].title
        cell.artist.text = self.upcomingEvents[indexPath.row].artist
       
        //time
        if self.upcomingEvents[indexPath.row].noDate == false && self.upcomingEvents[indexPath.row].noTime == false{
            
            let dateformat = DateFormatter()
            dateformat.dateFormat = "MMM d, yyyy HH:mm:ss"
            cell.time.text = dateformat.string(from: self.upcomingEvents[indexPath.row].dateValue)
        }
        else if self.upcomingEvents[indexPath.row].noDate == true && self.upcomingEvents[indexPath.row].noTime == false{
            
            let dateformat = DateFormatter()
            dateformat.dateFormat = "HH:mm:ss"
            cell.time.text = dateformat.string(from: self.upcomingEvents[indexPath.row].dateValue)
            
        }
        else if self.upcomingEvents[indexPath.row].noDate == false && self.upcomingEvents[indexPath.row].noTime == true{
            
            let dateformat = DateFormatter()
            dateformat.dateFormat = "MMM d, yyyy"
            cell.time.text = dateformat.string(from: self.upcomingEvents[indexPath.row].dateValue)
            
        }
        else{
            cell.time.text = "N/A"
        }
        
        //type
        cell.type.text = "Type: " + self.upcomingEvents[indexPath.row].type
        
        return cell
    }
    
    
    // method to run when table view cell is tapped
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if self.upcomingEvents[indexPath.row].url != ""{
            let url = URL(string:self.upcomingEvents[indexPath.row].url)
            UIApplication.shared.open(url!)
        }
    }
    
    
    /*segment*/
    //type segment
    
    @IBAction func typeSegmentTapped(_ sender: Any) {
        let getIndex = sortTypeSegment.selectedSegmentIndex
        print(getIndex)
        self.sortTypeIndex = getIndex
        
        if getIndex == 0{
            self.orderSegment.isEnabled = false
        }else{
            self.orderSegment.isEnabled = true
        }
        
        sortEvents()
        
        self.upcomingEventTable.reloadData()
    }
    
    //order segment
    @IBAction func orderSegmentType(_ sender: Any) {
        let getIndex = orderSegment.selectedSegmentIndex
        if getIndex == 0 {
            self.asc = true
        }
        else{
            self.asc = false
        }
        
        sortEvents()
        
        self.upcomingEventTable.reloadData()
    }
    
    //sort order function
    func sortEvents(){
       let sortType = self.sortType[self.sortTypeIndex]
        let asc = self.asc
        
        
        if sortType == "default"{
            
            self.upcomingEvents = self.upcomingEvents_backup
            
        }else if sortType == "title"{
            
            if asc == true{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.title < $1.title})
            }
            else{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.title > $1.title})
            }
            
        }else if sortType == "artist"{
            
            if asc == true{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.artist < $1.artist})
            }
            else{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.artist > $1.artist})
            }
            
        }else if sortType == "date"{
            
            if asc == true{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.dateCompare < $1.dateCompare})
            }
            else{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.dateCompare > $1.dateCompare})
            }
            
        }else if sortType == "type"{
            
            if asc == true{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.type < $1.type})
            }
            else{
                self.upcomingEvents = self.upcomingEvents.sorted(by: {$0.type > $1.type})
            }
            
        }else{}
    }
    
    
    
}
