//
//  autoTableVC.swift
//  hw9
//
//  Created by apple on 11/28/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import Alamofire
import SwiftyJSON

protocol autoDelegate {
    
    func autoSelected(selectedText:String)
}


class autoTableVC: UIViewController, UITableViewDelegate,UITableViewDataSource{
    
    @IBOutlet weak var autoTable: UITableView!
    var autoDelegate:autoDelegate?
    var autoResult:[String]! = []
    var searchText:String! = ""
    var noRecord:Bool = true
    
    override func viewDidLoad() {
        super.viewDidLoad()
        autoTable.layer.borderColor = UIColor.black.cgColor
        autoTable.dataSource = self
        autoTable.delegate = self
        
        // Do any additional setup after loading the view.
    }
    
    func loadAutoSearch(searchText:String){
        self.autoResult = []
        self.autoTable.reloadData()
        self.searchText = searchText
        let url = "http://sitaomin571hw8.us-east-2.elasticbeanstalk.com/auto"
        let params = ["Keywords":searchText]
        
        Alamofire.request(url, method: .get, parameters: params, encoding: URLEncoding.default).responseJSON {
            
            response in
            
            if response.result.isSuccess {
                
                if response.result.value != nil{
                    let result =  JSON(response.result.value as Any)
                    for ele in result.arrayValue{
                        self.autoResult.append(ele.stringValue)
                    }
                    
                    if self.autoResult.count == 0{
                        self.noRecord = true
                    }
                    else{
                        self.noRecord = false
                    }
                    
                    self.autoTable.reloadData()
                }
                else{
                    self.noRecord = false
                }
            }
            else {
                print("Error")
                self.noRecord = true
            }
        }
    }
    
    
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if self.noRecord == true{
            return 1
        }
        else{
            return self.autoResult.count
        }
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = self.autoTable.dequeueReusableCell(withIdentifier: "autoTableCell", for:indexPath) as! autoTableCell
        if noRecord == false{
                cell.autoEle.text = self.autoResult[indexPath.row]
        }
        else{
                cell.autoEle.text = ""
        }
        
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        if noRecord == false{
            self.autoDelegate?.autoSelected(selectedText: self.autoResult[indexPath.row])
        }
    }
    

    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destination.
        // Pass the selected object to the new view controller.
    }
    */

}
