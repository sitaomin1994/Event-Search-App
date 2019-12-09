//
//  artist.swift
//  hw9
//
//  Created by apple on 11/24/18.
//  Copyright © 2018 apple. All rights reserved.
//

import Foundation

struct Artist{
    
    var name:String!
    var follower:String!
    var popularity:Int!
    var photo:Array<String>!
    var url:String!
    var hasArtistInfo:Bool!
    var hasArtistPhoto:Bool!
    
    init(name:String, follower:String, popularity:Int, url:String, photo:[String],hasArtistInfo:Bool,hasArtistPhoto:Bool){
        self.name = name
        self.follower = follower
        self.popularity = popularity
        self.url = url
        self.photo = photo
        self.hasArtistInfo = hasArtistInfo
        self.hasArtistPhoto = hasArtistPhoto
    }

}
