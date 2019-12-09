//
//  eventArtistVC.swift
//  hw9
//
//  Created by apple on 11/20/18.
//  Copyright © 2018 apple. All rights reserved.
//

import UIKit
import SwiftyJSON
import Kingfisher
import SwiftSpinner

class eventArtistVC:UIViewController,UICollectionViewDelegate, UICollectionViewDataSource{
    
    //MARK: property
    @IBOutlet weak var collectionVC: UICollectionView!
    
    @IBOutlet weak var map: UIImageView!
    var artistData:JSON?
    var artists:[Artist] = []
    var hasArtistInfo:Bool = false
    
    @IBOutlet weak var tabBar: UITabBarItem!
    
    //MARK: action
    override func viewDidLoad() {
        super.viewDidLoad()
        
        SwiftSpinner.show("showing artist info...")
        
        if(self.artistData == nil || self.artistData?.arrayValue.count == 0 ){
            print("no artist record")
        }
        else{
            updateArtistData(json: self.artistData!)
        }
        
        collectionVC.delegate = self
        collectionVC.dataSource = self
        collectionVC.reloadData()
        
        SwiftSpinner.hide()
        
    }
    
    func updateArtistData(json:JSON){
        
        var length = 2
        if json.arrayValue.count < 2{
            length = json.arrayValue.count
        }
        else{
            length = 2
        }
        
        for i in 0..<length{
            
            print(json)
            
            //name
            var name:String! = json[i]["artistName"].stringValue
            if name == ""{
                name = "N/A"
            }
            
            /*artist info*/
            var followers:Int! = 0
            var popularity:Int! = 0
            var url:String! = "N/A"
            let artistInfo:Dictionary<String,JSON> = json[i]["musicArtist"].dictionaryValue
            var hasArtistInfo:Bool!
            
            //if has no key
            if artistInfo.isEmpty == false{
                
                //artist data
                let musicArtistData:Array<JSON> = json[i]["musicArtist"]["artists"]["items"].arrayValue
                
                //if artist number is 0
                print(musicArtistData.count)
                
                if musicArtistData.count == 0{
                    hasArtistInfo = false
                }
                else{
                    hasArtistInfo = false
                    
                    for artist in musicArtistData{
                        print(artist["name"].stringValue)
                        print(name!)
                        if(artist["name"].stringValue == name!){
                            followers = artist["followers"]["total"].intValue
                            popularity = artist["popularity"].intValue
                            url = artist["external_urls"]["spotify"].stringValue
                            hasArtistInfo = true
                            self.hasArtistInfo = true
                            break;
                        }
                    }
                }
            }
            else{
                hasArtistInfo = false
            }
            
            
            /*artist photo*/
            var photo:[String]! = [String]()
            var hasArtistPhoto:Bool!
            let artistPhoto:Dictionary<String,JSON> = json[i]["artistPhoto"].dictionaryValue
            if artistPhoto.isEmpty == true{
                hasArtistPhoto = false
            }
            else{
                hasArtistPhoto = true
                let Photo:Array = artistPhoto["items"]!.arrayValue
                if Photo.count != 0 {
                    for ele in Photo{
                        photo.append(ele["link"].stringValue)
                    }
                }
                else{
                    hasArtistPhoto = false
                }
            }
            
            
            let artist = Artist(name:name, follower: followers.formattedWithSeparator, popularity: popularity, url: url, photo: photo, hasArtistInfo: hasArtistInfo, hasArtistPhoto: hasArtistPhoto)
            
            print(artist)
            
            self.artists.append(artist)
        }
        
        //print(self.artists)
    }
    
    /*collection view function*/
    func numberOfSections(in collectionView: UICollectionView) -> Int {
        return self.artists.count
    }
    
    func collectionView(_ collectionView: UICollectionView, numberOfItemsInSection section: Int) -> Int {
        if self.hasArtistInfo == true{
            return 9
        }
        else{
            return 8
        }
    }
    
    func collectionView(_ collectionView: UICollectionView, cellForItemAt indexPath: IndexPath) -> UICollectionViewCell {
        if self.hasArtistInfo == true{
            if indexPath.row == 0{
                let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "artistCell", for: indexPath) as! artistCell
                //add artist info
                cell.address.text = self.artists[indexPath.section].name
                cell.follower.text = self.artists[indexPath.section].follower
                cell.popularity.text = String(self.artists[indexPath.section].popularity)
                
                if self.artists[indexPath.section].url != ""{
                    //add spotify url
                    let attributedString = NSMutableAttributedString(string: "Spotify")
                    let url = URL(string:self.artists[indexPath.section].url)!
                
                    // Set the 'check at' substring to be the link
                    attributedString.setAttributes([.link: url], range: NSMakeRange(0, 7))
                
                    cell.checkAt.attributedText = attributedString
                    cell.checkAt.isUserInteractionEnabled = true
                    cell.checkAt.isEditable = false
                
                    // Set how links should appear: blue and underlined
                    cell.checkAt.linkTextAttributes = [
                        .foregroundColor: UIColor.blue,
                        .underlineStyle: NSUnderlineStyle.single.rawValue
                    ]
                }
                else{
                    cell.checkAt.text = "N/A"
                }
                
                
                return cell
            }
            else{
                let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "photoCell", for: indexPath) as! photoCell
            
                let url = URL(string:self.artists[indexPath.section].photo[indexPath.row-1])!
                cell.artistPhoto.kf.setImage(with: url)
    
                return cell
            }
        }
        else{
            let cell = collectionView.dequeueReusableCell(withReuseIdentifier: "photoCell", for: indexPath) as! photoCell
            
            let url = URL(string:self.artists[indexPath.section].photo[indexPath.row])!
            cell.artistPhoto.kf.setImage(with: url)
            
            return cell
        }
    }
    
    func collectionView(_ collectionView: UICollectionView, viewForSupplementaryElementOfKind kind: String, at indexPath: IndexPath) -> UICollectionReusableView {
        
        let artistHeader = collectionView.dequeueReusableSupplementaryView(ofKind:kind , withReuseIdentifier: "artistHeader", for: indexPath) as! artistHeader
        
         artistHeader.artistName.text = self.artists[indexPath.section].name
        
        return artistHeader
    }
    
}
/**
extension Formatter {
    static let withSeparator: NumberFormatter = {
        let formatter = NumberFormatter()
        formatter.groupingSeparator = ","
        formatter.numberStyle = .decimal
        return formatter
    }()
}

extension BinaryInteger {
    var formattedWithSeparator: String {
        return Formatter.withSeparator.string(for: self) ?? ""
    }
}
**/

