//
//  searchFormVC.swift
//  hw9
//
//  Created by apple on 11/11/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import Alamofire
import SwiftyJSON
import CoreLocation
import McPicker
import EasyToast

class searchFormVC: UIViewController,CLLocationManagerDelegate,UIPickerViewDelegate,UIPickerViewDataSource,autoDelegate,  UITextFieldDelegate  {
    
    @IBOutlet weak var autoTable: UIView!
    //MARK: Properties
    var keywordsText:String!
    var categoryText:String!
    var distanceText:String!
    var currentLocationText:String!
    var currentLocationText_object:Dictionary<String,String>!
    var unitsText:String! = "miles"
    var otherLocationText_pass:String!
    
    
    
    let locationManager = CLLocationManager()
    var lat:String = ""
    var long:String = ""
    var locationSign:String = "current"
    var keywordEmpty:Bool = true
    var otherLocationEmpty:Bool = true
    var unitsData: [String] = [String]()
    
    @IBOutlet weak var NavBar_placeSearch: UINavigationBar!
    
    @IBOutlet weak var favoriteListContainer: UIView!
    var favoriteVC:FavoriteVC!
    var autoVC:autoTableVC!
    
    @IBOutlet weak var form: UIStackView!
    
    @IBOutlet weak var searchAndFavorite: UISegmentedControl!
    
    @IBOutlet weak var keywords: UITextField!
    
    @IBOutlet weak var category: McTextField!
    
    @IBOutlet weak var distance: UITextField!
    
    @IBOutlet weak var unitsPicker: UIPickerView!
    
    @IBOutlet weak var currentLocation: UIButton!
    @IBOutlet weak var otherLocation: UIButton!
    @IBOutlet weak var otherLocationText: UITextField!
    
    @IBOutlet weak var search: UIButton!
    @IBOutlet weak var clear: UIButton!
    
    //var auto = UIView(frame: CGRect(x:10, y: 220, width: 355, height: 175))
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        autoTable.layer.borderColor = UIColor.black.cgColor
        autoTable.layer.borderWidth = 0.3
        self.view.bringSubviewToFront(autoTable)
        self.autoTable.alpha = 0
        
        //let defaults = UserDefaults.standard
        let filePath = FileManager.default.urls(for: .documentDirectory, in: .userDomainMask).first
        print(filePath!)
        
