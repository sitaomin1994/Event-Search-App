//
//  favoriteVC.swift
//  hw9
//
//  Created by apple on 11/26/18.
//  Copyright © 2018 apple. All rights reserved.
//

import Foundation
import UIKit
import SwipeCellKit

class FavoriteVC:UIViewController,UITableViewDelegate,UITableViewDataSource,SwipeTableViewCellDelegate,reloadSearchListDelegate{
    
    //MARK: property
    
    @IBOutlet weak var noFavorite: UIView!
    @IBOutlet weak var favoriteTable: UITableView!
    var favoriteEvents:[Event]!
    var eventID:String!
    var dataSent:Dictionary<String,String>! = [:]
    var noRecordSign:Bool = true
    
    //MARK: action
    override func viewDidLoad() {
       
        super.viewDidLoad()
        
        self.favoriteEvents = []
        self.favoriteTable.delegate = self
        self.favoriteTable.dataSource = self
        self.favoriteTable.rowHeight = UITableView.automaticDimension
        self.favoriteTable.estimatedRowHeight = 200.0
        
        //UserDefaults.standard.removeObject(forKey: "Age")
        let defaults = UserDefaults.standard
        for (key, _) in defaults.dictionaryRepresentation() {
            if key.contains("eventID@hw9"){
                if let data = UserDefaults.standard.value(forKey:key) as? Data {
                    let event = try? PropertyListDecoder().decode(Event.self, from: data)
                    print(event as Any)
                    self.favoriteEvents.append(event!)
                }
            }
        }
        
        if self.favoriteEvents.count == 0{
            self.favoriteTable.alpha = 0
            self.noFavorite.alpha = 1
            //print("no favorite")
            noRecordSign = true
        }
        else{
            self.favoriteTable.alpha = 1
            self.noFavorite.alpha = 0
            noRecordSign = false
            self.favoriteTable.reloadData()
        }
        
    }
    
    /***Table Action****/
    //table action
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return self.favoriteEvents.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "favoriteCell", for: indexPath) as! favoriteCell
        
        if(self.favoriteEvents.count != 0){
            cell.title.text = self.favoriteEvents[indexPath.row].name
            cell.venue.text = self.favoriteEvents[indexPath.row].venueName
            cell.date.text = self.favoriteEvents[indexPath.row].date
            let image:UIImage =  UIImage(named:self.favoriteEvents[indexPath.row].category)!
            cell.favoriteImage.image = image
        }
        
        cell.delegate = self
        
        
        return cell
    }
    
    // method to run when table view cell is tapped
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        // Segue to the second view controller
        print(self.favoriteEvents)
        print(indexPath.row)
        self.eventID = self.favoriteEvents[indexPath.row].id
        print(self.eventID)
        self.dataSent["name"] = self.favoriteEvents[indexPath.row].name
        self.dataSent["category"] = self.favoriteEvents[indexPath.row].category
        self.dataSent["date"] = self.favoriteEvents[indexPath.row].date
        self.dataSent["url"] = self.favoriteEvents[indexPath.row].url
        self.dataSent["venueName"] = self.favoriteEvents[indexPath.row].venueName
        self.dataSent["id"] = self.favoriteEvents[indexPath.row].id
        
        
        self.performSegue(withIdentifier: "favoriteToTabVC", sender: self)
    }
    
    //swipe delete
    func tableView(_ tableView: UITableView, editActionsForRowAt indexPath: IndexPath, for orientation: SwipeActionsOrientation) -> [SwipeAction]? {
        
        guard orientation == .right else { return nil }
        
        let deleteAction = SwipeAction(style: .destructive, title: "Delete") { action, indexPath in
            
            //remove event to userdefaults
            self.view.showToast(self.favoriteEvents[indexPath.row].name + "was removed from favorites", position: .bottom, popTime: 100, dismissOnTap: true)
            let defaults = UserDefaults.standard
            let eventID:String = self.favoriteEvents[indexPath.row].id
            defaults.removeObject(forKey: eventID + "eventID@hw9")
            self.favoriteEvents.remove(at: indexPath.row)

            //check no record
            if self.favoriteEvents.count == 0{
                self.noRecordSign = true
            }
            else{
                self.noRecordSign = false
            }
            self.showNoRecordCheck()
            //reload data
            self.favoriteTable.reloadData()
        }
        
        // customize the action appearance
        deleteAction.image = UIImage(named: "delete")
        
        return [deleteAction]
    }
    
    /***segue go to tabvc****/
    //prepare for segue
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        
        if segue.identifier == "favoriteToTabVC"{
            let detailVC = segue.destination as! tabVC
            detailVC.id = self.eventID
            detailVC.dataReceived = self.dataSent
            detailVC.delgate1 = self
            print(detailVC.id)
            print(detailVC.dataReceived)
            print("go")
            // set a variable in the second view controller with the data to pass
        }
    }
    
    
    /*reload data delegate*/
    //delegate of tabvc
    func reloadSearchList() {
        self.viewDidLoad()
    }
    
    /*no record sign*/
    func showNoRecordCheck(){
        if self.noRecordSign == true{
            self.favoriteTable.alpha = 0
            self.noFavorite.alpha = 1
        }
        else{
            self.favoriteTable.alpha = 1
            self.noFavorite.alpha = 0
        }
    }
}
