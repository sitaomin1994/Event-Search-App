//
//  eventSearchListVC.swift
//  hw9
//
//  Created by apple on 11/19/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import Alamofire
import SwiftSpinner
import McPicker
import SwiftyJSON
import Kingfisher
import EasyToast


class eventSearchListVC: UIViewController, UITableViewDelegate,UITableViewDataSource, reloadSearchListDelegate {
    
    
    //MARK: property
    var params: Dictionary<String,AnyObject> = [:]
    var url = "http://sitaomin571hw8.us-east-2.elasticbeanstalk.com/event_search"
    var searchListData:[Event] = []
    var favoriteButtonState:[Int] = []
    var eventID:String! = ""
    var dataSent:Dictionary<String,String> = [:]
    @IBOutlet weak var searchListTable: UITableView!
    @IBOutlet weak var noResultContainer: UIView!
    var noResult:Bool = true
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
        SwiftSpinner.show("Searching for events...")
        print(params)
        
        // About table view
        searchListTable.delegate = self
        searchListTable.dataSource = self
        searchListTable.rowHeight = UITableView.automaticDimension
        searchListTable.estimatedRowHeight = 200.0
        searchListTable.alpha = 0
        noResultContainer.alpha = 0
        
        Alamofire.request(url, method: .get, parameters: params, encoding: URLEncoding.default).responseJSON {
            
            response in
            if response.result.isSuccess {
                
                print("Success! Got the events data")
                if response.result.value != nil{
                    let searchResultJSON : JSON = JSON(response.result.value as Any)
                    print(searchResultJSON)
                    self.updateResultData(json: searchResultJSON)
                    self.favoriteButtonState = [Int](repeating: 0, count: self.searchListData.count)
                 
                    self.checkFavoriteState()
                }
                else{
                    self.noResult = true
                }
                
                if self.searchListData.count == 0{
                    self.noResult = true
                }
                else{
                    self.noResult = false
                }
                
                //format table
                self.searchListTable.rowHeight = UITableView.automaticDimension
                self.searchListTable.estimatedRowHeight = 200.0
                self.searchListTable.reloadData()
                
                //decide to show table or no results
                if self.noResult == true{
                    self.searchListTable.alpha = 0
                    self.noResultContainer.alpha = 1
                }
                else{
                    self.searchListTable.alpha = 1
                    self.noResultContainer.alpha = 0
                    
                }
                
            }
            else {
                
                self.noResult = true
                //decide to show table or no results
                if self.noResult == true{
                    self.searchListTable.alpha = 0
                    self.noResultContainer.alpha = 1
                }
                else{
                    self.searchListTable.alpha = 1
                    self.noResultContainer.alpha = 0
                    
                }
                print("Error")
            }
            
            SwiftSpinner.hide()
        }
        
    }
    
    /**
     * function to parse json data
    **/
    func updateResultData(json: JSON){
        
        var name:String!
        var id:String!
        var venueName:String!
        var url:String!
        var date:String!
        var time:String!
        var category:String!
        
        if let events = json["_embedded"]["events"].array{
            for event in events{
                
                //name
                name = event["name"].stringValue
                if name == ""{
                    name = "N/A"
                }
                
                //id
                id = event["id"].string
                if id == ""{
                    id = "N/A"
                }
                
                //venueName
                venueName = event["_embedded"]["venues"][0]["name"].stringValue
                if venueName == ""{
                    venueName = "N/A"
                }
                
                //url
                url = event["images"][0]["url"].stringValue
                if url == ""{
                    url = "N/A"
                }
                
                //date
                date = event["dates"]["start"]["localDate"].stringValue
                time = event["dates"]["start"]["localTime"].stringValue
                
                //category
                category = event["classifications"][0]["segment"]["name"].stringValue
                if category == ""{
                    category = "N/A"
                }
                
                let temp = Event(name:name,id:id!, url:url, venueName:venueName , date: date + " " + time, category: category)
                
                self.searchListData.append(temp)
            }
            //print(self.searchListData)
        }
        else {
            print("no results")
        }
        
    }
    /*check favorite state*/
    func checkFavoriteState(){
        for i in 0..<self.searchListData.count{
            let eventKey = self.searchListData[i].id
            let state = isKeyPresentInUserDefaults(key: eventKey + "eventID@hw9")
            if state == true{
                self.favoriteButtonState[i] = 1
            }
            else{
                self.favoriteButtonState[i] = 0
            }
        }
    }
    
    /*check whether in user defaults*/
    func isKeyPresentInUserDefaults(key: String) -> Bool {
        return UserDefaults.standard.object(forKey: key) != nil
    }

    /**
     *  Table delegate and datasource functions
     **/
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return self.searchListData.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "searchListTableCell", for: indexPath) as! searchListTableCell
        
        //print(self.searchListData.count)
        if(self.searchListData.count != 0){
            //name venueName date label
            cell.eventTitle?.text = self.searchListData[indexPath.row].name
            cell.eventVenue?.text = self.searchListData[indexPath.row].venueName
            cell.eventDate?.text = self.searchListData[indexPath.row].date
            
            //category image
            if self.searchListData[indexPath.row].category != "N/A"{
                let image:UIImage =  UIImage(named:self.searchListData[indexPath.row].category)!
                cell.eventImage.image = image
            }
            
            //favorite image
            if self.favoriteButtonState[indexPath.row] == 0{
                cell.eventFavoriteButton.setImage(UIImage(named: "favorite-empty"), for:.normal)
            }
            else{
                cell.eventFavoriteButton.setImage(UIImage(named: "favorite-filled"), for:.normal)
            }
            cell.eventFavoriteButton.tag = indexPath.row
            cell.eventFavoriteButton.addTarget(self, action:#selector(self.favoriteButtonTapped(_:)), for: .touchUpInside)
            
        }
        return cell
    }
    
    // method to run when table view cell is tapped
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        // Segue to the second view controller
        self.eventID = self.searchListData[indexPath.row].id
        self.dataSent["name"] = self.searchListData[indexPath.row].name
        self.dataSent["category"] = self.searchListData[indexPath.row].category
        self.dataSent["date"] = self.searchListData[indexPath.row].date
        self.dataSent["url"] = self.searchListData[indexPath.row].url
        self.dataSent["venueName"] = self.searchListData[indexPath.row].venueName
        self.dataSent["id"] = self.searchListData[indexPath.row].id


        self.performSegue(withIdentifier: "GoToDetailVC", sender: self)
    }
    
    /**
     * favortie buttion action
     **/
    
    @IBAction func favoriteButtonTapped(_ sender: UIButton) {
       
        let myIndexPath = NSIndexPath(row: sender.tag, section: 0)
        
        let cell = self.searchListTable.cellForRow(at: myIndexPath as IndexPath) as! searchListTableCell
        
        if self.favoriteButtonState[sender.tag] == 0 {
           
            self.favoriteButtonState[sender.tag] = 1
            cell.showToast(cell.eventTitle.text! + "was added to favorites", position: .bottom, popTime: 100, dismissOnTap: true)
           
            //add event to userdefaults
            let defaults = UserDefaults.standard
            let eventID:String = self.searchListData[sender.tag].id
            defaults.set(try? PropertyListEncoder().encode(self.searchListData[sender.tag]), forKey: eventID + "eventID@hw9")
        }
        else {
            self.favoriteButtonState[sender.tag] = 0
            cell.showToast(cell.eventTitle.text! + "was removed from favorites", position: .bottom, popTime: 100, dismissOnTap: true)
            
            //remove event to userdefaults
            let defaults = UserDefaults.standard
            let eventID:String = self.searchListData[sender.tag].id
            defaults.removeObject(forKey: eventID + "eventID@hw9")
        }
        self.searchListTable.reloadRows(at: [myIndexPath as IndexPath], with: UITableView.RowAnimation.none)
    }
    
    // This function is called before the segue
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        
        if segue.identifier == "GoToDetailVC"{
            // get a reference to the second view controller
            let detailVC = segue.destination as! tabVC
            detailVC.id = self.eventID
            detailVC.dataReceived = self.dataSent
            detailVC.delgate1 = self
            print("go")
            // set a variable in the second view controller with the data to pass
        }
    }
    
    func reloadSearchList() {
        
        self.checkFavoriteState()
        self.searchListTable.reloadData()
    }
    
}