        //keywords
        self.keywords.text = ""
        self.keywords.delegate = self
        keywordEmpty = false
        keywords.addTarget(self, action: #selector(autoSearch), for: UIControl.Event.editingChanged)
        keywords.addTarget(self, action: #selector(dismissAuto), for: UIControl.Event.editingDidEnd)
        
        //category
        category.text = "All"
        let categoryData: [[String]] = [["All","Music","Sports","Arts & Theatre","File","Miscellaneous"]]
        let mcInputView = McPicker(data: categoryData)
        category.inputViewMcPicker = mcInputView
        
        category.doneHandler = { [weak category] (selections) in
            category?.text = selections[0]!
        }
        category.cancelHandler = { [weak category] in
            category?.text = "All"
        }
    
        //distance
        distance.text = "10"
        
        //units
        self.unitsPicker.delegate = self
        self.unitsPicker.dataSource = self
        unitsData = ["miles","kms"]
        
        otherLocationText.isEnabled = false
        otherLocationText.text = ""

        //search and clear
        search.isEnabled = false

        // get current location
        locationManager.delegate = self
        locationManager.desiredAccuracy = kCLLocationAccuracyHundredMeters
        locationManager.requestWhenInUseAuthorization()
        locationManager.startUpdatingLocation()
        
        
    }
    
    //MARK: Actions
    
    //auto complete
    @objc func autoSearch(){
        //print(self.keywords.text!)
        print("a" + self.keywords.text! + "a")
        let keywordsText = self.keywords.text
        let searchText = deleteEmptySpace(input: self.keywords.text!)
        print("a" + searchText + "a")
        
        if keywordsText != ""{
            if keywordsText?.last == " " && searchText != ""{
                self.autoVC.loadAutoSearch(searchText:searchText)
                self.autoTable.alpha = 1
            }
        }
        else{
            self.autoTable.alpha = 0
        }
        
        if searchText == ""{
            self.autoTable.alpha = 0
        }
    }
    
    func autoSelected(selectedText:String) {
        self.keywords.text = selectedText
        self.autoTable.alpha = 0
    }
    
    @objc func dismissAuto(){
        self.autoTable.alpha = 0
    }
    
    func textFieldShouldReturn(_ scoreText: UITextField) -> Bool {
        self.view.endEditing(true)
        return false
    }
    
    // SEGMENT
    @IBAction func searchFavoriteSegmentTapped(_ sender: UISegmentedControl) {
        
        if sender.selectedSegmentIndex == 0 {
            UIView.animate(withDuration: 0.5, animations: {
                self.form.alpha = 1
                self.favoriteListContainer.alpha = 0
            })
        } else {
            UIView.animate(withDuration: 0.5, animations: {
                self.form.alpha = 0
                self.favoriteListContainer.alpha = 1
                self.favoriteVC.viewDidLoad()
            })
        }
    
    }
    
    
    
    
    /*units picker delegate*/
    func numberOfComponents(in pickerView: UIPickerView) -> Int {
        return 1
    }
    
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        return unitsData.count
    }
    
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String? {
        return unitsData[row]
    }
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
        unitsText = unitsData[row]
    }
    func pickerView(_ pickerView: UIPickerView, viewForRow row: Int, forComponent component: Int, reusing view: UIView?) -> UIView {
        var pickerLabel: UILabel? = (view as? UILabel)
        if pickerLabel == nil {
            pickerLabel = UILabel()
            pickerLabel?.font = UIFont(name: "Arial", size:13)
            pickerLabel?.textAlignment = .center
        }
        pickerLabel?.text = unitsData[row]
        
        return pickerLabel!
    }
    
    
    /*current and other location radio
     ----------------------------------------------------------------------*/
    @IBAction func currentLocationTapped(_ sender: Any) {
        if(locationSign == "current"){
            print("current")
        }
        else{
            currentLocation.backgroundColor = UIColor.gray
            otherLocation.backgroundColor = UIColor.white
            locationSign = "current"
            otherLocationText.isEnabled = false
            otherLocationText.text = ""
            otherLocationText.placeholder = "Type in the location"

        }
    }
    
    @IBAction func otherLocationTapped(_ sender: Any) {
        
        if(locationSign == "current"){
            currentLocation.backgroundColor = UIColor.white
            otherLocation.backgroundColor = UIColor.gray
            locationSign = "other"
            otherLocationText.isEnabled = true
        }
    }
    
    
    /* override location manager delegete functions
     ----------------------------------------------------------------------*/
    //Write the didUpdateLocations method:
    func locationManager(_ manager: CLLocationManager, didUpdateLocations locations: [CLLocation]) {
        let location = locations[locations.count - 1]
        if location.horizontalAccuracy > 0 {
            
            self.locationManager.stopUpdatingLocation()
            
            print("longitude = \(location.coordinate.longitude), latitude = \(location.coordinate.latitude)")
            
            self.lat = String(location.coordinate.latitude)
            self.long = String(location.coordinate.longitude)
            self.search.isEnabled = true
        }
    }
    
    
    //Write the didFailWithError method:
    func locationManager(_ manager: CLLocationManager, didFailWithError error: Error) {
        print(error)
        print("fail to get location!")
    }
    
    
    //clear button tapped
    @IBAction func clearTapped(_ sender: UIButton) {
        
        //reset keywords category distance
        self.category.text = "All"
        self.distance.text = "10"
        self.keywords.text = ""
        self.keywords.placeholder = "Enter keyword"
        self.distance.placeholder = "10"
        
        //unit picker
        self.unitsPicker.selectRow(0, inComponent: 0, animated: false)
        
        //reset radio and location
        self.currentLocation.backgroundColor = UIColor.gray
        self.otherLocation.backgroundColor = UIColor.white
        self.locationSign = "current"
        self.otherLocationText.isEnabled = false
        self.otherLocationText.placeholder = "Type in the location"
        self.otherLocationText.text = ""
    }
    
    
    /*Write the PrepareForSegue Method
    -------------------------------------------------------------------------*/
    //check keyword and location empty and show toast
    override func shouldPerformSegue(withIdentifier identifier: String, sender: Any?) -> Bool {
        
        if identifier == "GoToSearchResult" {
            
            let keywordTrimmed = deleteEmptySpace(input: self.keywords.text!)
            let otherLocatonTrimmed = deleteEmptySpace(input: self.otherLocationText.text!)
            
            if keywordTrimmed == "" || (locationSign == "other" && otherLocatonTrimmed == ""){
                self.view.showToast("Keyword and Location are mandatary field", position: .bottom, popTime: 100, dismissOnTap: true)
                return false
            }
            else{
                return true
            }
        }
        else{
            return true
        }
    }
    
    
    
    //prepare for segue
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        
        if segue.identifier == "GoToSearchResult" {
            
            let destinationVC = segue.destination as! eventSearchListVC
            
            // pass params
            keywordsText = keywords.text
            categoryText = category.text
            distanceText = distance.text
        
            if(locationSign == "current"){
                currentLocationText_object = ["lat":String(lat),"long":String(long)]
                otherLocationText_pass = ""
                destinationVC.params = ["Keywords":keywordsText,"Category":categoryText,"Distance":distanceText,"DistanceUnits":unitsText,
                     "currentLocation":currentLocationText_object,"otherlocation":otherLocationText_pass] as Dictionary<String, AnyObject>
            }
            else{
                currentLocationText = ""
                otherLocationText_pass = otherLocationText.text
                destinationVC.params = ["Keywords":keywordsText,"Category":categoryText,"Distance":distanceText,"DistanceUnits":unitsText,
                                        "currentLocation":currentLocationText,"otherlocation":otherLocationText_pass] as Dictionary<String, AnyObject>
            }
            
            print(keywordsText)
            print(categoryText)
            print(distanceText)
            print(unitsText)
            print(currentLocationText)
            print(otherLocationText_pass)
            
        
            //destinationVC.delegate = self
            
        }
        
        
        if segue.identifier == "gotofavorite"{
            
            let vc = segue.destination as? FavoriteVC
            self.favoriteVC = vc
            
        }
        
        if segue.identifier == "autoComplete"{
            let autovc = segue.destination as? autoTableVC
            self.autoVC = autovc
            self.autoVC.autoDelegate = self
        }
        
    }
    
    //handle string empty space
    func deleteEmptySpace(input:String) -> String{
        let trimmedString = input.trimmingCharacters(in: .whitespaces)
        return trimmedString
    }
    
}

